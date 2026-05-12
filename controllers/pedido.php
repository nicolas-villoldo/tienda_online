<?php
include_once("../includes/conexion.php");
include_once("../models/pedido.php");
include_once("../models/detalle_pedido.php"); 

class PedidoService {
    private $modeloPedido;
    private $modeloDetalle;

    public function __construct($conexion) {
        $this->modeloPedido = new Pedido($conexion);
        $this->modeloDetalle = new DetallePedido($conexion);
    }

    public function crearPedidoCompleto($cliente_id, $carrito, $datosEnvio) {
        // 1. Calcular el total
        $total = 0;
        foreach ($carrito as $item) {
            $total += $item['precio'] * $item['cantidad'];
        }

        // 2. Crear el pedido principal
        // IMPORTANTE: Respetamos el orden del modelo: 
        // 1.cliente_id, 2.direccion, 3.telefono, 4.nombre, 5.total, 6.metodo, 7.estado
        $pedido_id = $this->modeloPedido->crear(
            $cliente_id, 
            $datosEnvio['direccion'], 
            $datosEnvio['telefono'], 
            $datosEnvio['nombre'], // Agregamos el nombre
            $total,
            $datosEnvio['metodo_pago'] ?? 'mercadopago', // Agregamos el método
            'pendiente' // El estado inicial
        );

        // 3. Si se creó el pedido, insertar los detalles
        if ($pedido_id) {
            foreach ($carrito as $item) {
                // Usamos el modelo DetallePedido que ignora el subtotal virtual
                $this->modeloDetalle->crear(
                    $pedido_id,
                    $item['id'],
                    $item['cantidad'],
                    $item['precio']
                );
            }
        }
        
        return $pedido_id;
    }
}
?>