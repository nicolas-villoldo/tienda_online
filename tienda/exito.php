<?php
// Ajustamos las rutas
require_once __DIR__ . '/../../includes/conexion.php'; 
require_once __DIR__ . '/../../models/pedido.php'; 
require_once __DIR__ . '/../../models/pago.php';

session_start();

$status = $_GET['status'] ?? '';
$payment_id = $_GET['payment_id'] ?? '';
$id_cliente = $_GET['external_reference'] ?? ''; 

$pedido_id = null;

if ($status === 'approved' && !empty($id_cliente)) {
    
    // Buscamos el último pedido 'pendiente'
    $id_cliente_clean = $conexion->real_escape_string($id_cliente);
    $sqlBusqueda = "SELECT id FROM pedidos WHERE cliente_id = '$id_cliente_clean' AND estado = 'pendiente' ORDER BY creado_en DESC LIMIT 1";
    $resBusqueda = $conexion->query($sqlBusqueda);
    
    if ($resBusqueda && $resBusqueda->num_rows > 0) {
        $pedido = $resBusqueda->fetch_assoc();
        $pedido_id = $pedido['id'];

        // Marcamos el pedido como 'pagado'
        $conexion->query("UPDATE pedidos SET estado = 'pagado' WHERE id = $pedido_id");

        // Actualizamos o Insertamos en la tabla de pagos
        $payment_id_clean = $conexion->real_escape_string($payment_id);
        $conexion->query("UPDATE pagos SET estado = 'aprobado', payment_id_mp = '$payment_id_clean' WHERE pedido_id = $pedido_id");

        // 3. VACIAR CARRITO
        $_SESSION['carrito'] = [];
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¡Compra Exitosa!</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card-exito">
                    <div class="check-icon">✔</div>
                    <h1 class="text-success-pro">¡Pago Confirmado! 🎉</h1>
                    <p class="lead">Gracias por confiar en nosotros.</p>
                    
                    <?php if ($pedido_id): ?>
                        <div class="info-pedido">
                            Pedido: <strong>#<?php echo $pedido_id; ?></strong><br>
                            Comprobante MP: <strong><?php echo htmlspecialchars($payment_id); ?></strong>
                        </div>
                    <?php endif; ?>

                    <hr>
                    <p class="text-muted">Ya estamos preparando tu envío. Te notificaremos por mail.</p>
                    
                    <a href="../../index_principal.php" class="btn-seguir-comprando">Seguir Comprando</a>
                </div>
            </div>
        </div>
    </div>

</body>
</html>