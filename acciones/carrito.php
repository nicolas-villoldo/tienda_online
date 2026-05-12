<?php
session_start();
// Asegurate que la ruta a conexion.php sea la correcta según lo que arreglamos
require_once __DIR__ . '/../includes/conexion.php';

if (isset($_POST['id'])) {
    $id = intval($_POST['id']);

    // 1. Buscamos los datos reales en la base de datos
    $consulta = $conexion->query("SELECT nombre, precio, stock FROM productos WHERE id = $id");
    $producto_db = $consulta->fetch_assoc();

    if ($producto_db) {
        // 2. Revisamos cuántos hay YA en el carrito (buscando dentro del sub-array)
        $cantidad_en_carrito = isset($_SESSION['carrito'][$id]['cantidad']) ? $_SESSION['carrito'][$id]['cantidad'] : 0;
        $nueva_cantidad = $cantidad_en_carrito + 1;

        // 3. Validamos contra el stock de la DB
        if ($nueva_cantidad <= $producto_db['stock']) {
            
            // EL CAMBIO CLAVE: Guardamos un array con toda la info del producto
            // Esto es lo que soluciona los errores de "offset on int" en el index
            $_SESSION['carrito'][$id] = [
                'id'       => $id,
                'cantidad' => $nueva_cantidad,
                'nombre'   => $producto_db['nombre'],
                'precio'   => $producto_db['precio']
            ];
            
            // Redirigimos al index principal (un nivel arriba)
            header("Location: ../index_principal.php?status=ok");
        } else {
            // Si no hay stock suficiente
            header("Location: ../index_principal.php?status=sin_stock");
        }
    } else {
        // Si el ID del producto no existe en la base de datos
        header("Location: ../index_principal.php?status=error_producto");
    }
    exit();
} else {
    // Si alguien entra al archivo sin mandar un ID
    header("Location: ../index_principal.php");
    exit();
}
?>