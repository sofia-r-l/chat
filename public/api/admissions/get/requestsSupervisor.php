<?php

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');

require_once __DIR__ . '/../../../../classes/controllers/admissions/requestsController.php';

// Aquí debes obtener el ID del revisor autenticado.
// Por ejemplo, desde la sesión, token JWT, o por GET/POST (no recomendado en producción).
// Ejemplo usando GET para pruebas:
$revisorId = isset($_GET['revisor_id']) ? intval($_GET['revisor_id']) : null;

if (!$revisorId) {
    http_response_code(400);
    echo json_encode(['error' => true, 'mensaje' => 'ID de revisor no proporcionado']);
    exit;
}

// Instancia el controlador y obtiene la solicitud pendiente
try {
    // Debes tener tu conexión PDO aquí
    require_once __DIR__ . '/../../../../classes/controllers/admissions/requestsController.php';
    $pdo = require __DIR__ . '/../../../../config/db_admissions_config.php';
 // Supón que tienes una función getPDO() que retorna la conexión

    $controller = new RequestsController($pdo);
    $solicitud = $controller->getPrimeraSolicitudPendiente($revisorId);

    if ($solicitud) {
        echo json_encode(['error' => false, 'data' => $solicitud]);
    } else {
        echo json_encode(['error' => true, 'mensaje' => 'No hay solicitudes pendientes para este revisor']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'mensaje' => 'Error en el servidor',
        'debug' => $e->getMessage()
    ]);
}