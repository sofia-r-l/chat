<?php
require_once __DIR__ . "/../../services/typeResourceService.php";

class TypeResourceController
{
    private $service;

    public function __construct()
    {
        $this->service = new TypeResourceService();
    }

    public function handleRequest()
    {
        header('Content-Type: application/json');

        try {
            switch ($_SERVER['REQUEST_METHOD']) {
                case 'GET':
                    $this->getResourceTypeId();
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
     * Obtiene el ID de un tipo de recurso
     */
    public function getResourceTypeId()
    {
        try {
            $typeResource = $_GET['type'] ?? null;

            if (empty($typeResource)) {
                throw new InvalidArgumentException("Parámetro resource_type es requerido", 400);
            }

            $response = $this->service->getResourceTypeId($typeResource);

            http_response_code($response['status']);
            echo json_encode($response);

        } catch (InvalidArgumentException $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        } catch (Exception $e) {
            throw new Exception("Error al obtener tipo de recurso: " . $e->getMessage());
        }
    }

}

 