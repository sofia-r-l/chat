<?php
require_once __DIR__ . '/../../models/admissions/get.php';

/**
 * Lista todas las solicitudes pendientes (si lo necesitas).
 */
/**
 * Obtiene la primer solicitud pendiente para un revisor específico.
 * @param PDO $pdo
 * @param int $revisorId
 * @return array|null
 */
function obtenerPrimeraSolicitudPendientePorRevisor($pdo, $revisorId) {
    $modelo = new GetModel($pdo);
    return $modelo->getPrimeraSolicitudPendientePorRevisorSP($revisorId);
}
