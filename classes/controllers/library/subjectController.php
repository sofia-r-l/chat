<?php
require_once __DIR__ ."/../../services/library/subjectService.php";

class SubjectController {
    private $service;

    public function __construct() {
        $this->service = new SubjectService();
    }

    public function handleRequest() {
        header('Content-Type: application/json');

        try {
            switch ($_SERVER['REQUEST_METHOD']) {
                case 'GET':
                    $this->handleGetRequest();
                    break;
                default:
                    http_response_code(405);
                    throw new Exception("Método no permitido");
            }
        } catch (Exception $e) {
            http_response_code($e->getCode() ?: 500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    private function handleGetRequest() {
        if (!isset($_GET['string_search'])) {
            throw new Exception("Parámetro 'string_search' faltante", 400);
        }

        $searchTerm = $_GET['string_search'];
        $typeInput = $_GET['type_input'] ?? 'etiquetas'; // Valor por defecto

        if (strlen($searchTerm) < 2) {
            throw new Exception("El término de búsqueda debe tener al menos 2 caracteres", 400);
        }

        $results = $this->service->getMatch($searchTerm);

        echo json_encode([
            'success' => true,
            'data' => $results
        ]);
    }
}