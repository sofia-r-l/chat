<?php
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');

require_once __DIR__ . '/../../../../classes/controllers/admissions/carreraController.php';

class CenterCarrerAPI {
    private $controller;

    public function __construct() {
        $this->controller = new CarreraController();
    }


        // Maneja la petición GET y verifica el parámetro id_centro
    public function handleRequest() {
        try {
            $method = $_SERVER['REQUEST_METHOD'];
            if ($method === 'GET' && isset($_GET['id_centro'])) {
                $this->getCarrerasPorCentro($_GET['id_centro']);
            } else {
                http_response_code(400);
                echo json_encode(['error' => true, 'mensaje' => 'Parámetros inválidos']);
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
    // Llama al controlador para obtener carreras por centro regional
    private function getCarrerasPorCentro($idCentro) {
        $id = filter_var($idCentro, FILTER_VALIDATE_INT);
        if ($id === false) {
            http_response_code(400);
            echo json_encode(['error' => true, 'mensaje' => 'ID de centro no válido']);
            return;
        }
        $this->controller->handleGetCarrerasPorCentro($id);
    }
}
// Instancia y ejecuta la API
$api = new CenterCarrerAPI();
$api->handleRequest();