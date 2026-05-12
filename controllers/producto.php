<?php
require_once __DIR__ . '/../includes/conexion.php';
require_once dirname(__DIR__) . '/models/productos.php';

class ProductoController {
    private $modelo;

    public function __construct($conexion) {
        $this->modelo = new Producto($conexion);
    }

    public function index() {
        return $this->modelo->obtenerTodos();
    }

    // AHORA recibe solo 5 parámetros: nombre, descripcion, precio, stock e imagen
    public function guardar($nombre, $descripcion, $precio, $stock, $imagen) {
        return $this->modelo->crear($nombre, $descripcion, $precio, $stock, $imagen);
    }
}

$controller = new ProductoController($conexion);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    if ($_POST['accion'] === 'crear') {
        
        // --- LÓGICA PARA LA IMAGEN ---
        $nombre_imagen = "default.jpg"; 
        
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
            $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
            $nuevo_nombre = time() . "_" . uniqid() . "." . $ext;
            $destino = "../Imagen/" . $nuevo_nombre;

            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $destino)) {
                $nombre_imagen = $nuevo_nombre;
            }
        }

        // --- CAPTURA DE DATOS (SIN $CAT) ---
        $res = $controller->guardar(
            $_POST['nombre'],
            $_POST['descripcion'],
            $_POST['precio'],
            $_POST['stock'],
            $nombre_imagen 
        );

        if ($res) {
            header("Location: ../admin/productos.php?msg=creado");
            exit;
        } else {
            echo "Error al guardar en la base de datos.";
        }
    }
}