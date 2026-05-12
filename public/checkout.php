<?php
session_start();
include("../includes/conexion.php"); 
require_once __DIR__ . '/../models/pedidos.php'; // Cargamos tu clase Pedido
require __DIR__ . '/../vendor/autoload.php';

use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;

MercadoPagoConfig::setAccessToken("APP_USR-2e175033-ab17-48e3-8e3b-88ce10e4d87d");

// Verificación de carrito
if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
    header("Location: ../index_principal.php");
    exit;
}

// CAPTURAMOS DATOS DEL FORMULARIO
$direccion = $_POST['direccion'] ?? '';
$telefono  = $_POST['telefono'] ?? '';
$metodo_pago = $_POST['metodo_pago'] ?? 'mercadopago';
$cliente_id = $_SESSION['cliente_id'] ?? 1; // Asegurarse que el ID 1 exista en la tabla clientes
$correo_placeholder = "cliente_tienda@web.com";

$url_base = "https://unsedimental-alycia-precollusive.ngrok-free.dev/tienda_online";

// CALCULAMOS TOTAL
$total = 0;
foreach ($_SESSION['carrito'] as $item) {
    $total += $item['precio'] * $item['cantidad'];
}

// 1. CREAMOS EL PEDIDO USANDO TU CLASE
$pedidoModel = new Pedido($conexion);
$pedido_id = $pedidoModel->crear(
    $cliente_id, 
    $direccion, 
    $telefono, 
    $correo_placeholder, 
    $total, 
    'pendiente'
);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Confirmar Pedido</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
<?php
if ($pedido_id) {
    // 2. INSERTAR DETALLES
    foreach ($_SESSION['carrito'] as $item) {
        $pedidoModel->agregarDetalle($pedido_id, $item['id'], $item['cantidad'], $item['precio']);
    }

    // 3. LÓGICA SEGÚN MÉTODO DE PAGO
    if ($metodo_pago === 'efectivo') {
        // Registro de pago en efectivo
        $sqlPago = "INSERT INTO pagos (pedido_id, monto, metodo, estado) VALUES ($pedido_id, $total, 'efectivo', 'pendiente')";
        $conexion->query($sqlPago);
        
        unset($_SESSION['carrito']); // En efectivo vaciamos acá directamente
        echo "<div class='card shadow p-4 text-center'>";
        echo "<h2 class='text-success'>✅ ¡Pedido #{$pedido_id} Recibido!</h2>";
        echo "<p>Prepararemos tus productos. Pagás $".number_format($total, 2, ',', '.')." al recibir.</p>";
        echo "<a href='../index_principal.php' class='btn btn-primary'>Volver a la tienda</a>";
        echo "</div>";

    } else {
        // Registro de pago Mercado Pago
        $sqlPago = "INSERT INTO pagos (pedido_id, monto, metodo, estado) VALUES ($pedido_id, $total, 'mercadopago', 'pendiente')";
        $conexion->query($sqlPago);

        // Generar Preferencia MP
        $items_mp = [];
        foreach ($_SESSION['carrito'] as $item) {
            $items_mp[] = [
                "title" => $item['nombre'],
                "quantity" => (int)$item['cantidad'],
                "unit_price" => (float)$item['precio'],
                "currency_id" => "ARS"
            ];
        }

        $client = new PreferenceClient();
        $preference = $client->create([
            "items" => $items_mp,
            "external_reference" => (string)$pedido_id,
            "binary_mode" => true,
            "back_urls" => [
                "success" => "$url_base/exito.php",
                "failure" => "$url_base/index_principal.php?pago=error",
                "pending" => "$url_base/index_principal.php?pago=pendiente"
            ],
            "auto_return" => "approved",
        ]);

        echo "<div class='card shadow p-4 text-center'>";
        echo "<h2 class='text-primary'>💳 Casi listo...</h2>";
        echo "<h4>Pedido #{$pedido_id} registrado</h4>";
        echo "<hr>";
        echo "<h3>Total: <strong>$" . number_format($total, 2, ',', '.') . "</strong></h3>";
        echo "<a href='{$preference->init_point}' class='btn btn-success btn-lg w-100 mt-3'>Pagar con Mercado Pago</a>";
        echo "</div>";
    }

} else {
    echo "<div class='alert alert-danger'>❌ Error al registrar pedido: " . $conexion->error . "<br>Asegurate de que el Cliente ID 1 exista en la tabla clientes.</div>";
}
?>
</div>
</body>
</html>