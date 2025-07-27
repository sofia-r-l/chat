<?php
class Mensaje {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function obtenerMensajes($emisor, $receptor) {
        $query = "SELECT emisor_id AS user, mensaje, fecha_envio AS date
                  FROM mensajes
                  WHERE (emisor_id = ? AND receptor_id = ?) OR (emisor_id = ? AND receptor_id = ?)
                  ORDER BY fecha_envio ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$emisor, $receptor, $receptor, $emisor]);
        return $stmt->fetchAll();
    }

    public function insertarMensaje($emisor, $receptor, $mensaje) {
        $query = "INSERT INTO mensajes (emisor_id, receptor_id, mensaje) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$emisor, $receptor, $mensaje]);
    }
}