<?php
// 1. Carga de dependencias y sesión
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../includes/conexion.php';
require_once __DIR__ . '/../models/pedido.php';

use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Payment\PaymentClient;

session_start();

// 2. Configuración (Usá el mismo token que en procesar_pago)
MercadoPagoConfig::setAccessToken("APP_USR-2e175033-ab17-48e3-8e3b-88ce10e4d87d");

// 3. Capturamos los datos que nos manda MP por la URL
$id_pedido_url = $_GET['pedido'] ?? null;
$payment_id = $_GET['payment_id'] ?? null; // ID del pago en Mercado Pago
$status = $_GET['status'] ?? null;         // Estado (approved, pending, etc.)

if (!$id_pedido_url || !$payment_id || $status !== 'approved') {
    header("Location: ../index_principal.php?pago=error_validacion");
    exit;
}

try {
    // 4. VALIDACIÓN REAL: Le preguntamos a MP si ese pago realmente existe y está aprobado
    $client = new PaymentClient();
    $payment = $client->get($payment_id);

    if ($payment->status === 'approved') {
        
        // El pago es REAL y está APROBADO
        $pedidoModel = new Pedido($conexion);
        
        // 5. Actualizamos el estado del pedido en nuestra DB
        // (Asegurate que tu modelo tenga una función para esto o hacé el UPDATE acá)
        $sql_update = "UPDATE pedidos SET estado = 'pagado', id_transaccion_mp = ? WHERE id = ?";
        $stmt = $conexion->prepare($sql_update);
        $stmt->bind_param("si", $payment_id, $id_pedido_url);
        $stmt->execute();

        // 6. Limpiamos el carrito porque la venta ya se concretó
        unset($_SESSION['carrito']);

        // 7. Mostramos éxito
        $mensaje_final = "¡Gracias por tu compra! Tu pago fue procesado con éxito.";
        $icono = "✅";
    } else {
        header("Location: ../index_principal.php?pago=no_aprobado");
        exit;
    }

} catch (Exception $e) {
    die("Error al validar el pago: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Confirmación de Compra</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #121212; color: #fff; display: flex; align-items: center; justify-content: center; height: 100vh; }
        .card-final { background: #1e1e1e; border: 1px solid #333; border-radius: 20px; padding: 40px; text-align: center; max-width: 500px; }
        .btn-volver { background: #f1c40f; color: #000; font-weight: bold; border-radius: 10px; padding: 10px 25px; text-decoration: none; }
    </style>
</head>
<body>
    <div class="card-final shadow-lg">
        <h1 style="font-size: 4rem;"><?php echo $icono; ?></h1>
        <h2 class="mb-4">Orden #<?php echo $id_pedido_url; ?></h2>
        <p class="lead mb-4"><?php echo $mensaje_final; ?></p>
        <a href="../index_principal.php" class="btn-volver">Volver al inicio</a>
    </div>
</body>
</html>