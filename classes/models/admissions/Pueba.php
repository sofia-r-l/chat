<?php


class Pueba {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    public function getTodasCarreras() {
        $stmt = $this->db->prepare("SELECT id_carrera, nombre_carrera FROM tbl_carreras");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}