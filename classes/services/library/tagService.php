<?php
require_once __DIR__ . "/../../models/library/tag.php";

class TagService
{
    private $model;

    public function __construct()
    {
        $this->model = new Tag();
    }

    /**
     * Crea una nueva etiqueta en el sistema
     * @param string $tagName Nombre de la etiqueta a crear
     * @return array Respuesta en formato JSON estandarizado
     */
    public function createTag($tagName)
    {
        try {
            // Validación del nombre

            if (empty(trim($tagName))) {
                return [
                    'success' => false,
                    'message' => 'El nombre de la etiqueta no puede estar vacío',
                    'status' => 400,
                    'data' => null
                ];
            }

            if (strlen(trim($tagName)) > 50) {
                return [
                    'success' => false,
                    'message' => 'El nombre de la etiqueta no puede exceder los 50 caracteres',
                    'status' => 400,
                    'data' => null
                ];
            }

            $tagId = $this->model->insertNewTag($tagName);

            return [
                'success' => true,
                'message' => 'Etiqueta creada exitosamente',
                'status' => 201,
                'data' => [
                    'tag_id' => $tagId,
                    'tag_name' => $tagName
                ]
            ];

        } catch (Exception $e) {
            error_log("Error en TagService - createTag: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al crear la etiqueta: ' . $e->getMessage(),
                'status' => 500,
                'data' => null
            ];
        }
    }


    /**
     * Obtiene todas las etiquetas y devuelve la respuesta en formato JSON
     * 
     * @return string Respuesta JSON con las etiquetas o mensaje de error
     */
    public function getAllTags()
    {
        try {

            $tags = $this->model->getAllTags();

            if (!is_array($tags)) {
                throw new RuntimeException('Formato de datos inesperado');
            }

            return json_encode([
                'success' => true,
                'data' => $tags,
                'count' => count($tags),
                'message' => count($tags) > 0
                    ? 'Etiquetas obtenidas exitosamente'
                    : 'No hay etiquetas registradas'
            ]);

        } catch (PDOException $e) {
            error_log('Error de base de datos en getAllTags - tagService: ' . $e->getMessage());
            return json_encode([
                'success' => false,
                'error' => 'Error al obtener las etiquetas',
                'status' => 500
            ]);

        } catch (Exception $e) {
            error_log('Error en getAllTags - tagService: ' . $e->getMessage());
            return json_encode([
                'success' => false,
                'error' => $e->getMessage(),
                'status' => 500
            ]);
        }
    }





    /**
     * obtiene las filas con conicidencias de seccion de strin enviado 
     * @param string $secctionString fragmento de cadena a machear
     * @return array Respuesta en formato JSON estandarizado
     */
    public function getMatchTags($secctionString)
    {
        try {
            // Validación del nombre

            if (empty(trim($secctionString))) {
                return [
                    'success' => false,
                    'message' => 'El valor a buscar conicidencias no puede estar vacío',
                    'status' => 400,
                    'data' => null
                ];
            }

            if (strlen(trim($secctionString)) > 50) {
                return [
                    'success' => false,
                    'message' => 'El fragmento de busqueda  no puede exceder los 50 caracteres',
                    'status' => 400,
                    'data' => null
                ];
            }

            $response = $this->model->getSeccionAnyTags($secctionString);

            return [
                'success' => true,
                'message' => ' coidencias obtenidas ',
                'status' => 200,
                'data' => $response
            ];

        } catch (Exception $e) {
            error_log("Error en tagService - getMatchTags: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al obtener coincidencias ' . $e->getMessage(),
                'status' => 500,
                'data' => null
            ];
        }
    }


}