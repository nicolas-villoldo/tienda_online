<?php
class Pedido {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }
    public function listarTodos() {
    // Usamos 'id' que seguro existe y es la clave primaria
    $sql = "SELECT * FROM pedidos ORDER BY id DESC";
    $resultado = $this->conexion->query($sql);

    if ($resultado) {
        return $resultado->fetch_all(MYSQLI_ASSOC);
    } else {
        // Si hay error en la consulta, lo logueamos para saber qué pasó
        error_log("Error en listarTodos: " . $this->conexion->error);
        return [];
    }
}
    // 1. Crear pedido (Sincronizado: SIN correo y CON orden correcto)
    public function crear($cliente_id, $direccion, $telefono, $nombre, $total, $metodo, $estado = 'pendiente') {
        // Quitamos correo_contacto de la lista de columnas y de los VALUES
        $sql = "INSERT INTO pedidos (cliente_id, direccion_envio, telefono_contacto, nombre_cliente, total, metodo_pago, estado) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conexion->prepare($sql);
        
        if (!$stmt) {
            die("Error en Prepare: " . $this->conexion->error);
        }

        // Explicación del bind_param ajustado:
        // i = integer (cliente_id)
        // s = string (direccion, telefono, nombre)
        // d = double/decimal (total)
        // s = string (metodo, estado)
        // Total: 7 parámetros (isssdss)
        $stmt->bind_param("isssdss", $cliente_id, $direccion, $telefono, $nombre, $total, $metodo, $estado);
        
        if ($stmt->execute()) {
            return $this->conexion->insert_id; 
        } else {
            error_log("Error en Pedido::crear: " . $stmt->error);
            return false;
        }
    }

    // 2. Insertar detalle (Usando el nombre que definimos: detalle_pedidos)
    public function agregarDetalle($pedido_id, $producto_id, $cantidad, $precio) {
        // Recordá que subtotal es VIRTUAL GENERATED, no se pone acá
        $sql = "INSERT INTO detalle_pedidos (pedido_id, producto_id, cantidad, precio) VALUES (?, ?, ?, ?)";
        $stmt = $this->conexion->prepare($sql);
        
        $stmt->bind_param("iiid", $pedido_id, $producto_id, $cantidad, $precio);
        return $stmt->execute();
    }

    // ... (El resto de métodos guardarTokensMP, obtenerPorId y confirmarPago están OK)
}
?>