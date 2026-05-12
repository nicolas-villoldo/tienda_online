<?php
require_once 'cerrojo.php';
require_once '../includes/conexion.php';
require_once '../models/productos.php'; // Usamos la clase que ya tiene el método borrar

// Verificamos que venga un ID por la URL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    $objProducto = new Producto($conexion);
    
    // Antes de borrar, podríamos buscar el producto para saber el nombre de la imagen
    // y así borrar el archivo del servidor para no acumular basura.
    $p = $objProducto->obtenerPorId($id);
    
    if ($p) {
        // Borramos el registro en la base de datos
        if ($objProducto->borrar($id)) {
            
            // OPCIONAL: Borrar la imagen de la carpeta si no es la default
            if (!empty($p['imagen']) && $p['imagen'] !== 'default.jpg') {
                $rutaImagen = "../Imagen/" . $p['imagen'];
                if (file_exists($rutaImagen)) {
                    unlink($rutaImagen); // Elimina el archivo físico
                }
            }
            
            header("Location: index.php?msg=borrado");
            exit();
        } else {
            echo "Error al intentar borrar el producto.";
        }
    } else {
        header("Location: index.php");
        exit();
    }
} else {
    // Si alguien entra a borrar.php sin ID, lo mandamos al panel
    header("Location: index.php");
    exit();
}