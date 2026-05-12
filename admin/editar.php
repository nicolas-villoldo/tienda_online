<?php
require_once 'cerrojo.php';
require_once '../includes/conexion.php';
require_once '../models/productos.php'; 

$objProducto = new Producto($conexion);

$id = intval($_GET['id'] ?? 0);
$p = $objProducto->obtenerPorId($id);

if (!$p) {
    header("Location: index.php");
    exit();
}

if (isset($_POST['actualizar'])) {
    $id_update = intval($_POST['id']);
    $nombre    = $_POST['nombre'];
    $precio    = $_POST['precio'];
    $stock     = $_POST['stock'];
    $descripcion = $_POST['descripcion'];

    if ($objProducto->actualizar($id_update, $nombre, $descripcion, $precio, $stock)) {
        header("Location: index.php?mensaje=actualizado");
        exit();
    } else {
        $error = "❌ Error al actualizar el producto.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto - AvanoAdmin</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="body-admin">

<div class="container-form">
    <h2>Editar Producto</h2>
    <p style="color: #666; font-size: 0.9em; text-align: left;">ID: #<?php echo $p['id']; ?></p>
    
    <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

    <form method="POST">
        <input type="hidden" name="id" value="<?php echo $p['id']; ?>">

        <label>Nombre del Producto:</label>
        <input type="text" name="nombre" value="<?php echo htmlspecialchars($p['nombre']); ?>" required>

        <label>Descripción Detallada:</label>
        <textarea name="descripcion" rows="4"><?php echo htmlspecialchars($p['descripcion'] ?? ''); ?></textarea>

        <label>Precio (ARS):</label>
        <input type="number" step="0.01" name="precio" value="<?php echo $p['precio']; ?>" required>

        <label>Stock Disponible:</label>
        <input type="number" name="stock" value="<?php echo $p['stock']; ?>" required>

        <div class="btn-container">
            <button type="submit" name="actualizar" class="btn-guardar">Guardar Cambios</button>
            <a href="index.php" class="btn-cancelar-link">Cancelar</a>
        </div>
    </form>
</div>

</body>
</html>