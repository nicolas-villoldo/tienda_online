<?php
// Usá el archivo de conexión que ya tenés creado para no repetir código
require_once '../includes/conexion.php'; 

// Verificamos si la tabla existe (por si usás otro nombre de DB)
$conexion->query("CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
)");

$user = 'nico';
$pass = '2/12/2006'; 
$pass_hash = password_hash($pass, PASSWORD_DEFAULT); // PASSWORD_DEFAULT es más flexible a futuro

// Limpiamos la tabla para que no haya duplicados del usuario 'nico'
$conexion->query("TRUNCATE TABLE usuarios");

// Usamos una sentencia preparada para que sea más limpio
$stmt = $conexion->prepare("INSERT INTO usuarios (usuario, password) VALUES (?, ?)");
$stmt->bind_param("ss", $user, $pass_hash);

if ($stmt->execute()) {
    echo "<div style='font-family: sans-serif; text-align: center; margin-top: 50px;'>";
    echo "<h1 style='color: #28a745;'>✅ USUARIO CREADO CON ÉXITO</h1>";
    echo "<p>Usuario: <b>$user</b><br>Pass: <b>$pass</b></p>";
    echo "<p style='color: red;'>⚠️ <b>IMPORTANTE:</b> Borrá este archivo (<i>crear_usuario.php</i>) ahora mismo por seguridad.</p>";
    echo "<a href='login.php' style='display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px;'>Ir al Login</a>";
    echo "</div>";
} else {
    echo "❌ ERROR: " . $conexion->error;
}
?>