<?php
// Front controller: Apache rewrites every request here (see Dockerfile).

require_once __DIR__ . '/../src/http.php';
require_once __DIR__ . '/../src/conexion.php';
require_once __DIR__ . '/../src/auth.php';

cors();

$metodo = $_SERVER['REQUEST_METHOD'];
$ruta = rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/', '/') ?: '/';

if ($ruta === '/health') {
    try {
        conexion()->query('SELECT 1');
        responder(200, ['status' => 'ok']);
    } catch (Throwable) {
        responder(503, ['status' => 'db-unavailable']);
    }
}

try {
    iniciar_sesion();

    match ("$metodo $ruta") {
        'POST /auth/register' => registro(),
        'POST /auth/login' => login(),
        'POST /auth/logout' => logout(),
        'GET /auth/me' => yo(),
        default => responder(404, ['message' => 'not-found']),
    };
} catch (Throwable $error) {
    error_log((string) $error);
    responder(500, ['message' => 'server-error']);
}
