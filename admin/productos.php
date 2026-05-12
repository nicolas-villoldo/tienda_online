<?php
require_once 'cerrojo.php';
require_once '../includes/conexion.php';
require_once '../models/pedido.php';

$objPedido = new Pedido($conexion);

// 1. Obtener los datos del pedido
$id = intval($_GET['id'] ?? 0);
$pedido = $objPedido->obtenerPorId($id); // Este método debe devolver los datos del cliente también

if (!$pedido) {
    header("Location: pedidos.php");
    exit();
}

// 2. Si se presionó "Actualizar Estado"
if (isset($_POST['actualizar_estado'])) {
    $nuevo_estado = $_POST['estado'];
    $sql = "UPDATE pedidos SET estado = ? WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("si", $nuevo_estado, $id);
    
    if ($stmt->execute()) {
        header("Location: editar_pedido.php?id=$id&msg=ok");
        exit();
    }
}

// 3. Obtener el detalle de productos de este pedido
// Asumiendo que tenés una tabla 'detalle_pedidos' o similar
$sql_detalle = "SELECT dp.*, p.nombre 
                FROM detalle_pedidos dp 
                INNER JOIN productos p ON dp.producto_id = p.id 
                WHERE dp.pedido_id = ?";
$stmt_d = $conexion->prepare($sql_detalle);
$stmt_d->bind_param("i", $id);
$stmt_d->execute();
$detalle = $stmt_d->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle Pedido #<?php echo $id; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-dark text-white">Estado del Pedido</div>
                <div class="card-body">
                    <form method="POST">
                        <select name="estado" class="form-select mb-3">
                            <option value="pendiente" <?php echo ($pedido['estado'] == 'pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                            <option value="pagado" <?php echo ($pedido['estado'] == 'pagado') ? 'selected' : ''; ?>>Pagado</option>
                            <option value="enviado" <?php echo ($pedido['estado'] == 'enviado') ? 'selected' : ''; ?>>Enviado</option>
                        </select>
                        <button type="submit" name="actualizar_estado" class="btn btn-success w-100">Actualizar Estado</button>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">Datos de Entrega</div>
                <div class="card-body">
                    <p><strong>Cliente:</strong> <?php echo htmlspecialchars($pedido['cliente_nombre'] ?? 'N/A'); ?></p>
                    <p><strong>Dirección:</strong><br><?php echo nl2br(htmlspecialchars($pedido['direccion_envio'])); ?></p>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between">
                    <span>Productos en el Pedido</span>
                    <span>Total: $<?php echo number_format($pedido['total'], 0, ',', '.'); ?></span>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($detalle as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['nombre']); ?></td>
                                <td><?php echo $item['cantidad']; ?></td>
                                <td>$<?php echo number_format($item['precio_unitario'] * $item['cantidad'], 0, ',', '.'); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="mt-3">
                <a href="pedidos.php" class="btn btn-outline-secondary">Volver a la lista</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>