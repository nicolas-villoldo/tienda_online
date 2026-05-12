<?php
require_once 'cerrojo.php'; 
require_once "../includes/conexion.php";
require_once "../models/pedido.php"; 

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Suponiendo que tenés un método eliminar en tu modelo Pedido
    // Si no lo tenés, podés usar una consulta directa:
    $query = "DELETE FROM pedidos WHERE id = $id";
    
    if (mysqli_query($conexion, $query)) {
        // Redirige de vuelta con un mensaje de éxito (opcional)
        header("Location: pedidos.php?msg=eliminado");
    } else {
        echo "Error al eliminar: " . mysqli_error($conexion);
    }
} else {
    header("Location: pedidos.php");
}
exit;