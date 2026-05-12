<?php
echo "Archivo de configuración cargado: " . php_ini_loaded_file() . "<br>";
echo "Directorio de extensiones: " . ini_get('extension_dir') . "<br>";
echo "Mysqli cargado: " . (extension_loaded('mysqli') ? 'SÍ' : 'NO');
?>