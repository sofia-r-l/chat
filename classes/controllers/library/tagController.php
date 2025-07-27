<?php

require_once __DIR__ . "/../../services/library/tagService.php";

class tagController
{

    private $service;

    public function __construct()
    {
        $this->service = new TagService();
    }


    public function handleRequest()
    {
        header('Content-Type: application/json');

        try {
            switch ($_SERVER['REQUEST_METHOD']) {
                case 'GET':
                    if (isset($_GET['string_search'])) {
                        $this->handleGetRequest();
                    } else {
                        $this->getAllTags();
                    }
                    break;
                case 'POST':
                    $this->createTag();
                    break;
                case 'PUT':
                    //$this->editarLibro();
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



    /**
     * Maneja la creación de una nueva etiqueta
     * 
     * @return void Imprime respuesta JSON y termina ejecución
     */
    public function createTag()
    {
        try {

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new RuntimeException('Método no permitido. Se requiere POST', 405);
            }

            $input = json_decode(file_get_contents('php://input'), true);
            $tagName = trim($input['tag_name'] ?? '');

            if (empty($tagName)) {
                throw new InvalidArgumentException('El nombre de la etiqueta es requerido', 400);
            }

            if (strlen($tagName) > 50) {
                throw new InvalidArgumentException('El nombre de la etiqueta no puede exceder los 50 caracteres', 400);
            }



            // Procesar la creación
            $response = $this->service->createTag($tagName);

            http_response_code(201);
            echo json_encode([
                'success' => true,
                'message' => 'Etiqueta creada exitosamente',
                'status' => 201,
                'data' => [
                    'tag_name' => $tagName
                ]
            ]);

        } catch (Exception $e) {
            http_response_code($e->getCode() ?: 500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }

    }


    /**
     * Obtiene todas las etiquetas disponibles
     * @return void Imprime respuesta JSON y termina ejecución
     */
    public function getAllTags()
    {
        try {

            if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
                throw new RuntimeException('Método no permitido. Se requiere GET', 405);
            }

            $tags = $this->service->getAllTags();

            if (empty($tags)) {
                http_response_code(404);
                echo json_encode([
                    'success' => true,
                    'data' => [],
                    'message' => 'No se encontraron etiquetas'
                ]);
                return;
            }


            http_response_code(200);
            echo json_encode([
                'success' => true,
                'data' => $tags
            ]);

        } catch (Exception $e) {
            // Errores inesperados
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Error interno del servidor',
                'status' => 500
            ]);
            error_log("Error en getAllTags: " . $e->getMessage());
        }
    }




    private function handleGetRequest()
    {
        if (!isset($_GET['string_search'])) {
            throw new Exception("Parámetro 'string_search' faltante", 400);
        }

        $searchTerm = $_GET['string_search'];
        //$typeInput = $_GET['type_input'] ?? 'etiquetas'; // Valor por defecto

        if (strlen($searchTerm) < 2) {
            throw new Exception("El término de búsqueda debe tener al menos 2 caracteres", 400);
        }

        $results = $this->service->getMatchTags($searchTerm);

        echo json_encode([
            'success' => true,
            'data' => $results
        ]);
    }





}