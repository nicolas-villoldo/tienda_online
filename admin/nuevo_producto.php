<?php
require_once 'cerrojo.php';
require_once '../includes/conexion.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Producto - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="bg-light">

<div class="container container-nuevo mt-4 mt-md-5 mb-5">
    <div class="card card-nuevo-producto shadow">
        <div class="card-header text-white text-center">
            <h3 class="mb-0 h4"> Cargar Nuevo Producto</h3>
        </div>
        
        <div class="card-body p-3 p-md-4">
            <form action="guardar_producto.php" method="POST" enctype="multipart/form-data">
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Nombre del Producto</label>
                    <input type="text" name="nombre" class="form-control form-control-lg" placeholder="" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="3" placeholder="Detalles, materiales, talles..."></textarea>
                </div>

                <div class="row">
                    <div class="col-12 col-md-6 mb-3">
                        <label class="form-label fw-bold">Precio (ARS)</label>
                        <input type="number" step="0.01" name="precio" class="form-control form-control-lg" placeholder="0.00" required>
                    </div>

                    <div class="col-12 col-md-6 mb-3">
                        <label class="form-label fw-bold">Stock Inicial</label>
                        <input type="number" name="stock" class="form-control form-control-lg" value="1" required>
                    </div>
                </div>

                <div class="mb-4 border-top pt-3">
                    <label class="form-label fw-bold">Imagen del Producto</label>
                    <input type="file" name="imagen" class="form-control" accept="image/*" required>
                    <div class="form-text mt-2">Subí una foto clara del producto.</div>
                </div>

                <div class="d-flex btn-container-mobile justify-content-between mt-4 border-top pt-3">
                    <a href="index.php" class="btn btn-outline-secondary">Volver al Panel</a>
                    <button type="submit" class="btn btn-guardar-nuevo btn-success fw-bold shadow-sm">Guardar Producto</button>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>