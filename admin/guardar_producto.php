<?php
require_once 'cerrojo.php';
require_once '../includes/conexion.php';
require_once '../models/productos.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $producto = new Producto($conexion);

    // Capturamos los datos
    $nombre = $_POST['nombre'] ?? '';
    $precio = $_POST['precio'] ?? 0;
    $stock = $_POST['stock'] ?? 0;
    $descripcion = $_POST['descripcion'] ?? '';
    
    // Gestión de imagen
    $nombre_imagen = "default.jpg";
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $nombre_archivo = time() . "_" . $_FILES['imagen']['name'];
        if (move_uploaded_file($_FILES['imagen']['tmp_name'], "../Imagen/" . $nombre_archivo)) {
            $nombre_imagen = $nombre_archivo;
        }
    }

    // --- CAMBIO CLAVE ACÁ ---
    // El orden debe ser: nombre, descripcion, precio, stock, imagen
    // para que coincida con tu función public function crear($nombre, $descripcion, $precio, $stock, $imagen)
    if ($producto->crear($nombre, $descripcion, $precio, $stock, $nombre_imagen)) {
        header("Location: index.php?msg=exito");
        exit(); // Siempre meté un exit después de un header Location
    } else {
        echo "Error: No se pudo guardar el producto.";
    }
}