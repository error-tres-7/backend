<?php
// Applies database/schema.sql. Railway runs it as the pre-deploy command (railway.json).

require_once __DIR__ . '/../src/conexion.php';

$conexion = conexion();
$conexion->multi_query(file_get_contents(__DIR__ . '/../database/schema.sql'));

do {
    if ($resultado = $conexion->store_result()) {
        $resultado->free();
    }
} while ($conexion->more_results() && $conexion->next_result());

echo "schema applied\n";
