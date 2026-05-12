<?php
require_once 'cerrojo.php';
require_once '../includes/conexion.php';
require_once '../models/pedido.php'; 

$objPedido = new Pedido($conexion);
$id = intval($_GET['id'] ?? 0);

$pedido = $objPedido->obtenerPorId($id);

if (!$pedido) {
    header("Location: pedidos.php");
    exit();
}

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevoEstado = $_POST['estado'];
    $res = $objPedido->actualizarEstado($id, $nuevoEstado);

    if ($res) {
        $mensaje = "<div class='alert alert-success shadow-sm'>✅ Estado actualizado a: <strong>" . ucfirst($nuevoEstado) . "</strong></div>";
        $pedido = $objPedido->obtenerPorId($id); // Recargamos datos frescos
    } else {
        // Si falla, mostramos el error técnico de la conexión para saber por qué
        $error_detalle = mysqli_error($conexion);
        $mensaje = "<div class='alert alert-danger shadow-sm'>❌ Error al actualizar: " . $error_detalle . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Pedido #<?php echo $id; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="bg-gestion">

<div class="container mt-4 mt-md-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8">
            <div class="card card-pedido shadow-lg p-3 p-md-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
                    <h2 class="mb-2 mb-md-0 text-primary h4">📦 Gestión de Pedido #<?php echo $id; ?></h2>
                    <span class="badge bg-dark px-3 py-2">ID: <?php echo $id; ?></span>
                </div>
                
                <?php echo $mensaje; ?>

                <div class="row bg-light p-3 rounded mb-4 mx-0">
                    <div class="col-6 mb-3">
                        <p class="info-label">Total del Pedido</p>
                        <p class="info-value text-success">$<?php echo number_format($pedido['total'], 2, ',', '.'); ?></p>
                    </div>
                    <div class="col-6 mb-3">
                        <p class="info-label">Fecha</p>
                        <p class="info-value small"><?php echo date('d/m/Y H:i', strtotime($pedido['creado_en'])); ?></p>
                    </div>
                    <div class="col-12">
                        <p class="info-label">Dirección de Envío</p>
                        <p class="info-value"><?php echo htmlspecialchars($pedido['direccion_envio'] ?? 'Retira en local'); ?></p>
                    </div>
                </div>

                <form method="post">
                    <div class="mb-4">
                        <label for="estado" class="form-label fw-bold text-secondary">Estado Logístico</label>
                        <select name="estado" id="estado" class="form-select form-select-lg border-primary">
                            <option value="pendiente" <?php echo ($pedido['estado'] == 'pendiente') ? 'selected' : ''; ?>>⏳ Pendiente</option>
                            <option value="pagado"    <?php echo ($pedido['estado'] == 'pagado')    ? 'selected' : ''; ?>>💰 Pagado</option>
                            <option value="enviado"   <?php echo ($pedido['estado'] == 'enviado')   ? 'selected' : ''; ?>>🚚 Enviado</option>
                            <option value="entregado" <?php echo ($pedido['estado'] == 'entregado') ? 'selected' : ''; ?>>✅ Entregado</option>
                        </select>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="pedidos.php" class="btn btn-outline-secondary px-4">Volver</a>
                        <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>