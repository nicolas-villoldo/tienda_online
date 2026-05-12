<?php
include("../includes/conexion.php");

// 1. Datos de prueba (Simulando lo que vendría del formulario)
$cliente_id = 1;
$direccion  = "Calle Falsa 123, CABA";
$telefono   = "1122334455";
$correo     = "diego@prueba.com";
$total      = 599.99;
$estado     = 'pendiente';

// 2. Insertar Pedido con las NUEVAS COLUMNAS
// El orden debe ser igual al de tu tabla pedidos
$sqlPedido = "INSERT INTO pedidos (cliente_id, direccion_envio, telefono_contacto, correo_contacto, total, estado)
              VALUES (?, ?, ?, ?, ?, ?)";

$stmtP = $conexion->prepare($sqlPedido);

// "isssds" -> i (int), s (string), s (string), s (string), d (double), s (string)
$stmtP->bind_param("isssds", $cliente_id, $direccion, $telefono, $correo, $total, $estado);

if ($stmtP->execute()) {
    $pedido_id = $conexion->insert_id;

    // 3. Insertar detalle del pedido (Producto ID 1 como prueba)
    $producto_id = 1;
    $cantidad    = 1;
    $precio      = 599.99;

    $sqlDetalle = "INSERT INTO detalle_pedidos (pedido_id, producto_id, cantidad, precio)
                   VALUES (?, ?, ?, ?)";
    
    $stmtD = $conexion->prepare($sqlDetalle);
    $stmtD->bind_param("iiid", $pedido_id, $producto_id, $cantidad, $precio);
    
    if ($stmtD->execute()) {
        echo "✅ Pedido de prueba creado con ID: " . $pedido_id . "<br>";
        echo "📍 Dirección cargada: " . $direccion;
    } else {
        echo "❌ Error en detalle: " . $conexion->error;
    }

} else {
    echo "❌ Error en pedido: " . $conexion->error;
}
?>