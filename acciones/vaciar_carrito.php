<?php
session_start();

// 1. Borramos el contenido del carrito
if (isset($_SESSION['carrito'])) {
    unset($_SESSION['carrito']);
}

// 2. Redirigimos al index principal
// Usamos ../ para salir de la carpeta 'acciones'
header("Location: ../index_principal.php");
exit();
?>