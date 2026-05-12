<?php
require_once 'cerrojo.php';
require_once '../includes/conexion.php';
require_once '../models/clientes.php'; // Traemos la clase blindada

$objCliente = new Cliente($conexion);

// Obtenemos el ID de la URL y buscamos al cliente usando el objeto
$id = intval($_GET['id'] ?? 0);
$cliente = $objCliente->obtenerPorId($id);

// Si el cliente no existe, volvemos a la lista
if (!$cliente) {
    header("Location: clientes.php");
    exit();
}

$mensaje = "";

// Procesamos el formulario si se envió por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Usamos el método 'actualizar' de la clase, que ya tiene el bind_param
    $res = $objCliente->actualizar(
        $id, 
        $_POST['nombre'], 
        $_POST['correo'], 
        $_POST['telefono'], 
        $_POST['direccion']
    );

    if ($res) {
        $mensaje = "<div class='alert alert-success'>✅ Cliente actualizado correctamente</div>";
        // Recargamos los datos del cliente para que el formulario se actualice visualmente
        $cliente = $objCliente->obtenerPorId($id);
    } else {
        $mensaje = "<div class='alert alert-danger'>❌ Error al actualizar los datos</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cliente - Leguizamo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">
    <div class="card shadow p-4 border-0">
        <h1 class="mb-4">✏️ Editar Cliente</h1>
        
        <?php echo $mensaje; ?>

        <form method="post">
            <div class="mb-3">
                <label class="form-label fw-bold">Nombre</label>
                <input type="text" name="nombre" class="form-control" 
                       value="<?php echo htmlspecialchars($cliente['nombre']); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Correo</label>
                <input type="email" name="correo" class="form-control" 
                       value="<?php echo htmlspecialchars($cliente['correo']); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Teléfono</label>
                <input type="text" name="telefono" class="form-control" 
                       value="<?php echo htmlspecialchars($cliente['telefono']); ?>">
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Dirección</label>
                <textarea name="direccion" class="form-control" rows="3"><?php echo htmlspecialchars($cliente['direccion']); ?></textarea>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-success px-4 fw-bold">Guardar cambios</button>
                <a href="clientes.php" class="btn btn-outline-secondary px-4">Volver</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>