<?php
class Pago {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    // Registrar pago con Seguridad (Prepare)
    public function crear($pedido_id, $monto, $metodo, $estado, $codigo_transaccion) {
        $sql = "INSERT INTO pagos (pedido_id, monto, metodo, estado, codigo_transaccion) 
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $this->conexion->prepare($sql);
        
        if (!$stmt) {
            die("Error en Prepare de Pagos: " . $this->conexion->error);
        }

        // i=int, d=decimal, s=string
        // fíjate que pasamos: pedido_id (i), monto (d), metodo (s), estado (s), codigo (s)
        $stmt->bind_param("idsss", $pedido_id, $monto, $metodo, $estado, $codigo_transaccion);
        
        return $stmt->execute();
    }

    // Obtener pagos de un pedido (Seguro)
    public function obtenerPorPedido($pedido_id) {
        $sql = "SELECT * FROM pagos WHERE pedido_id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $pedido_id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }
}
?>