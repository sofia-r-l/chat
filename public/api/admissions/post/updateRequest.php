
<?php
// Log para depuración del método HTTP recibido

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');

require_once __DIR__ . '/../../../../classes/controllers/admissions/applicantController.php';

class RevisionAPI {
    private $controller;

    public function __construct() {
        $this->controller = new AplicantController();
    }

    public function handleRequest() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            error_log('Entró a POST en updateRequest.php');
            $this->controller->updateStateRequest();
        } else {
            error_log('Método no permitido en updateRequest.php: ' . $_SERVER['REQUEST_METHOD']);
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