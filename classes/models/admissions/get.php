<?php
require_once __DIR__ . "/../../../config/db_admissions_config.php";

/**
 * Modelo para operaciones de consulta relacionadas con solicitudes y revisores.
 */
class GetModel {
    /**
     * Instancia de PDO para la conexión a la base de datos.
     * @var PDO
     */
    private $pdo;

    /**
     * Constructor del modelo.
     * @param PDO $pdo
     */
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Obtiene la primer solicitud pendiente para un revisor específico usando el procedimiento almacenado.
     * @param int $revisorId
     * @return array|null
     */
    public function getPrimeraSolicitudPendientePorRevisorSP($revisorId)
    {
        try {
            
            $stmt = $this->pdo->prepare("CALL get_primera_solicitud_pendiente(:revisorId)");
            $stmt->bindParam(':revisorId', $revisorId, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor(); // Importante para procedimientos almacenados
            return $result;
        } catch (PDOException $e) {
            // Manejo de error (puedes loguear el error si lo deseas)
            return null;
        }
    }
}