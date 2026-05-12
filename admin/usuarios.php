<?php
// Iniciamos la sesión (obligatorio para usar $_SESSION)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Si la variable de sesión 'admin' no existe, significa que no pasó por el login
if (!isset($_SESSION['admin'])) {
    // Lo mandamos al login para que se identifique
    header("Location: login.php");
    exit();
}
?>