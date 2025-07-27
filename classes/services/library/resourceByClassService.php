<?php
require_once __DIR__ . "/../../models/resource_by_class.php";

class ResourceByClassService
{
    private $model;

    public function __construct()
    {
        $this->model = new ResourceByClass();
    }

    /**
     * Asocia un recurso con una asignatura
     * 
     * @param int $resourceId ID del recurso
     * @param int $classId ID de la asignatura
     * @return array Respuesta en formato JSON
     */
    public function associateResourceWithClass($resourceId, $classId)
    {
        try {
            // Validación de IDs
            if (!is_numeric($resourceId) || $resourceId <= 0) {
                return [
                    'success' => false,
                    'message' => 'ID de recurso inválido',
                    'status' => 400,
                    'data' => null
                ];
            }

            if (!is_numeric($classId) || $classId <= 0) {
                return [
                    'success' => false,
                    'message' => 'ID de asignatura inválido',
                    'status' => 400,
                    'data' => null
                ];
            }

            // Insertar la relación
            $relationId = $this->model->insertNewResourceByClass((int)$resourceId, (int)$classId);

            return [
                'success' => true,
                'message' => 'Recurso asociado con asignatura exitosamente',
                'status' => 201,
                'data' => [
                    'relation_id' => $relationId,
                    'resource_id' => $resourceId,
                    'class_id' => $classId
                ]
            ];

        } catch (Exception $e) {
            error_log("Error en resourceByClassService - associateResourceWithClass: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al asociar recurso con asignatura: ' . $e->getMessage(),
                'status' => 500,
                'data' => null
            ];
        }
    }

    /**
     * Endpoint para manejar la asociación de recursos con asignaturas via API
     * 
     * @param array $requestData Datos de la solicitud
     * @return void Imprime la respuesta JSON directamente
     */
    public function handleAssociationRequest($requestData)
    {
        header('Content-Type: application/json');
        
        // Validar método HTTP
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'message' => 'Método no permitido',
                'status' => 405,
                'data' => null
            ]);
            return;
        }

        // Validar parámetros requeridos
        if (!isset($requestData['resource_id']) || !is_numeric($requestData['resource_id'])) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'El parámetro resource_id (numérico) es requerido',
                'status' => 400,
                'data' => null
            ]);
            return;
        }

        if (!isset($requestData['class_id']) || !is_numeric($requestData['class_id'])) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'El parámetro class_id (numérico) es requerido',
                'status' => 400,
                'data' => null
            ]);
            return;
        }

        // Procesar la asociación
        $response = $this->associateResourceWithClass(
            (int)$requestData['resource_id'],
            (int)$requestData['class_id']
        );
        
        http_response_code($response['status']);
        echo json_encode($response);
    }
}