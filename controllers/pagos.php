<?php
// controles/pago.php

class PagoController {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    public function procesarRegresoExitoso($datos_mp) {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }

        // IMPORTANTE: Ajustamos la ruta para salir de 'controles' y entrar a 'models'
        include_once __DIR__ . "/../models/pedido.php";
        include_once __DIR__ . "/../models/pago.php";

        // Capturamos los datos que vienen por la URL (GET)
        $status = $datos_mp['status'] ?? '';
        $payment_id = $datos_mp['payment_id'] ?? '';
        $id_cliente = $datos_mp['external_reference'] ?? ''; 

        if ($status === 'approved' && !empty($id_cliente)) {
            // 1. Buscamos el pedido pendiente de ese cliente
            $sql = "SELECT id FROM pedidos WHERE cliente_id = ? AND estado = 'pendiente' ORDER BY creado_en DESC LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $id_cliente);
            $stmt->execute();
            $resultado = $stmt->get_result();

            if ($resultado->num_rows > 0) {
                $pedido = $resultado->fetch_assoc();
                $pedido_id = $pedido['id'];

                // 2. Actualizamos pedido a 'pagado'
                $this->db->query("UPDATE pedidos SET estado = 'pagado' WHERE id = $pedido_id");

                // 3. Actualizamos tabla pagos
                $this->db->query("UPDATE pagos SET estado = 'aprobado', payment_id_mp = '$payment_id' WHERE pedido_id = $pedido_id");

                // 4. Limpiamos carrito
                unset($_SESSION['carrito']);
                return true;
            }
        }
        return false;
    }
}