<?php
require_once __DIR__ . "/../../models/library/virtualResource.php";

class VirtualResourceService
{
    private $model;
    private $tamanoMaximo = 10 * 1024 * 1024; // 10 MB (ajustable)

    public function __construct()
    {
        $this->model = new VirtualResource(); // Tu modelo de recursos virtuales
    }

    /**
     * Procesa y guarda cualquier archivo en una sola llamada
     * @param  $archivo Archivo subido ($_FILES['nombre'])
     * @param string $titulo Título del recurso
     * @param int $idDocente ID del docente asociado que subio el recurso
     * @param int $idTipoRecurso  ID del tipo de recurso
     * @return array Resultado de la operación
     */
    public function processAndSaveFIle($archivo, $titulo, $idDocente, $idTipoRecurso)
    {

        $resultadoProcesado = $this->processFile($archivo);

        if ($resultadoProcesado['error']) {
            return $resultadoProcesado; // Retorna el error de processFile()
        }

        //echo "da error";
        // Luego guardar usando saveResource()
        return $this->saveResource(
            $titulo,
            $idDocente,
            $idTipoRecurso,
            $resultadoProcesado
        );


    }

    /**
     * Procesa cualquier tipo de archivo sin validaciones específicas
     * estas ya son restringidas para subir el archivo.
     * (excepto tamaño máximo y lectura básica)
     */
    public function processFile($archivo)
    {
        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            return ['error' => true, 'mensaje' => 'Error en la subida del archivo.'];
        }

        if ($archivo['size'] > $this->tamanoMaximo) {
            return ['error' => true, 'mensaje' => 'Archivo demasiado grande. Máximo ' . ($this->tamanoMaximo / 1024 / 1024) . 'MB.'];
        }

        // Leer contenido binario 
        $contenido = file_get_contents($archivo['tmp_name']);
        if ($contenido === false) {
            return ['error' => true, 'mensaje' => 'Error al leer el contenido del archivo.'];
        }

        // Metadatos básicos para todos los tipos de archivo
        return [
            'error' => false,
            'nombre' => $archivo['name'],
            'tipo_mime' => $archivo['type'],
            'tamano' => $archivo['size'],
            'contenido' => $contenido,
            'extension' => strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION))
        ];
    }



    /**
     * Guarda el recurso en la BD con el tipo especificado 
     */
    public function saveResource($titulo, $idDocente, $idTipoRecurso, $archivoProcesado)
    {
        try {
            $id = $this->model->insertNewVirtualResource(
                $titulo,
                $idDocente,
                $idTipoRecurso, // Puede ser NULL
                $archivoProcesado['contenido'],
                $archivoProcesado['tipo_mime'],
                $archivoProcesado['tamano']
            );

            return [
                'error' => false,
                'id' => $id,
                'mensaje' => 'Recurso guardado exitosamente',
                'metadata' => [
                    'nombre' => $archivoProcesado['nombre'],
                    'extension' => $archivoProcesado['extension']
                ]
            ];

        } catch (Exception $e) {
            error_log("Error al guardar recurso : " . $e->getMessage());
            return [
                'error' => true,
                'mensaje' => 'Error en BD: ' . $e->getMessage()
            ];
        }
    }



    /**
     * Obtiene todos los archivos por tipo de recurso
     * @param int $id_tipo_recurso ID del tipo de recurso a consultar
     * @return array Resultados de la consulta o array vacío en caso de error
     */
    public function getAllResourcesByType($id_tipo_recurso)
    {
        try {

            $resultados = $this->model->getAllResourcesByType((int) $id_tipo_recurso);

            if (empty($resultados)) {
                throw new RuntimeException("No se encontraron recursos para el tipo especificado", 404);
            }

            return $resultados;

        } catch (Exception $e) {
            error_log('Error en getAllResourcesByType: ' . $e->getMessage());
            return [];
        }
    }



    public function getInfoResource($idResource)
    {
        try {
            $resultados = $this->model->getAllInfoByType((int) $idResource);

            if (empty($resultados)) {
                throw new RuntimeException("No se encontraron recursos para el tipo especificado", 404);
            }

            return $resultados;

        } catch (Exception $e) {
            error_log('Error en getAInfoResource - VirtualResourceServicios: ' . $e->getMessage());
            return [
                'error' => true,
                'mensaje' => 'Error en BD: ' . $e->getMessage()
            ];
        }

    }


    /**
     * Obtiene todos los archivos por tipo de recurso
     * @param int $idResource ID del recurso a eliminar
     * @return array Resultados de la consulta o array vacío en caso de error
     */
    public function deleteResourceById($idResource)
    {
        try {

            $resultados = $this->model->deleteResourceById((int) $idResource);

            if (empty($resultados)) {
                throw new RuntimeException("No se encontraron recursos para el tipo especificado", 404);
            }

            return $resultados;

        } catch (Exception $e) {
            error_log('Error en getAllResourcesByType: ' . $e->getMessage());
            return [
                'error' => true,
                'mensaje' => 'Error en BD: ' . $e->getMessage()
            ];
        }

    }

    /**
     * Borra un registro de libro  
     * 
     * @param int $idResource Identificador del tipo de recurso a borrar
     * @return boolean
     * @throws PDOException Si ocurre un error en la base de datos
     */
    public function existResourceById($idResource)
    {

        try {
            $resultados = $this->model->existResourceById((int) $idResource);
            if (empty($resultados)) {
                throw new RuntimeException('Recurso no econtrado', 404);
            }

            return $resultados;
        } catch (Exception $e) {
            error_log('Error en virtualResourcesService' . $e->getMessage());
            return false;
        }
    }



    /**
     * Obtiene un archivo para su descarga o visualización
     * 
     * @param int $idRecurso Identificador del recurso virtual
     * @return array Estructura con estado y datos del archivo
     * @throws Exception Si ocurre un error 
     */
    public function getFileById($idRecurso)
    {
        if (empty($idRecurso) || !is_numeric($idRecurso)) {
            throw new InvalidArgumentException("El ID de recurso es requerido", 400);
        }
        try {



            $response = $this->model->getFile($idRecurso);
            // echo json_encode($response);
            error_log("Buscando recurso ID: $idRecurso - " . 
             ($response['success'] ? 'Encontrado' : 'No encontrado'));
            return $response;

        } catch (PDOException $e) {
            error_log("Error en getFile: " . $e->getMessage());
            return [
                "success" => false,
                "message" => "Error al obtener el recurso",
                "status" => 500,
                "file" => null
            ];
        }
    }



}