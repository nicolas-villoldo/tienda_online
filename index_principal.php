<?php
// Reporte de errores para desarrollo
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Usamos rutas relativas limpias
require_once "includes/conexion.php"; 
require_once "models/productos.php"; 

// Si usaste el conexion.php que te pasé con la clase Database
$productoModel = new Producto($conexion);
$lista_productos = $productoModel->obtenerTodos(); 

$total = 0;
if (!empty($_SESSION['carrito'])) {
    foreach ($_SESSION['carrito'] as $producto) {
        $total += ($producto['precio'] * $producto['cantidad']);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xotic.tragos | Tienda Online</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        body { background-color: #121212; color: #e0e0e0; font-family: 'Segoe UI', sans-serif; margin: 0; padding: 0; }
        h1 { text-align: center; padding: 25px 0; margin: 0; background: #000; color: #f1c40f; text-transform: uppercase; letter-spacing: 4px; border-bottom: 2px solid #e74c3c; }
        
        .hero-banner {
            width: 100%; height: 350px; 
            background-image: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.8)), url('Imagen/ferned.jpeg'); 
            background-size: cover; background-position: center; display: flex; align-items: center; justify-content: center; border-bottom: 3px solid #f1c40f;
        }

        .container { max-width: 1200px; margin: 30px auto; padding: 0 20px; display: flex; flex-wrap: wrap; gap: 20px; }

        .productos-grid { flex: 2; display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; }
        .producto-card { background: #1e1e1e; border: 1px solid #333; border-radius: 12px; padding: 15px; text-align: center; }
        .btn-agregar { background: #f1c40f; color: #000; font-weight: bold; border: none; width: 100%; padding: 10px; cursor: pointer; border-radius: 6px; margin-top: 10px; }

        .carrito-sidebar { flex: 1; min-width: 320px; background: #1e1e1e; border: 2px solid #333; border-radius: 15px; padding: 20px; height: fit-content; position: sticky; top: 10px; }
        .input-xotic { width: 100%; margin-bottom: 10px; padding: 12px; background: #121212; border: 1px solid #444; color: #fff; border-radius: 8px; box-sizing: border-box; }
        
        .btn-confirmar { width: 100%; padding: 15px; background: #27ae60; color: white; border: none; font-weight: bold; cursor: pointer; border-radius: 10px; font-size: 1.1em; transition: 0.3s; }
        .btn-confirmar:hover { background: #219150; }
        .btn-confirmar:disabled { background: #444; cursor: not-allowed; opacity: 0.6; }

        .btn-whatsapp { position: fixed; width: 60px; height: 60px; bottom: 30px; right: 30px; background-color: #25d366; color: #FFF; border-radius: 50px; text-align: center; font-size: 30px; display: flex; align-items: center; justify-content: center; z-index: 999; }
    </style>
</head>
<body>

    <h1>Xotic.tragos</h1>

    <div class="hero-banner">
        <h2 style="font-size: 3em; color: white; text-shadow: 2px 2px 10px #000;">XOTIC FOOD & DRINKS</h2>
    </div>

    <div class="container">
        
        <!-- GRILLA DE PRODUCTOS -->
        <div class="productos-grid">
            <?php foreach($lista_productos as $p): ?>
                <div class="producto-card">
                    <img src="Imagen/<?php echo $p['imagen']; ?>" style="width: 100%; height: 160px; object-fit: cover; border-radius: 8px;">
                    <h3 style="color: #f1c40f; margin: 10px 0;"><?php echo htmlspecialchars($p['nombre']); ?></h3>
                    <p style="font-weight: bold; font-size: 1.2em; margin: 5px 0;">$<?php echo number_format($p['precio'], 0, ',', '.'); ?></p>
                    <form action="acciones/carrito.php" method="POST">
                        <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                        <button type="submit" class="btn-agregar">Agregar</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- SIDEBAR DEL CARRITO -->
        <div class="carrito-sidebar">
            <h3 style="margin-top: 0; color: #f1c40f; border-bottom: 1px solid #333; padding-bottom: 10px;">🛒 Tu Pedido</h3>
            
            <div style="margin-bottom: 20px;">
                <?php if (!empty($_SESSION['carrito'])): ?>
                    <?php foreach($_SESSION['carrito'] as $item): ?>
                        <div style="display: flex; justify-content: space-between; padding: 5px 0; font-size: 0.9em; border-bottom: 1px dashed #444;">
                            <span><?php echo $item['nombre']; ?> x<?php echo $item['cantidad']; ?></span>
                            <span>$<?php echo number_format($item['precio'] * $item['cantidad'], 0, ',', '.'); ?></span>
                        </div>
                    <?php endforeach; ?>
                    <p style="text-align: right; color: #f1c40f; font-size: 1.4em; font-weight: bold; margin-top: 15px;">Total: $<?php echo number_format($total, 0, ',', '.'); ?></p>
                <?php else: ?>
                    <p style="color: #888; font-style: italic; background: #252525; padding: 10px; border-radius: 5px;">El carrito está vacío. ¡Elegí algo rico!</p>
                <?php endif; ?>
            </div>

            <!-- FORMULARIO DE ENVÍO -->
            <!-- IMPORTANTE: Revisá que la carpeta se llame 'tienda' y el archivo 'procesar_pago.php' -->
            <form action="TIENDA_ONLINE/tienda/procesar_pago.php" method="POST">
                <p style="font-size: 0.8em; color: #f1c40f; margin-bottom: 5px; text-transform: uppercase;">1. Tus Datos</p>
                <input type="text" name="nombre" placeholder="Nombre completo" required class="input-xotic">
                <input type="text" name="telefono" placeholder="WhatsApp (sin 0 ni 15)" required class="input-xotic">
                <input type="text" name="direccion" placeholder="Dirección de entrega" required class="input-xotic">
                
                <p style="font-size: 0.8em; color: #f1c40f; margin: 15px 0 5px 0; text-transform: uppercase;">2. Pago</p>
                <div style="background: #121212; padding: 10px; border-radius: 8px; margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; cursor: pointer;">
                        <input type="radio" name="metodo_pago" value="mercadopago" checked> 💳 Mercado Pago
                    </label>
                    <label style="display: block; cursor: pointer;">
                        <input type="radio" name="metodo_pago" value="efectivo"> 💵 Efectivo al recibir
                    </label>
                </div>

                <button type="submit" class="btn-confirmar" <?php echo ($total == 0) ? 'disabled' : ''; ?>>
                    Confirmar Pedido 
                </button>
            </form>

            <?php if ($total > 0): ?>
                <a href="acciones/vaciar_carrito.php" style="display: block; text-align: center; margin-top: 15px; color: #666; text-decoration: none; font-size: 0.8em;">Vaciar pedido actual</a>
            <?php endif; ?>
        </div>
    </div>

    <a href="https://wa.me/5491169620468" class="btn-whatsapp" target="_blank">
        <i class="fab fa-whatsapp"></i>
    </a>

</body>
</html>