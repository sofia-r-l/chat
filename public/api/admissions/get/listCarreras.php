<?php
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');

require_once __DIR__ . '/../../../../classes/controllers/admissions/carreraController.php';

/**
 * API para manejo de carreras académicas
 * 
 * Endpoints:
 * GET /api/carreras - Obtiene todas las carreras
 * GET /api/carreras/rest?exclude_id=X - Obtiene carreras excepto la especificada
 */
class ListarCarreras {
    private $controller;
    
    public function __construct() {
        $this->controller = new CarreraController();
    }
    
    public function handleRequest() {
        try {
            $method = $_SERVER['REQUEST_METHOD'];
            $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            
            if ($method === 'GET') {
                if (strpos($path, '/api/carreras/rest') !== false && isset($_GET['exclude_id'])) {
                    $this->getCarrerasRestantes($_GET['exclude_id']);
                } else {
                    $this->getTodasCarreras();
                }
            } else {
                http_response_code(405);
                echo json_encode(['error' => true, 'mensaje' => 'Método no permitido']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'error' => true,
                'mensaje' => 'Error en el servidor',
                'debug' => $e->getMessage() // Solo en desarrollo
            ]);
        }
    }
    
    private function getTodasCarreras() {
        $this->controller->handleGetRequest();
    }
    
    private function getCarrerasRestantes($idExcluir) {
        // Verifica que el ID sea válido
        $id = filter_var($idExcluir, FILTER_VALIDATE_INT);
        if ($id === false) {
            http_response_code(400);
            echo json_encode(['error' => true, 'mensaje' => 'ID no válido']);
            return;
        }
        
        // Asume que tu controller tiene este método
        $this->controller->handleGetRestCarrera($id);
    }
}

// Uso de la clase
$api = new ListarCarreras();
$api->handleRequest();
