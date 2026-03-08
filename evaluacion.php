<?php
function conectarBD() {
    $servidor = "sql204.infinityfree.com";
    $usuario = "if0_40393242";
    $contrasena = "ZBCbazcIqTyh";
    $basedatos = "if0_40393242_residencia";

    
    $conexion = new mysqli($servidor, $usuario, $contrasena, $basedatos);

    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }

    $conexion->set_charset("utf8");
    return $conexion;
}
?>