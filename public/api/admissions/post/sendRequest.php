<?php

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');

require_once __DIR__ . '/../../../../classes/controllers/admissions/applicantController.php';

/**
 * API para manejo de solicitudes de admisión
 * 
 * Endpoints:
 * POST /api/aspirantes - Crea una nueva solicitud
 */
class AplicantAPI {
    private $controller;

    public function __construct() {
        $this->controller = new AplicantController();
    }

    public function handleRequest() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->controller->handlePostRequest();
        } else {
            http_response_code(405);
            echo json_encode([' error' => true, 'mensaje' => 'Método no permitido']);
        }
    }
}


class RevisionAPI {
    private $controller;

    public function __construct() {
        $this->controller = new AplicantController();
    }

    public function handleRequest() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->controller->updateStateRequest();
        } else {
            http_response_code(405);
            echo json_encode(['error' => true, 'mensaje' => 'Método no permitido']);
        }
    }
}

try {
    $api = new RevisionAPI();
    $api->handleRequest();
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'mensaje' => 'Error interno del servidor',
        'debug' => $e->getMessage().' en '.$e->getFile().':'.$e->getLine()
    ]);
}



try {
    $api = new AplicantAPI();
    $api->handleRequest();
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'mensaje' => 'Error interno del servidor',
        'debug' => $e->getMessage().' en '.$e->getFile().':'.$e->getLine()
    ]);
}

