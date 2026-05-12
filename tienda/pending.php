<?php
include("includes/conexion.php");
include("includes/pedido.php");

$collection_id = $_GET['collection_id'] ?? null;
$status = $_GET['status'] ?? null;
$external_reference = $_GET['external_reference'] ?? null;

if ($status === "pending" && $external_reference) {
    $sql = "UPDATE pedidos SET estado='pendiente', pago_id='$collection_id' WHERE id='$external_reference'";
    if ($conexion->query($sql)) {
        $mensaje = "⏳ El pago del pedido #$external_reference está en revisión.";
    } else {
        $mensaje = "Error al actualizar el pedido: " . $conexion->error;
    }
} else {
    $mensaje = "⚠️ No se pudo procesar el estado del pago.";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pago pendiente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h1>Resultado del Pago</h1>
    <div class="alert alert-warning">
        <?php echo $mensaje; ?>
    </div>
    <a href="carrito.php" class="btn btn-primary">Volver al carrito</a>
</div>
</body>
</html>
