<?php
require_once __DIR__ . "/../../models/library/author.php";

class AuthorService
{
    private $model;

    public function __construct()
    {
        $this->model = new Author();
    }

    /**
     * Crea un nuevo autor en el sistema
     * 
     * @param string $authorName Nombre del autor a crear
     * @return array Respuesta en formato JSON
     */
    public function createAuthor($authorName)
    {
        try {

            if (empty(trim($authorName))) {
                return [
                    'error' => true,
                    'mensaje' => 'El nombre del autor no puede estar vacío',
                    'status' => 400
                ];
            }

            $authorId = $this->model->insertNewAuthor($authorName);

            return [
                'error' => false,
                'mensaje' => 'Autor creado exitosamente',
                'data' => [
                    'author_id' => $authorId,
                    'author_name' => $authorName
                ],
                'status' => 201
            ];

        } catch (Exception $e) {
            error_log("Error en authorService - reateAuthor: " . $e->getMessage());
            return [
                'error' => true,
                'mensaje' => 'Error al crear el autor desde service: ' . $e->getMessage(),
                'status' => 500
            ];
        }
    }




    /**
     * obtiene las filas con conicidencias de seccion de strin enviado 
     * @param string $secctionString fragmento de cadena a machear
     * @return array Respuesta en formato JSON estandarizado
     */
    public function getMatchAuthor($secctionString)
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

            $response = $this->model->getSeccionAnyAuthor($secctionString);

            return [
                'success' => true,
                'message' => ' coidencias obtenidas ',
                'status' => 200,
                'data' => $response
            ];

        } catch (Exception $e) {
            error_log("Error en authorService - getMatchAuthor: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al obtener coincidencias ' . $e->getMessage(),
                'status' => 500,
                'data' => null
            ];
        }
    }

   
}