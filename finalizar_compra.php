<?php
require_once __DIR__ . '/includes/conexion.php'; 
session_start();

// 1. Capturamos los datos que Mercado Pago nos manda por la URL
$status = $_GET['status'] ?? '';
$payment_id = $_GET['payment_id'] ?? '';
$external_reference = $_GET['external_reference'] ?? '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estado de tu compra - Mi Tienda</title>
    <link rel="stylesheet" href="css/style.css"> 
</head>
<body>

<div class="resultado-pago">
    <?php if ($status === 'approved'): ?>
        <h1 class="success-text">¡Gracias por tu compra! 🎉</h1>
        <p>Tu pago ha sido procesado con éxito.</p>
        <p><strong>Nro. de Operación:</strong> <?php echo htmlspecialchars($payment_id); ?></p>
        
        <?php 
            // Vaciamos el carrito porque la compra ya se hizo
            unset($_SESSION['carrito']); 
        ?>
        
        <p>Pronto recibirás un mensaje con los detalles del envío.</p>

    <?php elseif ($status === 'pending'): ?>
        <h1 class="pending-text">Pago pendiente ⏳</h1>
        <p>Tu orden se procesará cuando se acredite el pago.</p>

    <?php else: ?>
        <h1 class="error-text">Hubo un problema ❌</h1>
        <p>No pudimos procesar el pago. Por favor, intentá de nuevo.</p>
    <?php endif; ?>

    <br>
    <a href="index_principal.php" class="btn-volver">Volver al inicio</a>
</div>

</body>
</html>