<?php
// 1. Iniciamos la sesión para poder manipularla
session_start();

// 2. Limpiamos todas las variables de sesión
$_SESSION = array();

// 3. Destruimos la sesión por completo
session_destroy();

// 4. Redirigimos al login con un mensaje de "Sesión cerrada"
header("Location: login.php?msg=logout");
exit();
?>