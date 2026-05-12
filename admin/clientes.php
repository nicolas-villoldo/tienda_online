<?php
include("../includes/conexion.php");

// Traer todos los clientes - Quitamos 'correo' de la consulta
// Nota: cambié fecha_registro por creado_en que es como figura en  estructura SQL
$sql = "SELECT id, nombre, telefono, direccion, creado_en FROM clientes ORDER BY creado_en DESC";
$resultado = $conexion->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Clientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2">👥 Administración de Clientes</h1>
        <a href="index.php" class="btn btn-outline-secondary btn-sm">Volver al Panel</a>
    </div>

    <div class="table-responsive shadow-sm">
        <table class="table table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Teléfono (WhatsApp)</th>
                    <th>Dirección</th>
                    <th>Fecha Registro</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($resultado && $resultado->num_rows > 0): ?>
                    <?php while ($cliente = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><strong>#<?php echo $cliente['id']; ?></strong></td>
                        <td><?php echo htmlspecialchars($cliente['nombre']); ?></td>
                        <td>
                            <a href="https://wa.me/<?php echo $cliente['telefono']; ?>" target="_blank" class="text-decoration-none">
                                🟢 <?php echo htmlspecialchars($cliente['telefono'] ?? '---'); ?>
                            </a>
                        </td>
                        <td><?php echo htmlspecialchars($cliente['direccion'] ?? 'No especificada'); ?></td>
                        <td><small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($cliente['creado_en'])); ?></small></td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="editar_cliente.php?id=<?php echo $cliente['id']; ?>" 
                                   class="btn btn-sm btn-outline-primary">
                                   <i class="bi bi-pencil"></i> Editar
                                </a>
                                <a href="eliminar_cliente.php?id=<?php echo $cliente['id']; ?>" 
                                   class="btn btn-sm btn-outline-danger" 
                                   onclick="return confirm('¿Seguro que quieres eliminar a <?php echo $cliente['nombre']; ?>?');">
                                   <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">No hay clientes registrados aún.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>