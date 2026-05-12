<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Error en el pago</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow border-0 text-center p-5">
            <h1 class="display-4 text-danger">El pago no se concretó ❌</h1>
            <p class="lead">Hubo un problema al procesar tu tarjeta o el pago fue cancelado.</p>
            <hr>
            <p>No te preocupes, tus productos siguen en el carrito.</p>
            <div class="d-grid gap-2 d-md-block">
                <a href="carrito.php" class="btn btn-warning btn-lg">Reintentar Pago</a>
                <a href="index_principal.php" class="btn btn-outline-secondary btn-lg">Ir al Inicio</a>
            </div>
        </div>
    </div>
</body>
</html>