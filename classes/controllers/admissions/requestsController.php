<?php
require_once __DIR__ . '/../../services/admissions/solicitudesService.php';
$modelo = new GetModel($pdo);
/**
 * Controlador para solicitudes de revisión.
 */
class RequestsController
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Devuelve la primer solicitud pendiente para el revisor actual.
     * @param int $revisorId
     * @return array|null
     */
    public function getPrimeraSolicitudPendiente($revisorId)
    {
        return obtenerPrimeraSolicitudPendientePorRevisor($this->pdo, $revisorId);
    }
}