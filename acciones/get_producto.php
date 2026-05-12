<?php
// 1. Permitir que Angular (que está en el puerto 4200) acceda a estos datos
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// 2. Conexión a tu base de datos (ajustá los datos si son distintos)
$servidor = "localhost";
$usuario = "root";
$password = "TuPasswordReal";
$base_datos = "tienda_online"; // <--- PONÉ EL NOMBRE DE TU BASE ACÁ

$conexion = new mysqli($servidor, $usuario, $password, $base_datos);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// 3. Consultar los productos
$sql = "SELECT id, nombre, precio FROM productos";
$resultado = $conexion->query($sql);

$productos = [];

if ($resultado->num_rows > 0) {
    while($fila = $resultado->fetch_assoc()) {
        $productos[] = $fila;
    }
}

// 4. Enviar los datos en formato JSON
echo json_encode($productos);

$conexion->close();
?>