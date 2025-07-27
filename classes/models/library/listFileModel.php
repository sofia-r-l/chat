<?php
require_once dirname(__DIR__, 1) . '/db_scripts/database.php';

class BookModel {

    public static function obtenerLibrosActivos() {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT id, titulo, autor, fecha_creacion 
                                FROM libros 
                                WHERE estado = 'activo' 
                                ORDER BY fecha_creacion DESC"
                                );
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
