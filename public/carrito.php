<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// 1. Cargamos conexión y vendor
// Asegurate que la ruta sea correcta según tu carpeta
include_once __DIR__ . "/../includes/conexion.php"; 
require __DIR__ . '/../vendor/autoload.php';

use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;

/**
 * 2. CONFIGURACIÓN DEL TOKEN
 */
$token_a_usar = defined('MP_TOKEN') ? MP_TOKEN : "APP_USR-3473124580000888-013101-1d40d4db95744ee5111faa2dab8dc240-3153482970";
MercadoPagoConfig::setAccessToken($token_a_usar);

// 3. RECUPERAR ID DE PEDIDO Y TOTAL
$id_pedido = isset($_GET['id_pedido']) ? $_GET['id_pedido'] : 0;
$total = 0;

if (!empty($_SESSION['carrito'])) {
    foreach ($_SESSION['carrito'] as $p) {
        $total += ($p['precio'] * $p['cantidad']);
    }
}

$preference_url = "#";
$error_msg = "";

// Usamos BASE_URL de conexion.php, si no existe usamos localhost por defecto
$url_sitio = defined('BASE_URL') ? BASE_URL : "http://localhost/tienda_online";

if ($total > 0 && $id_pedido > 0) {
    try {
        $client = new PreferenceClient();
        $preference = $client->create([
            "external_reference" => (string)$id_pedido, // VINCULAMOS CON TU DB
            "items" => [[
                "title" => "Compra en Tienda Avano - Pedido #" . $id_pedido,
                "quantity" => 1,
                "unit_price" => (float)$total,
                "currency_id" => "ARS"
            ]],
            "back_urls" => [
                "success" => $url_sitio . "/index_principal.php?pago=exito", 
                "failure" => $url_sitio . "/index_principal.php?pago=error",
                "pending" => $url_sitio . "/index_principal.php?pago=pendiente"
            ],
            "auto_return" => "approved",
            "binary_mode" => true // Evita que queden pagos en "proceso" eterno
        ]);
        
        $preference_url = $preference->init_point;

    } catch (Exception $e) {
        $error_msg = "Error de MP: " . $e->getMessage();
    }
} else {
    $error_msg = $id_pedido == 0 ? "Falta el número de pedido." : "El carrito está vacío.";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmar Pago - Tienda Nico</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .checkout-card { max-width: 400px; margin: 50px auto; padding: 20px; background: white; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .btn-mp { background: #009ee3; color: white; padding: 12px 25px; border-radius: 5px; text-decoration: none; display: inline-block; font-weight: bold; margin-top: 20px; }
        .error-box { color: #721c24; background: #f8d7da; padding: 15px; margin-top: 20px; border-radius: 5px; border: 1px solid #f5c6cb; }
    </style>
</head>
<body style="text-align: center; background: #f0f2f5; font-family: Arial, sans-serif;">

    <div class="checkout-card">
        <img src="https://www.mercadopago.com/instore/merchant/utils/images/mercadopago-logo.png" width="120" alt="Mercado Pago">
        
        <h1>¡Ya casi, Nico!</h1>
        <p>Pedido N°: <strong><?php echo $id_pedido; ?></strong></p>
        <p>Total a abonar:</p>
        <h2 style="color: #333;">$<?php echo number_format($total, 2, ',', '.'); ?></h2>
        
        <?php if ($preference_url !== "#"): ?>
            <p style="font-size: 0.9em; color: #666;">Al hacer clic, serás redirigido a Mercado Pago de forma segura.</p>
            <a href="<?php echo $preference_url; ?>" class="btn-mp">PAGAR CON MERCADO PAGO</a>
        <?php else: ?>
            <div class="error-box">
                <strong>No se pudo generar el pago:</strong><br>
                <?php echo $error_msg; ?>
            </div>
        <?php endif; ?>
        
        <br><br>
        <a href="../index_principal.php" style="color: #666; text-decoration: none; font-size: 0.9em;">← Cancelar y volver</a>
    </div>

</body>
</html>