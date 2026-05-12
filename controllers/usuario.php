<?php
include("../includes/conexion.php");
include("../models/cliente.php");

class Usuario {
    private $modelo;

    public function __construct($conexion) {
        $this->modelo = new Cliente($conexion);
    }

    public function registrar($nombre, $correo, $clave, $telefono, $direccion) {
        $clave_hash = password_hash($clave, PASSWORD_DEFAULT);
        return $this->modelo->crear($nombre, $correo, $clave_hash, $telefono, $direccion);
    }

    public function login($correo, $clave) {
        $cliente = $this->modelo->obtenerPorCorreo($correo);
        if ($cliente && password_verify($clave, $cliente['clave_hash'])) {
            return $cliente;
        }
        return null;
    }
}
?>
