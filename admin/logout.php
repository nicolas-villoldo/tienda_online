<?php
session_start();
// Destruimos todas las variables de sesión del servidor
session_destroy();
// Lo mandamos de vuelta al login
header("Location: ../index_principal.php");
exit();
?>