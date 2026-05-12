<?php
// 1. Carga de dependencias y configuración inicial
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../includes/conexion.php'; 
require_once __DIR__ . '/../models/pedido.php'; 

use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;

// Centralizá tu token
MercadoPagoConfig::setAccessToken("APP_USR-2e175033-ab17-48e3-8e3b-88ce10e4d87d");

session_start();

// 2. CHEQUEOS PREVIOS
if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
    header("Location: ../index_principal.php");
    exit;
}

// CAPTURAMOS DATOS DEL FORMULARIO
$nombre    = $_POST['nombre'] ?? 'Cliente Sin Nombre';
$telefono  = $_POST['telefono'] ?? '';
$direccion = $_POST['direccion'] ?? '';
$correo    = $_POST['correo'] ?? ''; // Importante para tu nueva columna
$metodo_elegido = $_POST['metodo_pago'] ?? 'mercadopago';

$items_mp = [];
$total_compra = 0; 

// 3. PROCESAMOS EL CARRITO
foreach ($_SESSION['carrito'] as $producto) {
    $precio   = (float)($producto['precio'] ?? 0.0);
    $cantidad = (int)($producto['cantidad'] ?? 1);
    $total_compra += ($precio * $cantidad);
    
    $items_mp[] = [
        "id" => "prod-" . ($producto['id'] ?? '0'),
        "title" => (string)($producto['nombre'] ?? 'Producto'),
        "quantity" => $cantidad,
        "unit_price" => $precio,
        "currency_id" => "ARS"
    ];
}

// 4. CREAMOS EL PEDIDO EN LA BASE DE DATOS
$pedidoModel = new Pedido($conexion);
$cliente_id = $_SESSION['cliente_id'] ?? 1; 

// Iniciamos transacción
$conexion->begin_transaction();

try {
    // Agregamos el correo y el método elegido al crear el pedido
    $id_pedido = $pedidoModel->crear(
        $cliente_id, 
        $direccion, 
        $telefono, 
        $nombre, 
        $total_compra, 
        $metodo_elegido, // Nuevo parámetro
        $correo          // Nuevo parámetro
    );

    if (!$id_pedido) {
        throw new Exception("Error al crear el pedido: " . $conexion->error);
    }

    foreach ($_SESSION['carrito'] as $p) {
        $pedidoModel->agregarDetalle($id_pedido, $p['id'], $p['cantidad'], $p['precio']);
        
        // Descontar stock
        $sql_stock = "UPDATE productos SET stock = stock - ? WHERE id = ?";
        $stmt_stock = $conexion->prepare($sql_stock);
        $stmt_stock->bind_param("ii", $p['cantidad'], $p['id']);
        $stmt_stock->execute();
    }

    $conexion->commit(); 

} catch (Exception $e) {
    $conexion->rollback();
    die("❌ Error crítico: " . $e->getMessage());
}

// 5. LÓGICA DE REDIRECCIÓN SEGÚN MÉTODO
if ($metodo_elegido === 'efectivo') {
    // Si es efectivo, simplemente vaciamos y confirmamos
    unset($_SESSION['carrito']); 
    header("Location: ../index_principal.php?pago=exito_efectivo&orden=" . $id_pedido);
    exit;
} else {
    // MERCADO PAGO
    $url_base = "https://unsedimental-alycia-precollusive.ngrok-free.dev/tienda_online";
    $client = new PreferenceClient();

    try {
        $preference = $client->create([
            "items" => $items_mp,
            "binary_mode" => true,
            "back_urls" => [
                "success" => $url_base . "/tienda/finalizar_compra.php?pedido=" . $id_pedido,
                "failure" => $url_base . "/index_principal.php?pago=error",
                "pending" => $url_base . "/index_principal.php?pago=pendiente"
            ],
            "auto_return" => "approved", 
            "external_reference" => (string)$id_pedido 
        ]);

        // --- ACTUALIZAMOS EL PEDIDO CON EL ID DE PREFERENCIA ---
        $pref_id = $preference->id;
        $sql_update = "UPDATE pedidos SET preference_id = ? WHERE id = ?";
        $stmt_up = $conexion->prepare($sql_update);
        $stmt_up->bind_param("si", $pref_id, $id_pedido);
        $stmt_up->execute();

        // Redirigimos al Checkout de MP
        header("Location: " . $preference->init_point);
        exit;

    } catch (Exception $e) {
        die("Error en Mercado Pago: " . $e->getMessage());
    }
}