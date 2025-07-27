<?php

require_once __DIR__ ."/../../models/library/subjects.php";


class SubjectService {

   private $model;

   public function __construct() {
    $this->model = new Subjects();
   }


   /**
     * obtiene las filas con conicidencias de seccion de strin enviado 
     * @param string $secctionString fragmento de cadena a machear
     * @return array Respuesta en formato JSON estandarizado
     */
    public function getMatch($secctionString)
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

            $response = $this->model->getSeccionAnySubjects($secctionString);

            return [
                'success' => true,
                'message' => ' coidencias obtenidas ',
                'status' => 200,
                'data' => $response
            ];

        } catch (Exception $e) {
            error_log("Error en subjetService - getMatch: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al obtener coincidencias ' . $e->getMessage(),
                'status' => 500,
                'data' => null
            ];
        }
    }



}