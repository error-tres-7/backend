<?php

/** Answers with JSON and stops. Errors follow the frontend contract: `{ message, errors? }`. */
function responder(int $estado, array $cuerpo): never
{
    http_response_code($estado);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($cuerpo, JSON_UNESCAPED_UNICODE);
    exit();
}

/** Accepts a JSON body or a classic form post. */
function entrada(): array
{
    $tipo = $_SERVER['CONTENT_TYPE'] ?? '';

    if (str_contains($tipo, 'application/json')) {
        $datos = json_decode(file_get_contents('php://input') ?: '', true);
        return is_array($datos) ? $datos : [];
    }

    return $_POST;
}

/** Only origins listed in ALLOWED_ORIGINS may call with credentials (session cookie). */
function cors(): void
{
    $permitidos = array_filter(array_map('trim', explode(',', getenv('ALLOWED_ORIGINS') ?: '')));
    $origen = $_SERVER['HTTP_ORIGIN'] ?? '';

    if ($origen !== '' && in_array($origen, $permitidos, true)) {
        header("Access-Control-Allow-Origin: $origen");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Accept, Idempotency-Key');
        header('Access-Control-Max-Age: 600');
        header('Vary: Origin');
    }

    if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
        http_response_code(204);
        exit();
    }
}

/** Session cookie shared across *.entrepuestos.com so the frontend and api subdomain are same-site. */
function iniciar_sesion(): void
{
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => getenv('SESSION_DOMAIN') ?: '',
        'secure' => getenv('SESSION_SECURE') !== 'false',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}
