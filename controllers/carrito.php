<?php
class CarritoController {
    // Agregar producto al carrito
    public function agregar($id, $cantidad = 1) {
        // Acá podrías usar el Modelo Producto para chequear stock real en DB
        // antes de dejar que el usuario lo sume
        if(!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }
        
        // Lógica para sumar cantidad si ya existe o agregar nuevo
        // ...
    }

    public function vaciar() {
        unset($_SESSION['carrito']);
    }

    public function calcularTotal() {
        $total = 0;
        if(isset($_SESSION['carrito'])) {
            foreach($_SESSION['carrito'] as $item) {
                $total += $item['precio'] * $item['cantidad'];
            }
        }
        return $total;
    }
}
?>