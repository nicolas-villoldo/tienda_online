<?php
session_start(); 
include("../includes/conexion.php");

// 1. Verificar carrito
if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
    header("Location: index_principal.php");
    exit();
}

// 2. Recibir datos del formulario (Nombre, correo, etc.)
$nombre    = $_POST['nombre'] ?? 'Invitado';
$correo    = $_POST['correo'] ?? '';
$telefono  = $_POST['telefono'] ?? '';
$direccion = $_POST['direccion'] ?? '';

$total = 0;
foreach ($_SESSION['carrito'] as $item) {
    $total += $item['precio'] * $item['cantidad'];
}

// 3. Crear el pedido
$sqlPedido = "INSERT INTO pedidos (cliente_id, direccion_envio, telefono_contacto, correo_contacto, total, estado)
              VALUES (?, ?, ?, ?, ?, 'pendiente')";

$stmtP = $conexion->prepare($sqlPedido);
$cliente_id = $_SESSION['cliente_id'] ?? 1; // Usamos el de la sesión o 1 por defecto
$stmtP->bind_param("isssd", $cliente_id, $direccion, $telefono, $correo, $total);

if ($stmtP->execute()) {
    $pedido_id = $stmtP->insert_id;

    // 4. Insertar detalles
    $sqlDetalle = "INSERT INTO detalle_pedidos (pedido_id, producto_id, cantidad, precio)
                   VALUES (?, ?, ?, ?)";
    $stmtD = $conexion->prepare($sqlDetalle);

    foreach ($_SESSION['carrito'] as $item) {
        $producto_id = $item['id'];
        $cantidad    = $item['cantidad'];
        $precio      = $item['precio'];

        $stmtD->bind_param("iiid", $pedido_id, $producto_id, $cantidad, $precio);
        $stmtD->execute();
    }

    // --- ¡ESTA ES LA CLAVE! ---
    // No vaciamos el carrito acá para que Mercado Pago pueda leerlo.
    // Mandamos al usuario al archivo que tiene el BOTÓN AZUL de Mercado Pago.
    // Asegurate que el nombre del archivo sea el correcto (ej: confirmar_pago.php)
   // En la parte final de nuevo_pago.php, reemplazá el header por este:
header("Location: checkout.php?id_pedido=" . $pedido_id);
exit();
    
} else {
    die("Error al crear pedido: " . $conexion->error);
}
?>