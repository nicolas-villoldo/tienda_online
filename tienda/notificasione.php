<?php
// Asegúrate de que la ruta al vendor sea correcta según tu carpeta
require_once __DIR__ . '/../vendor/autoload.php';

// Importar las clases necesarias del SDK v2
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Payment\PaymentClient;

// 1. Configura tus credenciales
MercadoPagoConfig::setAccessToken("APP_USR-2e175033-ab17-48e3-8e3b-88ce10e4d87d");

// 2. Obtener el cuerpo de la notificación
$json_event = file_get_contents('php://input');
$data = json_decode($json_event, true);

// Verificamos que sea una notificación de pago
if (isset($data['type']) && $data['type'] == 'payment') {
    
    $payment_id = $data['data']['id'];
    $client = new PaymentClient(); // Instanciar el cliente de pagos

    try {
        // 3. Consultar los detalles completos del pago
        $payment = $client->get($payment_id);

        if ($payment) {
            $status = $payment->status; 
            $status_detail = $payment->status_detail;
            $external_reference = $payment->external_reference;

            // 4. Conexión a tu DB (Cambia 'tu_password' por el real)
            $pdo = new PDO("mysql:host=localhost;dbname=tienda_online;charset=utf8", "root", "TuPasswordReal");

            // 5. Actualizar la tabla de PAGOS
            $sql = "UPDATE pagos SET 
                    mp_payment_id = :mp_id, 
                    mp_status_detail = :detail, 
                    estado = :estado, 
                    payload_recibido = :payload 
                    WHERE pedido_id = :pedido_id";
 
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':mp_id' => $payment_id,
                ':detail' => $status_detail,
                ':estado' => ($status == 'approved') ? 'completado' : 'fallido',
                // En el SDK v2, los objetos se pueden convertir a JSON así:
                ':payload' => json_encode($payment->toArray()), 
                ':pedido_id' => $external_reference
            ]);

            // 6. Si el pago fue aprobado, actualizar el ESTADO DEL PEDIDO
            if ($status == 'approved') {
                $sql_pedido = "UPDATE pedidos SET estado = 'pagado' WHERE id = :pedido_id";
                $pdo->prepare($sql_pedido)->execute([':pedido_id' => $external_reference]);
            }
        }
    } catch (Exception $e) {
        // Log de errores si algo falla con la API
        error_log($e->getMessage());
    }
}

http_response_code(200);  