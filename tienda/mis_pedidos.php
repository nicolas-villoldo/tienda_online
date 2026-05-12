<?php
session_start();
include("../includes/conexion.php");
include("../models/pedido.php");

// 1. Validar que el usuario esté logueado
$cliente_id = $_SESSION['cliente_id'] ?? 1; // Ajusta según tu sistema de login

// 2. Consultar los pedidos del cliente junto con el estado de su pago
$sql = "SELECT p.id, p.total, p.estado AS estado_pedido, p.creado_en, 
               pg.estado AS estado_pago, pg.mp_status_detail
        FROM pedidos p
        LEFT JOIN pagos pg ON p.id = pg.pedido_id
        WHERE p.cliente_id = ?
        ORDER BY p.creado_en DESC";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $cliente_id);
$stmt->execute();
$resultado = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Pedidos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-bag-check"></i> Mis Pedidos</h1>
        <a href="index.php" class="btn btn-outline-primary">Volver a la tienda</a>
    </div>

    <?php if ($resultado->num_rows === 0): ?>
        <div class="alert alert-info text-center">
            Aún no has realizado ninguna compra. <br>
            <a href="index.php" class="alert-link">¡Empieza a comprar ahora!</a>
        </div>
    <?php else: ?>
        <div class="table-responsive bg-white shadow-sm rounded">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Pedido #</th>
                        <th>Fecha</th>
                        <th>Total</th>
                        <th>Estado Pedido</th>
                        <th>Estado Pago</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($pedido = $resultado->fetch_assoc()): ?>
                        <tr>
                            <td><strong>#<?php echo $pedido['id']; ?></strong></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($pedido['creado_en'])); ?></td>
                            <td>$<?php echo number_format($pedido['total'], 2); ?></td>
                            <td>
                                <span class="badge <?php 
                                    echo $pedido['estado_pedido'] == 'pagado' ? 'bg-success' : 'bg-warning text-dark'; 
                                ?>">
                                    <?php echo ucfirst($pedido['estado_pedido']); ?>
                                </span>
                            </td>
                            <td>
                                <small class="text-muted">
                                    <?php echo $pedido['estado_pago'] ? ucfirst($pedido['estado_pago']) : 'No iniciado'; ?>
                                    <?php if($pedido['mp_status_detail']) echo " (". $pedido['mp_status_detail'] .")"; ?>
                                </small>
                            </td>
                            <td>
                                <a href="detalle_pedido.php?id=<?php echo $pedido['id']; ?>" class="btn btn-sm btn-info text-white">
                                    <i class="bi bi-eye"></i> Detalles
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
</body>
</html>