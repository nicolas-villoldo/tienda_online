<?php
// 1. Configuración
if (!defined('DB_HOST')) define('DB_HOST', '127.0.0.1'); 
if (!defined('DB_USER')) define('DB_USER', 'root');
if (!defined('DB_PASS')) define('DB_PASS', ''); 
if (!defined('DB_NAME')) define('DB_NAME', 'tienda_online');
if (!defined('DB_PORT')) define('DB_PORT', 3306); 

// Credenciales Extra
if (!defined('MP_TOKEN')) define('MP_TOKEN', 'APP_USR-2e175033-ab17-48e3-8e3b-88ce10e4d87d');
if (!defined('BASE_URL')) define('BASE_URL', 'https://unsedimental-alycia-precollusive.ngrok-free.dev/tienda_online');

/**
 * CLASE ORIGINAL (MySQLi)
 * Se mantiene para compatibilidad con el resto del sitio
 */
class Database {
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $db   = DB_NAME;
    private $port = DB_PORT;
    public $conn;

    public function __construct() {
        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->db, $this->port);

        if ($this->conn->connect_error) {
            die("Error de conexión MySQLi: " . $this->conn->connect_error);
        }

        $this->conn->set_charset("utf8");
    }

    public function consulta($sql) {
        $resultado = $this->conn->query($sql);
        if (!$resultado) {
            die("Error en la consulta: " . $this->conn->error);
        }
        return $resultado;
    }

    public function cerrar() {
        $this->conn->close();
    }
}

// Instancia para scripts que usen MySQLi
$db = new Database();
$conexion = $db->conn;

/**
 * NUEVA FUNCIÓN (PDO)
 * Necesaria para procesar_pago.php y transacciones seguras
 */
function conectarPDO() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";port=" . DB_PORT . ";charset=utf8mb4";
        $opciones = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        return new PDO($dsn, DB_USER, DB_PASS, $opciones);
    } catch (PDOException $e) {
        die("Error crítico de conexión PDO: " . $e->getMessage());
    }
}
?>