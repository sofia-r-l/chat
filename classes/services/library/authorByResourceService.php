<?php
require_once __DIR__ . "/../../models/library/authorByResource.php";

class AuthorByResourceService
{
    private $model;

    public function __construct()
    {
        $this->model = new AuthorByResource();
    }

    /**
     * Asocia un autor con un recurso
     * 
     * @param int $authorId ID del autor
     * @param int $resourceId ID del recurso
     * @return array Respuesta en formato JSON
     */
    public function associateAuthorWithResource($authorId, $resourceId)
    {
        try {
            // Validación de ID
            if (!is_numeric($authorId) || $authorId <= 0) {
                return [
                    'error' => true,
                    'mensaje' => 'ID de autor inválido',
                    'status' => 400
                ];
            }

            if (!is_numeric($resourceId) || $resourceId <= 0) {
                return [
                    'error' => true,
                    'mensaje' => 'ID de recurso inválido',
                    'status' => 400
                ];
            }

            // Insertar la relación
            $relationId = $this->model->insertNewAuthorByResource((int) $authorId, (int) $resourceId);

            return [
                'error' => false,
                'mensaje' => 'Autor asociado con recurso exitosamente',
                'data' => [
                    'relation_id' => $relationId,
                    'author_id' => $authorId,
                    'resource_id' => $resourceId
                ],
                'status' => 201
            ];

        } catch (Exception $e) {
            error_log("Error en authorByResourceService - associateAuthorWithResource: " . $e->getMessage());
            return [
                'error' => true,
                'mensaje' => 'Error al asociar autor con recurso en Service : ' . $e->getMessage(),
                'status' => 500
            ];
        }
    }



    /**
     * Valida que el parmetro de id sea cooerente 
     * 
     * @param int $idResource ID del recurso
     * @return array Respuesta en formato JSON estandarizado de los ids autores y sus nombres 
     */
    public function getAuthorByResource($idResource)
    {
        try {
            if (!is_numeric($idResource) || $idResource <= 0) {
                return $this->buildResponse(false, 'ID no valido', 400);
            }
            $authorId = $this->model->getAllauthorByResource((int) $idResource);
            return $this->buildResponse(
                true,
                "Autores obtenidos correctamente en servicio",
                200,
                [
                    "id_recurso" => $idResource,
                    "autores" => $authorId
                   
                ]
            );

        } catch (Exception $e) {
            error_log('Error en AuthorByResourceService - getAutorByResource:' . $e->getMessage());
            $status = 500;
            $message = 'Error al obtener autores del recurso';
            return $this->buildResponse(false, $message, $status);
        }

    }

    /**
     * Estandar de respuesta estandarizada
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