
<?php
// config/conexion.php

$host = 'localhost';        // Cambia por tu host si usas otro
$dbname = 'Prueba';    // Cambia por el nombre de tu base
$user = 'root';          // Tu usuario de BD
$pass = 'ff';       // Tu contraseña de BD

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
} 

class Database {
    private $host = "localhost";
    private $db_name = "Prueba";
    private $username = "root";
    private $password = "Sistemas_12@";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host={$this->host};dbname={$this->db_name};charset=utf8", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            echo "Error de conexión: " . $exception->getMessage();
            exit;
        }
        return $this->conn;
    }
}
