<?php
session_start();
require_once '../includes/conexion.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = trim($_POST['user']);
    $pass = $_POST['pass'];

    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }

    $sql = "SELECT * FROM usuarios WHERE usuario = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $datos = $resultado->fetch_assoc();
        
        if (password_verify($pass, $datos['password'])) {
            $_SESSION['admin'] = $datos['usuario']; 
            $_SESSION['usuario_nombre'] = $datos['usuario']; 
            
            session_write_close(); 
            header("Location: index.php");
            exit();
        } else {
            $error = "Contraseña incorrecta. Revisá que no haya espacios.";
        }
    } else {
        $error = "El usuario '$user' no existe.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* PEGÁ EL CSS ACÁ ADENTRO DIRECTO */
        body.login-body { background: #f0f2f5 !important; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
        .login-card { width: 100%; max-width: 400px; padding: 40px; background: white !important; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        .btn-login { width: 100%; padding: 12px; background-color: #009ee3 !important; color: white !important; border: none; border-radius: 8px; font-weight: bold; margin-top: 10px; }
    </style>
</head>
<body class="login-body">
    <div class="login-card">
        <h3>Admin Login</h3>
        <form method="POST">
            <div class="mb-3">
                <label>Usuario:</label>
                <input type="text" name="user" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Contraseña:</label>
                <input type="password" name="pass" class="form-control" required>
            </div>
            <button type="submit" class="btn-login">Entrar al Panel</button>
        </form>
    </div>
</body>
</html>