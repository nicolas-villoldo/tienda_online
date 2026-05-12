<?php
require_once 'cerrojo.php';
require_once '../includes/conexion.php';
require_once '../models/productos.php'; 

$objProducto = new Producto($conexion);
$productos = $objProducto->obtenerTodos();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Leguizamo</title>
    
    <!-- Bootstrap (Opcional, pero ayuda) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- RUTA ABSOLUTA: Esto ignora dónde esté el archivo PHP parado -->
    <link rel="stylesheet" href="/tienda_online/css/admin.css?v=<?php echo time(); ?>">
</head>
<body class="admin-body">

    <h1>Panel de Administración</h1>
    
    <div class="admin-header">
        <span>Bienvenido, <strong>Admin</strong></span>
        <a href="logout.php" style="color: red; font-weight: bold; text-decoration: none;">Cerrar Sesión</a>
    </div>

    <div class="contenedor-botones">
        <a href="nuevo_producto.php" class="btn-link btn-verde">+ Nuevo Producto</a>
        <a href="pedidos.php" class="btn-link btn-azul">Ver Pedidos</a>
    </div>

    <div class="tabla-contenedor">
        <table>
            <thead>
                <tr>
                    <th>Imagen</th>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($productos as $p): ?>
                <tr>
                    <td>
                        <?php if(!empty($p['imagen'])): ?>
                            <img src="../Imagen/<?php echo $p['imagen']; ?>" width="50" style="border-radius: 5px; height: 50px; object-fit: cover;">
                        <?php else: ?>
                            <small style="color: #999;">Sin foto</small>
                        <?php endif; ?>
                    </td>
                    <td><strong><?php echo htmlspecialchars($p['nombre']); ?></strong></td>
                    <td><span class="precio-negro">$<?php echo number_format($p['precio'], 0, ',', '.'); ?></span></td>
                    <td><?php echo $p['stock']; ?> un.</td>
                    <td>
                        <a href="editar.php?id=<?php echo $p['id']; ?>" class="accion-btn btn-editar">Editar</a>
                        <a href="borrar.php?id=<?php echo $p['id']; ?>" class="accion-btn btn-borrar" onclick="return confirm('¿Seguro que querés borrar este producto?')">Borrar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</body>
</html>