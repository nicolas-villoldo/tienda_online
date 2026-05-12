<?php
// 1. Iniciar sesión y conexión
session_start();

// Usamos realpath para asegurarnos de que encuentre el archivo subiendo un nivel
require_once realpath(__DIR__ . '/../includes/conexion.php');

// Verificamos si la función existe antes de llamarla para que no de Fatal Error
if (function_exists('conectarPDO')) {
    $pdo = conectarPDO(); 
} else {
    die("Error: La función conectarPDO() no se encontró en includes/conexion.php");
}

// 2. CHEQUEOS DE SEGURIDAD
if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
    header("Location: ../index.php"); 
    exit();
}

// 3. RECUPERAR DATOS DEL FORMULARIO (POST)
$direccion   = $_POST['direccion'] ?? 'Retiro en local';
$telefono    = $_POST['telefono'] ?? 'Sin teléfono';
$nombre      = $_POST['nombre'] ?? 'Invitado';
$metodo_pago = $_POST['metodo_pago'] ?? 'efectivo';

// Calculamos el total
$total_carrito = 0;
foreach ($_SESSION['carrito'] as $item) {
    $total_carrito += $item['precio'] * $item['cantidad'];
}

$id_cliente = $_SESSION['cliente_id'] ?? 1; 

try {
    $pdo->beginTransaction();

    // 4. INSERT en 'pedidos'
    $sql_pedido = "INSERT INTO pedidos (cliente_id, nombre_cliente, direccion_envio, telefono_contacto, total, metodo_pago, estado) 
                   VALUES (:cliente_id, :nombre, :direccion, :telefono, :total, :metodo, 'pendiente')";
    
    $stmt = $pdo->prepare($sql_pedido);
    $stmt->execute([
        ':cliente_id' => $id_cliente,
        ':nombre'     => $nombre,
        ':direccion'  => $direccion,
        ':telefono'   => $telefono,
        ':total'      => $total_carrito,
        ':metodo'     => $metodo_pago
    ]);

    $id_pedido_nuevo = $pdo->lastInsertId();

    // 5. INSERT en 'detalle_pedidos'
    $sql_detalle = "INSERT INTO detalle_pedidos (pedido_id, producto_id, cantidad, precio) 
                    VALUES (:pedido_id, :producto_id, :cantidad, :precio)";
    $stmt_detalle = $pdo->prepare($sql_detalle);

    foreach ($_SESSION['carrito'] as $producto) {
        $stmt_detalle->execute([
            ':pedido_id'   => $id_pedido_nuevo,
            ':producto_id' => $producto['id'],
            ':cantidad'    => $producto['cantidad'],
            ':precio'      => $producto['precio']
        ]);
        
        // Descontar stock
        $sql_stock = "UPDATE productos SET stock = stock - :cant WHERE id = :id";
        $stmt_stock = $pdo->prepare($sql_stock);
        $stmt_stock->execute([':cant' => $producto['cantidad'], ':id' => $producto['id']]);
    }

    $pdo->commit();
    
    // 6. LÓGICA DE REDIRECCIÓN
    if ($metodo_pago === 'mercadopago') {
        // Asegurate de tener este archivo creado o cambialo a tu link de pago
        header("Location: checkout.php?id_pedido=" . $id_pedido_nuevo);
    } else {
        // Si es efectivo, vaciamos el carrito y avisamos éxito
        $_SESSION['carrito'] = []; 
        header("Location: ../index.php?pago=exito_efectivo");
    }
    exit();

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    die("Error en la compra: " . $e->getMessage());
}
?>