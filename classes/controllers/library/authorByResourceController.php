<?php

require_once __DIR__ . "/../../services/library/authorByResourceService.php";

class AuthorByResourceController
{

    private $service;

    public function __construct()
    {
        $this->service = new AuthorByResourceService();
    }

    public function handleRequest()
    {
        header('Content-Type: application/json');

        try {
            switch ($_SERVER['REQUEST_METHOD']) {
                case 'GET':
                    $this->getAuthorsToResource((int) $_GET['id_resource']);
                    break;
                case 'POST':
                    //  $this->uploadResource();
                    break;
                case 'PUT':
                    //  $this->editarLibro();
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

    public function getAuthorsToResource($idResource)
    {
        try {
       
            if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
                http_response_code(405);
                header('Allow: GET'); // Indica qué métodos son permitidos
                throw new Exception("Método no permitido. Use GET");
            }

            if (!isset($_GET['id_resource']) || !is_numeric($_GET['id_resource'])) {
                http_response_code(400);
                throw new InvalidArgumentException('Se requiere un ID de recurso válido (ej: ?id_resource=123)');
            }

            $idResource = (int) $_GET['id_resource'];

            if ($idResource <= 0) {
                http_response_code(400);
                throw new InvalidArgumentException('El ID de recurso debe ser un número positivo');
            }

            $resultado = $this->service->getAuthorByResource($idResource);

            if ($resultado === null) {
                http_response_code(404);
                throw new Exception('Recurso no encontrado');
            }

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'data' => $resultado
            ]);

        } catch (InvalidArgumentException $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage(),
                'type' => 'invalid_arguments'
            ]);

        } catch (Exception $e) {
            $statusCode = http_response_code(); 
            if ($statusCode === 200) {
                http_response_code(500);
            }

            error_log("Error en AuthorByResourcesController: " . $e->getMessage());

            echo json_encode([
                'success' => false,
                'error' => 'Error interno del servidor',
                'type' => 'server_error'
            ]);
        }
    }

}