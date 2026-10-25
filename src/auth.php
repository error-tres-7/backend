<?php
// Ported from the PHP that lived in the frontend repo (php/*.php): same validation and queries,
// but answering JSON instead of redirecting to HTML pages that the SvelteKit app does not have.

function registro(): never
{
    $datos = entrada();
    $nombre = trim((string) ($datos['nombre'] ?? ''));
    $correo = trim((string) ($datos['correo'] ?? ''));
    $contrasena = (string) ($datos['contraseña'] ?? $datos['contrasena'] ?? '');
    $rol = $datos['rol'] ?? 'cliente';

    if ($nombre === '' || !filter_var($correo, FILTER_VALIDATE_EMAIL) || strlen($contrasena) < 8) {
        responder(422, ['message' => 'datos', 'errors' => ['nombre, correo válido y contraseña de 8+ caracteres']]);
    }

    if (!in_array($rol, ['cliente', 'comerciante'], true)) {
        $rol = 'cliente';
    }

    $conexion = conexion();

    $verificar = $conexion->prepare('SELECT id_usuario FROM Usuarios WHERE correo = ?');
    $verificar->bind_param('s', $correo);
    $verificar->execute();
    $verificar->store_result();
    $existe = $verificar->num_rows > 0;
    $verificar->close();

    if ($existe) {
        responder(409, ['message' => 'correo']);
    }

    $hash = password_hash($contrasena, PASSWORD_DEFAULT);
    $registro = $conexion->prepare('INSERT INTO Usuarios (nombre, correo, contraseña, rol) VALUES (?, ?, ?, ?)');
    $registro->bind_param('ssss', $nombre, $correo, $hash, $rol);
    $registro->execute();
    $id = $registro->insert_id;
    $registro->close();

    responder(201, ['id_usuario' => $id, 'nombre' => $nombre, 'correo' => $correo, 'rol' => $rol]);
}

function login(): never
{
    $datos = entrada();
    $correo = trim((string) ($datos['correo'] ?? ''));
    $contrasena = (string) ($datos['contraseña'] ?? $datos['contrasena'] ?? '');

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL) || $contrasena === '') {
        responder(401, ['message' => 'credenciales']);
    }

    $consulta = conexion()->prepare('SELECT id_usuario, nombre, correo, contraseña, rol FROM Usuarios WHERE correo = ?');
    $consulta->bind_param('s', $correo);
    $consulta->execute();
    $usuario = $consulta->get_result()->fetch_assoc();
    $consulta->close();

    if (!$usuario || !password_verify($contrasena, $usuario['contraseña'])) {
        responder(401, ['message' => 'credenciales']);
    }

    session_regenerate_id(true);
    $_SESSION['id_usuario'] = $usuario['id_usuario'];
    $_SESSION['nombre'] = $usuario['nombre'];
    $_SESSION['correo'] = $usuario['correo'];
    $_SESSION['rol'] = $usuario['rol'];

    responder(200, sesion_actual());
}

function logout(): never
{
    $_SESSION = [];
    session_destroy();
    responder(200, ['message' => 'ok']);
}

function sesion_actual(): array
{
    return [
        'id_usuario' => $_SESSION['id_usuario'],
        'nombre' => $_SESSION['nombre'],
        'correo' => $_SESSION['correo'],
        'rol' => $_SESSION['rol'],
    ];
}

function yo(): never
{
    if (!isset($_SESSION['id_usuario'])) {
        responder(401, ['message' => 'sin-sesion']);
    }

    responder(200, sesion_actual());
}
