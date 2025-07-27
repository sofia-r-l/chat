<?php

require_once __DIR__ . "/../../models/admissions/applicant.php";


class ApplicantService
{

    private $model;
    private $tamanoMaximo =   5 * 1024 * 1024; // 5 MB

    public function __construct()
    {
        $this->model = new Applicant();
    }

    public function procesarArchivo($archivo)
    {

        echo json_encode($archivo);
        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            return ['error' => true, 'mensaje' => 'Error en la subida del archivo.'];
        }

        if ($archivo['size'] > $this->tamanoMaximo) {
            return ['error' => true, 'mensaje' => 'Archivo demasiado grande. Máximo 10MB.'];
        }

        $ancho = null;
        $alto = null;

        if (strpos($archivo['type'], 'image/') === 0) {
            $dimensiones = getimagesize($archivo['tmp_name']);
            if ($dimensiones) {
                $ancho = $dimensiones[0];
                $alto = $dimensiones[1];
            }
        }

        $contenido = file_get_contents($archivo['tmp_name']);

        return [
            'error' => false,
            'nombre' => $archivo['name'],
            'tipo' => $archivo['type'],
            'tamano' => $archivo['size'],
            'ancho' => $ancho,
            'alto' => $alto,
            'contenido' => $contenido
        ];
    }

     


}

