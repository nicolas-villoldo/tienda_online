<?php
session_start();
require_once '../includes/conexion.php'; 
require_once '../controles/pagos.php'; 

$pagoControl = new PagoController($conexion);
$pago_procesado = false;

if (isset($_GET['status']) && $_GET['status'] == 'approved') {
    $pago_procesado = $pagoControl->procesarRegresoExitoso($_GET);
    if ($pago_procesado) {
        unset($_SESSION['carrito']);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>¡Compra Exitosa!</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

    <div class="success-card">
        <div class="icon">✅</div>
        <h1>¡Muchas gracias por tu compra!</h1>
        <p>El pago fue aprobado con éxito.</p>
        <p>Tu número de operación es: <strong><?php echo htmlspecialchars($_GET['payment_id'] ?? 'N/A'); ?></strong></p>
        
        <hr>
        
        <?php if ($pago_procesado): ?>
            <p>Tu pedido ha sido registrado y pagado correctamente.</p>
        <?php else: ?>
            <p class="status-msg">El pago fue aprobado, pero hubo un detalle al actualizar tu pedido. No te preocupes, guardá tu número de operación.</p>
        <?php endif; ?>
        
        <p>En breve nos pondremos en contacto para el envío.</p>
        
        <a href="../index_principal.php" class="btn-volver">Volver a la tienda</a>
    </div>

</body>
</html>