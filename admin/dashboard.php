<?php
require_once 'cerrojo.php';
// Ya no necesitamos la conexión acá si el procesamiento se hace en otro lado, 
// pero la dejamos por si tenés algún menú dinámico arriba.
require_once '../includes/conexion.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cargar Producto - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card-header { background-color: #28a745 !important; }
        .btn-success { background-color: #28a745; border: none; }
    </style>
</head>
<body class="bg-light">

<div class="container mt-4 mb-5">
    <div class="card shadow">
        <div class="card-header text-white text-center">
            <h3 class="mb-0">👟 Cargar Nuevo Producto</h3>
        </div>
        
        <div class="card-body p-4">
            <form action="guardar_producto.php" method="POST" enctype="multipart/form-data">
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Nombre del Producto</label>
                    <input type="text" name="nombre" class="form-control" placeholder="Ej: Nike Air Jordan" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="3" placeholder="Detalles de la zapatilla..."></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Precio (ARS)</label>
                        <input type="number" step="0.01" name="precio" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Stock Inicial</label>
                        <input type="number" name="stock" class="form-control" value="1" required>
                    </div>
                </div>

                <div class="mb-3 border-top pt-3">
                    <label class="form-label fw-bold">Imagen del Producto</label>
                    <input type="file" name="imagen" class="form-control" accept="image/*" required>
                </div>

                <div class="d-flex justify-content-between mt-4 border-top pt-3">
                    <a href="index.php" class="btn btn-outline-secondary">Volver al Panel</a>
                    <button type="submit" class="btn btn-success px-5 fw-bold">Guardar Producto</button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
