<?php
// Railway's MySQL service exposes MYSQLHOST, MYSQLPORT, MYSQLUSER, MYSQLPASSWORD and MYSQLDATABASE.
// Reference them from this service's variables (see README).

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

function conexion(): mysqli
{
    static $conexion = null;

    if ($conexion === null) {
        $conexion = new mysqli(
            getenv('MYSQLHOST') ?: '127.0.0.1',
            getenv('MYSQLUSER') ?: 'root',
            getenv('MYSQLPASSWORD') ?: '',
            getenv('MYSQLDATABASE') ?: 'plataforma_mercados',
            (int) (getenv('MYSQLPORT') ?: 3306)
        );
        $conexion->set_charset('utf8mb4');
    }

    return $conexion;
}
