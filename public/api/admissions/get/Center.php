<?php
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');

require_once __DIR__ . '/../../../../classes/controllers/admissions/carreraController.php';

class CentrosAPI {
    private $controller;
    // Constructor: inicializa el controlador de carreras

    public function __construct() {
        $this->controller = new CarreraController();
    }


    // Maneja la petición GET para obtener los centros regionales

    public function handleRequest() {
        try {
            $method = $_SERVER['REQUEST_METHOD'];
            // Si la petición es GET, obtiene los centros regionales

            if ($method === 'GET') {
                $this->getCentrosRegionales();
            } else {
                http_response_code(405);
                echo json_encode(['error' => true, 'mensaje' => 'Método no permitido']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'error' => true,
                'mensaje' => 'Error en el servidor',
                'debug' => $e->getMessage()
            ]);
        }
    }
        // Llama al controlador para obtener los centros regionales
    private function getCentrosRegionales() {
        $this->controller->handleGetCentrosRegionales();
    }
}
// Instancia y ejecuta la API

$api = new CentrosAPI();
$api->handleRequest();