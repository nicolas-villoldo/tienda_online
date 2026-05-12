<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Usamos 'admin', que es lo que definiste en tu login.php
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
?>