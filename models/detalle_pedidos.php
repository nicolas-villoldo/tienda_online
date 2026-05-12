<?php
class DetallePedido {
    private $conexion;

    public function __construct($db) {
        $this->conexion = $db;
    }

    /**
     * 1. Método para INSERTAR
     * NO ponemos 'subtotal' porque es VIRTUAL GENERATED. 
     * MySQL lo calcula solo apenas guardás la cantidad y el precio.
     */
    public function crear($pedido_id, $producto_id, $cantidad, $precio) {
        // Asegurate que el nombre de la tabla sea 'detalle_pedidos' (en singular el "detalle")
        $sql = "INSERT INTO detalle_pedidos (pedido_id, producto_id, cantidad, precio) 
                VALUES (?, ?, ?, ?)";
        
        $stmt = $this->conexion->prepare($sql);
        
        if (!$stmt) {
            die("Error en Prepare: " . $this->conexion->error);
        }

        // i = int (id, producto, cantidad), d = double/decimal (precio)
        $stmt->bind_param("iiid", $pedido_id, $producto_id, $cantidad, $precio);
        
        if ($stmt->execute()) {
            return true;
        } else {
            die("Error al insertar detalle: " . $stmt->error);
        }
    }

    /**
     * 2. Método para CONSULTAR
     * Acá SI aparece el subtotal porque MySQL ya lo calculó y te lo devuelve.
     */
    public function obtenerPorPedido($pedido_id) {
        // Al usar dp.* traes todas las columnas, INCLUIDO el subtotal generado
        $sql = "SELECT dp.*, p.nombre 
                FROM detalle_pedidos dp 
                INNER JOIN productos p ON dp.producto_id = p.id 
                WHERE dp.pedido_id = ?";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $pedido_id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        // El array final va a tener ['subtotal'] listo para usar en tu frontend
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }
}
?>