<?php
// Asegúrate de que la ruta al archivo de la clase sea correcta
// Si estás en public/index.php, la ruta al modelo suele ser:
require_once "../models/Producto.php"; 
require_once "../includes/conexion.php";

// Instanciamos el MODELO (la clase Producto)
// Nota: Le cambié el nombre de la variable de $controller a $modelo para no confundir
$modelo = new Producto($conexion);

// USAMOS EL NOMBRE EXACTO DE LA FUNCIÓN QUE ESTÁ EN TU CLASE
$productos = $modelo->obtenerTodos(); 

echo "<h1>Catálogo de Productos</h1>";
echo "<ul>";
foreach ($productos as $producto) {
    echo "<li>";
    // Asegúrate de que las columnas 'nombre' y 'precio' existen en tu base de datos
    echo "<strong>" . $producto['nombre'] . "</strong> - $" . $producto['precio'];
    echo "</li>";
}
echo "</ul>";
?>