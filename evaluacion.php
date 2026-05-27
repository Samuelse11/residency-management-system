<?php
function conectarBD() {
    $servidor = "";
    $usuario = "";
    $contrasena = "";
    $basedatos = "";

    
    $conexion = new mysqli($servidor, $usuario, $contrasena, $basedatos);

    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }

    $conexion->set_charset("utf8");
    return $conexion;
}
?>
