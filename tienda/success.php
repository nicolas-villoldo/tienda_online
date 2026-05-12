<?php
// 1. Sesión y Conexiones arriba de todo
session_start();
require_once __DIR__ . '/../includes/conexion.php'; 
require_once __DIR__ . '/../models/pedido.php'; 

// 2. Traemos los datos de la URL
$collection_id = $_GET['collection_id'] ?? null; 
$status = $_GET['status'] ?? null;              
$external_reference = $_GET['external_reference'] ?? null; 

$mensaje = "";
$clase_alerta = "alert-warning"; // Por defecto

if ($status === "approved" && $external_reference) {
    
    // 3. LIMPIEZA DE DATOS
    $id_pedido = $conexion->real_escape_string($external_reference);
    $id_pago_mp = $conexion->real_escape_string($collection_id);

    // 4. ACTUALIZACIÓN EN LA DB
    $sql = "UPDATE pedidos SET estado='pagado', pago_id='$id_pago_mp' WHERE id='$id_pedido'";
    
    if ($conexion->query($sql)) {
        $mensaje = "✅ ¡Pago confirmado! Tu pedido #$id_pedido ya figura como pagado.";
        $clase_alerta = "alert-success";
        
        // 5. Vaciamos el carrito
        unset($_SESSION['carrito']);
    } else {
        $mensaje = "❌ Error en la base de datos: " . $conexion->error;
        $clase_alerta = "alert-danger";
    }
} else {
    $mensaje = "⚠️ El pago no se pudo confirmar o está pendiente. ID de Pedido: " . htmlspecialchars($external_reference);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado de tu Compra</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container">
    <div class="card-resultado">
        <h1 class="mb-4">Estado del Pago</h1>
        
        <div class="alert <?php echo $clase_alerta; ?>">
            <?php echo $mensaje; ?>
        </div>
        
        <hr>
        
        <p class="text-muted">
            ¿Tenés dudas? Guardá tu ID de operación: <br>
            <strong><?php echo htmlspecialchars($collection_id); ?></strong>
        </p>
        
        <a href="../index_principal.php" class="btn-dark-custom mt-3">Volver al Inicio</a>
    </div>
</div>

</body>
</html>