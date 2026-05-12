<?php
//mensaje de exito despues de la compra para el cliente
session_start();
include("../includes/conexion.php");

// Mercado Pago envía datos por la URL al regresar (collection_id, status, external_reference)
$pedido_id = $_GET['external_reference'] ?? null;
$status = $_GET['status'] ?? '';

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>¡Gracias por tu compra!</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center bg-white p-5 rounded shadow">
            <?php if ($status === 'approved'): ?>
                <h1 class="display-4 text-success">¡Pago Exitoso! 🎉</h1>
                <p class="lead">Tu pedido <strong>#<?php echo htmlspecialchars($pedido_id); ?></strong> ha sido procesado correctamente.</p>
                <div class="alert alert-info">
                    Te hemos enviado un correo con los detalles de la compra y el seguimiento del envío.
                </div>
                <script>
                    // Un poco de confeti para celebrar la venta
                    confetti({
                        particleCount: 150,
                        spread: 70,
                        origin: { y: 0.6 }
                    });
                </script>
            <?php else: ?>
                <h1 class="display-4 text-warning">Pago Pendiente ⏳</h1>
                <p class="lead">Estamos esperando la confirmación de Mercado Pago para el pedido #<?php echo htmlspecialchars($pedido_id); ?>.</p>
                <p>No te preocupes, en cuanto se acredite te avisaremos.</p>
            <?php endif; ?>

            <hr class="my-4">
            <p>¿Qué quieres hacer ahora?</p>
            <div class="d-grid gap-2 d-md-block">
                <a href="index.php" class="btn btn-primary btn-lg">Seguir Comprando</a>
                <a href="mis_pedidos.php" class="btn btn-outline-secondary btn-lg">Ver mis pedidos</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>