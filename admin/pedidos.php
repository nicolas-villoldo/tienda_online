<?php
require_once 'cerrojo.php'; 
require_once "../includes/conexion.php";
require_once "../models/pedido.php"; 

$objPedido = new Pedido($conexion);
$pedidos = $objPedido->listarTodos();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Pedidos - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .btn-copy { padding: 0.1rem 0.4rem; font-size: 0.75rem; transition: 0.2s; }
        .btn-copy:hover { background-color: #0d6efd; color: white; }
        /* Estilo para que los botones de acciones no se peguen */
        .acciones-gap { display: flex; gap: 8px; justify-content: center; }
    </style>
</head>
<body class="pedidos-body">

<div class="container mt-4 mt-md-5 mb-5">
    <div class="d-flex pedidos-header justify-content-between align-items-center mb-4">
        <h1 class="h2 mb-0">📦 Pedidos Recibidos</h1>
        <a href="index.php" class="btn btn-outline-secondary shadow-sm">Volver al Panel</a>
    </div>

    <div class="tabla-pedidos-contenedor shadow-sm">
        <table class="table table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID / Fecha</th>
                    <th>Cliente</th>
                    <th>Productos</th> 
                    <th>WhatsApp</th>
                    <th>Dirección</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($pedidos)): ?>
                    <?php foreach ($pedidos as $p): ?>
                    <tr>
                        <td class="col-id-fecha">
                            <strong>#<?php echo $p['id']; ?></strong><br>
                            <small class="text-muted"><?php echo date('d/m/y H:i', strtotime($p['creado_en'])); ?></small>
                        </td>
                        
                        <td><strong><?php echo htmlspecialchars($p['cliente_nombre'] ?? 'Invitado'); ?></strong></td>

                        <td style="max-width: 200px;">
                            <small class="text-truncate d-block" title="<?php echo htmlspecialchars($p['productos_comprados'] ?? ''); ?>">
                                <?php echo htmlspecialchars($p['productos_comprados'] ?? 'Sin detalle'); ?>
                            </small>
                        </td>
                        
                        <td>
                            <a href="https://wa.me/<?php echo $p['telefono_contacto']; ?>" target="_blank" class="link-whatsapp" style="text-decoration: none;">
                                🟢 <small><?php echo htmlspecialchars($p['telefono_contacto'] ?? '---'); ?></small>
                            </a>
                        </td>

                        <td style="max-width: 200px;">
                            <div class="d-flex align-items-center justify-content-between">
                                <small class="text-truncate" id="dir-<?php echo $p['id']; ?>">
                                    <?php echo htmlspecialchars($p['direccion_envio'] ?? 'Retira en local'); ?>
                                </small>
                                <?php if (!empty($p['direccion_envio']) && $p['direccion_envio'] !== 'Retira en local'): ?>
                                    <button class="btn btn-outline-primary btn-copy ms-2" 
                                            onclick="copiarTexto('dir-<?php echo $p['id']; ?>')" 
                                            title="Copiar Dirección">
                                        <i class="bi bi-clipboard"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </td>

                        <td class="fw-bold text-success">$<?php echo number_format($p['total'], 0, ',', '.'); ?></td>
                        
                        <td>
                            <?php 
                                $color = 'secondary';
                                if ($p['estado'] == 'pendiente') $color = 'warning text-dark';
                                if ($p['estado'] == 'pagado') $color = 'success';
                                if ($p['estado'] == 'enviado') $color = 'info';
                            ?>
                            <span class="badge bg-<?php echo $color; ?>">
                                <?php echo ucfirst($p['estado']); ?>
                            </span>
                        </td>
                        
                        <td class="text-center">
                            <div class="acciones-gap">
                                <a href="editar_pedido.php?id=<?php echo $p['id']; ?>" 
                                   class="btn btn-primary btn-sm fw-bold px-3" title="Editar / Ver">
                                   <i class="bi bi-pencil-square"></i>
                                </a>
                                
                                <a href="eliminar_pedido.php?id=<?php echo $p['id']; ?>" 
                                   class="btn btn-danger btn-sm px-3" 
                                   onclick="return confirm('¿Nico, estás seguro de borrar el pedido #<?php echo $p['id']; ?>? Esto no se puede deshacer.');"
                                   title="Eliminar Pedido">
                                   <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <h4 class="h6">Aún no hay pedidos registrados.</h4>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function copiarTexto(idElemento) {
    const texto = document.getElementById(idElemento).innerText.trim();
    navigator.clipboard.writeText(texto).then(() => {
        alert("Dirección copiada: " + texto);
    }).catch(err => {
        console.error('Error al copiar: ', err);
    });
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>