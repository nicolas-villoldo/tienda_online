<?php
class Usuario {
    private $conexion;

    public function __construct($db) {
        $this->conexion = $db;
    }

    // Para entrar al panel de Admin usando el nombre de usuario
    public function login($username, $password) {
        // Ajustado a la columna 'usuario' de tu DB
        $sql = "SELECT * FROM usuarios WHERE usuario = ?";
        $stmt = $this->conexion->prepare($sql);
        
        if (!$stmt) {
            die("Error en Prepare (Usuario): " . $this->conexion->error);
        }

        $stmt->bind_param("s", $username);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if ($usuario = $resultado->fetch_assoc()) {
            // Verificamos la contraseña hasheada
            if (password_verify($password, $usuario['password'])) {
                return $usuario;
            }
        }
        return false;
    }
}
?>