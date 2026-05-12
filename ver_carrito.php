<?php
session_start();
require_once 'includes/conexion.php';

$total_compra = 0;

if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])) {
    foreach ($_SESSION['carrito'] as $id => $cantidad) {
        $res = $conexion->query("SELECT nombre, precio FROM productos WHERE id = $id");
        $p = $res->fetch_assoc();
        
        $subtotal = $p['precio'] * $cantidad;
        $total_compra += $subtotal; // Sumamos al total general

        echo "Producto: " . $p['nombre'] . " x " . $cantidad . " = $" . number_format($subtotal, 2) . "<br>";
    }
    echo "<h3>TOTAL A PAGAR: $" . number_format($total_compra, 2) . "</h3>";
    echo "<a href='index_principal.php'>Seguir comprando</a> | <a href='vaciar_carrito.php'>Vaciar Carrito</a>";
} else {
    echo "Carrito vacío. <a href='index_principal.php'>Volver</a>";
}
?>