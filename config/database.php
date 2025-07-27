<?php
/*
// config/conexion.php

class Database {
    private $host = "localhost";
    private $db_name = "chat_db";
    private $username = "root";
    private $password = "root"; // Mover a variables de entorno
    private $conn;

    public function getConnection() {
        if ($this->conn === null) {
            try {
                $this->conn = new PDO(
                    "mysql:host={$this->host};dbname={$this->db_name};charset=utf8", 
                    $this->username, 
                    $this->password
                );
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            } catch (PDOException $exception) {
                error_log("Error de conexión: " . $exception->getMessage());
                throw new Exception("Error al conectar con la base de datos");
            }
        }
        return $this->conn;
    }
    
    // Evitar la clonación del objeto (patrón Singleton)
    private function __clone() {}
*/

$host = 'localhost';
$db   = 'chat_db';
$user = 'root';
$pass = 'root';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("DB Connection failed: " . $e->getMessage());
}