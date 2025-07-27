<?php
require_once __DIR__ . "/../../models/library/tagByResource.php";

class TagByResourceService
{
    private $model;

    public function __construct()
    {
        $this->model = new TagByResource();
    }

    /**
     * Asocia una etiqueta con un recurso
     * 
     * @param int $tagId ID de la etiqueta
     * @param int $resourceId ID del recurso
     * @return array Respuesta en formato JSON estandarizado
     */
    public function associateTagWithResource($tagId, $resourceId)
    {

        //echo ("la iad e la etiqueta: ". $tagId ."  el id del recurso: ". $resourceId ."");
        try {

            if (!is_numeric($tagId) || $tagId <= 0) {
                return $this->buildResponse(false, 'ID de etiqueta no válido', 400);
            }

            if (!is_numeric($resourceId) || $resourceId <= 0) {
                return $this->buildResponse(false, 'ID de recurso no válido', 400);
            }

            // Insertar la relación
            $relationId = $this->model->insertNewTagByResource((int) $tagId, (int) $resourceId);

            return $this->buildResponse(
                true,
                'Etiqueta asociada con recurso exitosamente',
                201,
                [
                    'relation_id' => $relationId,
                    'tag_id' => $tagId,
                    'resource_id' => $resourceId
                ]
            );

        } catch (Exception $e) {
            error_log("Error en tagByResourceService - associateTagWithResource: " . $e->getMessage());

            $status = 500;
            $message = 'Error al asociar etiqueta con recurso';


            if (strpos($e->getMessage(), 'ya está asociada') !== false) {
                $status = 409;
                $message = $e->getMessage();
            }

            return $this->buildResponse(false, $message, $status);
        }
    }



     /**
     * Valida que el parmetro de id sea cooerente 
     * 
     * @param int $idResource ID del recurso
     * @return array Respuesta en formato JSON estandarizado de los ids etiquetas
     */
    public function getTagByResource($idResource)
    {
        try {
            if (!is_numeric($idResource) || $idResource <= 0) {
                return $this->buildResponse(false, 'ID no valido', 400);
            }
            $tagId = $this->model->getAllTagByResource((int) $idResource);
            return $this->buildResponse(
                true,
                "Etiquetas obtenidas correctamente en servicio",
                200,
                [
                    "id_recurso" => $idResource,
                    "etiquetas" => $tagId
                    //"etiquetas_name" => $tagName
                ]
            );

        } catch (Exception $e) {
            error_log('Error en tagByResourceService - getTagByResource:' . $e->getMessage());
            $status = 500;
            $message = 'Error al obtener etiqeutas de recurso';
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