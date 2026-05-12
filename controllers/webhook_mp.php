<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../includes/conexion.php';

use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Payment\PaymentClient;

MercadoPagoConfig::setAccessToken("APP_USR-2e175033-ab17-48e3-8e3b-88ce10e4d87d");

$id_pago = $_GET['data_id'] ?? $_POST['data_id'] ?? null;
$tipo = $_GET['type'] ?? $_POST['type'] ?? null;

if ($tipo === 'payment' && $id_pago) {
    $client = new PaymentClient();
    
    try {
        $payment = $client->get($id_pago);
        
        if ($payment->status === 'approved') {
            $monto = $payment->transaction_amount;
            $id_cliente = (int)$payment->external_reference; 

            // 1. BUSCAMOS LA DIRECCIÓN ACTUAL DEL CLIENTE
            $consulta = $conexion->prepare("SELECT direccion FROM clientes WHERE id = ?");
            $consulta->bind_param("i", $id_cliente);
            $consulta->execute();
            $fila = $consulta->get_result()->fetch_assoc();
            $dire_envio = $fila['direccion'] ?? 'Dirección no especificada';

            // 2. INSERTAMOS EL PEDIDO CON LA DIRECCIÓN
            $stmt = $conexion->prepare("INSERT INTO pedidos (total, estado, cliente_id, direccion_envio) VALUES (?, 'pagado', ?, ?)");
            $stmt->bind_param("dis", $monto, $id_cliente, $dire_envio);
            $stmt->execute();
            $id_nuevo_pedido = $conexion->insert_id;

            // 3. ACTUALIZACIÓN DE STOCK PRODUCTO POR PRODUCTO
            // Verificamos si Mercado Pago nos envió la información adicional de los ítems
            if (isset($payment->additional_info->items)) {
                foreach ($payment->additional_info->items as $item) {
                    $id_prod = (int)$item->id; // El ID del producto en tu base de datos
                    $cantidad = (int)$item->quantity; // Cuántos compró de ese producto

                    // Restamos la cantidad exacta al producto correspondiente
                    $upd = $conexion->prepare("UPDATE productos SET stock = stock - ? WHERE id = ?");
                    $upd->bind_param("ii", $cantidad, $id_prod);
                    $upd->execute();
                }
            }

            http_response_code(200);
            exit("Pedido #$id_nuevo_pedido procesado y stock actualizado.");
        }
    } catch (Exception $e) {
        http_response_code(200);
        exit("Error: " . $e->getMessage());
    }
}
http_response_code(200);
?>