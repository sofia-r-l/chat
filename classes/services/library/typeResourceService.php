<?php
require_once __DIR__ . "/../../models/type_resource.php";

class TypeResourceService
{
    private $model;

    public function __construct()
    {
        $this->model = new TypeResource();
    }

    /**
     * Crea un nuevo tipo de recurso
     * 
     * @param string $resourceType Nombre del tipo de recurso
     * @return array Respuesta en formato JSON estandarizado
     */
    public function createResourceType($resourceType)
    {
        try {
            // Validación del tipo de recurso
            $resourceType = trim($resourceType);

            if (empty($resourceType)) {
                return $this->buildResponse(false, 'El tipo de recurso no puede estar vacío', 400);
            }

            if (strlen($resourceType) > 50) {
                return $this->buildResponse(false, 'El tipo de recurso no puede exceder los 50 caracteres', 400);
            }

            // Insertar el nuevo tipo de recurso
            $typeId = $this->model->insertNewTypeResource($resourceType);

            return $this->buildResponse(
                true,
                'Tipo de recurso creado exitosamente',
                201,
                [
                    'type_id' => $typeId,
                    'resource_type' => $resourceType
                ]
            );

        } catch (Exception $e) {
            error_log("Error en TypeResourceService - createResourceType: " . $e->getMessage());

            $status = 500;
            $message = 'Error al crear el tipo de recurso';

            // Manejo específico para violación de constraint única
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                $status = 409;
                $message = 'Este tipo de recurso ya existe';
            }

            return $this->buildResponse(false, $message, $status);
        }
    }

    
    public function getResourceTypeId($typeResource)
    {
        try {
           
            $normalizedType = mb_strtolower(trim($typeResource), 'UTF-8');

            if (empty($normalizedType)) {
                throw new InvalidArgumentException('El tipo de recurso no puede estar vacío');
            }

            $typeId = $this->model->getResourcesTypeId($normalizedType);

            if ($typeId === false || $typeId === null) {
                return $this->buildResponse(false, 'Tipo de recurso no encontrado', 404);
            }

            return $this->buildResponse(
                true,
                'Tipo de recurso encontrado',
                200,
                ['type_id' => $typeId]
            );

        } catch (InvalidArgumentException $e) {
            return $this->buildResponse(false, $e->getMessage(), 400);
        } catch (Exception $e) {
            error_log("Error en getResourceTypeId - SERVICE: " . $e->getMessage());
            return $this->buildResponse(false, 'Error en el servidor', 500);
        }
    }



    /**
     * respuesta estandarizada
     * 
     * @param bool $success Indica si la operación fue exitosa
     * @param string $message Mensaje descriptivo
     * @param int $status Código de estado HTTP
     * @param array|null $data Datos adicionales
     * @return array Respuesta estructurada
     */
    private function buildResponse($success, $message, $status, $data = null)
    {
        return [
            'success' => $success,
            'message' => $message,
            'status' => $status,
            'data' => $data
        ];
    }
}