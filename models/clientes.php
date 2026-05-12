<?php
class Cliente {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    // 1. Registrar nuevo cliente (Sin Correo)
    public function crear($nombre, $clave_hash, $telefono, $direccion) {
        // Quitamos 'correo' del INSERT
        $sql = "INSERT INTO clientes (nombre, clave_hash, telefono, direccion) VALUES (?, ?, ?, ?)";
        $stmt = $this->conexion->prepare($sql);
        
        if (!$stmt) {
            die("Error en Prepare (Cliente): " . $this->conexion->error);
        }

        // "ssss" = 4 strings: nombre, clave, telefono, direccion
        $stmt->bind_param("ssss", $nombre, $clave_hash, $telefono, $direccion);
        return $stmt->execute();
    }

    // 2. AHORA BUSCAMOS POR TELÉFONO (Reemplaza a obtenerPorCorreo)
    public function obtenerPorTelefono($telefono) {
        $sql = "SELECT * FROM clientes WHERE telefono = ?";
        $stmt = $this->conexion->prepare($sql);
        
        if (!$stmt) {
            die("Error en Prepare (Obtener Cliente): " . $this->conexion->error);
        }

        $stmt->bind_param("s", $telefono);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        return $resultado->fetch_assoc();
    }

    // 3. Buscar cliente por ID
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM clientes WHERE id = ?";
        $stmt = $this->conexion->prepare($sql);
        
        if (!$stmt) {
            die("Error en Prepare (Obtener por ID): " . $this->conexion->error);
        }

        $stmt->bind_param("i", $id); 
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        return $resultado->fetch_assoc();
    }

    // 4. Actualizar datos (Sin Correo)
    public function actualizar($id, $nombre, $telefono, $direccion) {
        // Quitamos 'correo' del UPDATE
        $sql = "UPDATE clientes SET nombre = ?, telefono = ?, direccion = ? WHERE id = ?";
        $stmt = $this->conexion->prepare($sql);
        
        if (!$stmt) {
            die("Error en Prepare (Actualizar Cliente): " . $this->conexion->error);
        }

        // "sssi" = 3 textos y el ID final que es número
        $stmt->bind_param("sssi", $nombre, $telefono, $direccion, $id);
        return $stmt->execute();
    }

    // 5. Listar todos
    public function listarTodos() {
        $sql = "SELECT * FROM clientes ORDER BY id DESC";
        $resultado = $this->conexion->query($sql);
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }
}
?>