<?php
class Producto {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    // 1. Obtener todos los productos (Para el Index del Admin)
    public function obtenerTodos() {
        $sql = "SELECT * FROM productos ORDER BY id DESC";
        $resultado = $this->conexion->query($sql);
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    // 2. Buscar producto por ID (Para cargar los datos en el formulario de edición)
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM productos WHERE id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->fetch_assoc();
    }

    // 3. Crear producto (Blindado)
    public function crear($nombre, $descripcion, $precio, $stock, $imagen) {
        $sql = "INSERT INTO productos (nombre, descripcion, precio, stock, imagen) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conexion->prepare($sql);
        
        if (!$stmt) {
            die("Error en SQL (Prepare): " . $this->conexion->error);
        }

        // s=nombre, s=desc, d=precio, i=stock, s=imagen
        $stmt->bind_param("ssdis", $nombre, $descripcion, $precio, $stock, $imagen);
        return $stmt->execute();
    }

    // 4. NUEVO: Actualizar producto (Para que funcione tu archivo de edición)
    public function actualizar($id, $nombre, $descripcion, $precio, $stock) {
        $sql = "UPDATE productos SET nombre = ?, descripcion = ?, precio = ?, stock = ? WHERE id = ?";
        $stmt = $this->conexion->prepare($sql);
        
        if (!$stmt) {
            die("Error en Prepare (Actualizar Producto): " . $this->conexion->error);
        }

        // s=nombre, s=desc, d=precio, i=stock, i=id
        $stmt->bind_param("ssdii", $nombre, $descripcion, $precio, $stock, $id);
        return $stmt->execute();
    }

    // 5. NUEVO: Borrar producto (Por si querés limpiar el stock viejo)
    public function borrar($id) {
        $sql = "DELETE FROM productos WHERE id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>