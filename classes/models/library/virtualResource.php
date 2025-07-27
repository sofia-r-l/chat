<?php

require_once __DIR__ . "/../../../config/db_academic_config.php";

class VirtualResource
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = $GLOBALS['pdo'];
    }


    /**
     * Inserta un nuevo recurso en base de datos
     * 
     * @return int id del ultimo registro en insertado 
     * @throws PDOException Si ocurre un error en la base de datos
     * @throws Exception Si ocurre un error inesperado
     */
    public function insertNewVirtualResource(
        string $titulo,
        int $idDocente = 1,
        int $idTipoRecurso,
        $recursoBlob,
        $mime = 'pdf',
        $tamanio
    ) {
        try {
            $sql = "INSERT INTO TBL_RECURSO_VIRTUAL (
                TITULO, 
                ID_DOCENTE, 
                ID_TIPO_RECURSO_VIRTUAL,
                RECURSO,
                MIME,
                TAMANIO
                ) VALUES (
                :titulo, 
                :idDocente, 
                :idTipoRecurso,
                :recurso,
                :mime,
                :tamanio
                )";

            $stmt = $this->pdo->prepare($sql);

            $stmt->bindValue(":titulo", $titulo, PDO::PARAM_STR);
            $stmt->bindValue(":idDocente", $idDocente, PDO::PARAM_INT);
            $stmt->bindValue(":idTipoRecurso", $idTipoRecurso, PDO::PARAM_INT);
            $stmt->bindParam(":recurso", $recursoBlob, PDO::PARAM_LOB);
            $stmt->bindParam(":mime", $mime, PDO::PARAM_STR);
            $stmt->bindParam(":tamanio", $tamanio, PDO::PARAM_INT);

            if (!$stmt->execute()) {
                throw new Exception("Error al insertar recurso");
            }

            //$this->pdo->commit();

            return $this->pdo->lastInsertId();


        } catch (PDOException $e) {
            throw new Exception("Error de base de datos al coordinar la insercion del recurso virtual: " . $e->getMessage());
        }
    }



    /**
     * Obtiene todos los recursos virtuales por tipo de recurso
     * 
     * @param int $idTypeResource Identificador del tipo de recurso
     * @return array Array asociativo con los recursos encontrados
     * @throws PDOException Si ocurre un error en la base de datos
     * @throws InvalidArgumentException Si el parámetro es inválido
     */
    public function getAllResourcesByType(int $idTypeResource): array
    {

        if ($idTypeResource <= 0) {
            throw new InvalidArgumentException("El ID de tipo NO cumple como parametro");
        }

        try {
            $sql = "SELECT 
                    ID_RECURSO_VIRTUAL,
                    TITULO,
                    MIME
                FROM TBL_RECURSO_VIRTUAL 
                WHERE ID_TIPO_RECURSO_VIRTUAL = :id_tipo_recurso_virtual
                ORDER BY FECHA_REGISTRO DESC";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(":id_tipo_recurso_virtual", $idTypeResource, PDO::PARAM_INT);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);


            if (empty($resultados)) {
                return [];
            }

            return $resultados;

        } catch (PDOException $e) {
            error_log("[BD Error] En getAllResources - virtualResource: {$idTypeResource} - " . $e->getMessage());
            throw new RuntimeException("Error al obtener recursos. Por favor intente más tarde");
        }
    }

    /**
     * Obtiene todos los recursos virtuales por tipo de recurso 
     * 
     * @param int $idTypeResource Identificador del tipo de recurso
     * @return array Array asociativo con etiquetas asocidas y autores 
     * @throws PDOException Si ocurre un error en la base de datos
     * @throws InvalidArgumentException Si el parámetro es inválido
     */
    public function getAllInfoByType(int $idTypeResource): array
    {
        if ($idTypeResource <= 0) {
            throw new InvalidArgumentException("El ID de tipo NO cumple como parametro");
        }

        try { /*rv.MIME as mime,*/
            // Consulta principal para obtener los recursos
            $sql = "SELECT 
                    rv.ID_RECURSO_VIRTUAL as id_libro,
                    rv.TITULO as titulo,
                   
                    rv.ID_TIPO_RECURSO_VIRTUAL as id_tipo_recurso,
                    trv.TIPO_RECURSOS as tipo_recurso,
                    rv.FECHA_REGISTRO as fecha_registro
                FROM TBL_RECURSO_VIRTUAL rv
                JOIN TBL_TIPO_RECURSO_VIRTUAL trv ON rv.ID_TIPO_RECURSO_VIRTUAL = trv.ID_TIPO_RECURSO_VIRTUAL
                WHERE rv.ID_TIPO_RECURSO_VIRTUAL = :id_tipo_recurso
                ORDER BY rv.FECHA_REGISTRO DESC";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(":id_tipo_recurso", $idTypeResource, PDO::PARAM_INT);
            $stmt->execute();

            $recursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($recursos)) {
                return [];
            }

            // Obtener datos adicionales para cada recurso
            foreach ($recursos as &$recurso) {
                // Obtener etiquetas
                $sqlEtiquetas = "SELECT e.ID_ETIQUETA 
                            FROM TBL_ETIQUETAS_X_RECURSOS er
                            JOIN TBL_ETIQUETAS e ON er.ID_ETIQUETA = e.ID_ETIQUETA
                            WHERE er.ID_RECURSO = :id_recurso";
                $stmtEtiquetas = $this->pdo->prepare($sqlEtiquetas);
                $stmtEtiquetas->bindValue(":id_recurso", $recurso['id_libro'], PDO::PARAM_INT);
                $stmtEtiquetas->execute();
                $recurso['etiquetas'] = $stmtEtiquetas->fetchAll(PDO::FETCH_COLUMN, 0);

                // Obtener autores
                $sqlAutores = "SELECT a.ID_AUTOR 
                          FROM TBL_AUTORES_X_RECURSOS ar
                          JOIN TBL_AUTORES a ON ar.ID_AUTOR = a.ID_AUTOR
                          WHERE ar.ID_RECURSO = :id_recurso";
                $stmtAutores = $this->pdo->prepare($sqlAutores);
                $stmtAutores->bindValue(":id_recurso", $recurso['id_libro'], PDO::PARAM_INT);
                $stmtAutores->execute();
                $recurso['autores'] = $stmtAutores->fetchAll(PDO::FETCH_COLUMN, 0);

                // Obtener el archivo (BLOB)
                $sqlArchivo = "SELECT ARCHIVO 
                          FROM TBL_RECURSO_VIRTUAL 
                          WHERE ID_RECURSO_VIRTUAL = :id_recurso";
                $stmtArchivo = $this->pdo->prepare($sqlArchivo);
                $stmtArchivo->bindValue(":id_recurso", $recurso['id_libro'], PDO::PARAM_INT);
                $stmtArchivo->execute();
                $recurso['file'] = $stmtArchivo->fetchColumn();
            }

            return $recursos;

        } catch (PDOException $e) {
            error_log("[BD Error] En getAllResources - virtualResource: {$idTypeResource} - " . $e->getMessage());
            throw new RuntimeException("Error al obtener recursos. Por favor intente más tarde");
        }
    }


    /**
     * Borra un registro de libro  
     * 
     * @param int $idResource Identificador del tipo de recurso a borrar
     * @return array
     * @throws PDOException Si ocurre un error en la base de datos
     */
    public function deleteResourceById($idRecurso)
    {


        try {
            $this->pdo->beginTransaction();

            // 1. Eliminar etiquetas asociadas
            $stmt = $this->pdo->prepare("DELETE FROM TBL_ETIQUETAS_X_RECURSOS WHERE ID_RECURSO = :id_recurso");
            $stmt->bindValue(":id_recurso", $idRecurso, PDO::PARAM_INT);
            $stmt->execute();

            // 2. Eliminar autores asociados
            $stmt = $this->pdo->prepare("DELETE FROM TBL_AUTORES_X_RECURSOS WHERE ID_RECURSO = :id_recurso");
            $stmt->bindValue(":id_recurso", $idRecurso, PDO::PARAM_INT);
            $stmt->execute();

            // 3. Eliminar el recurso principal
            $stmt = $this->pdo->prepare("DELETE FROM TBL_RECURSO_VIRTUAL WHERE ID_RECURSO_VIRTUAL = :id_recurso");
            $stmt->bindValue(":id_recurso", $idRecurso, PDO::PARAM_INT);
            $stmt->execute();

            $this->pdo->commit();
            return [
                'success' => true,
                'message' => "Registro eliminado correctamente",
                'status' => 200
            ];
            ;

        } catch (PDOException $e) {
            error_log("" . $e->getMessage());
            throw new RuntimeException("");
        }
    }


    /**
     * Consulta la existenica de u recurso por el id 
     * 
     * @param int $idResource Identificador del tipo de recurso 
     * @return boolean true si retorna al menos 1 registro
     * @throws PDOException Si ocurre un error en la base de datos
     */
    public function existResourceById($idRecurso)
    {
        try {
            $sql = "SELECT * FROM TBL_RECURSO_VIRTUAL WHERE ID_RECURSO_VIRTUAL = :id_recurso";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(":id_recurso", $idRecurso, PDO::PARAM_LOB);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            return $resultado > 0;
        } catch (PDOException $e) {
            error_log("Error al coordinar consulta desde modelo existResourceById" . $e->getMessage());
            throw new RuntimeException("Error inesperado en Modelo");
        }

    }


    /**
     * Obtiene un archivo para su descarga o visualización
     * 
     * @param int $idRecurso Identificador del recurso virtual
     * @return array Estructura con estado y datos del archivo
     * @throws PDOException Si ocurre un error en la base de datos
     */
    public function getFile($idRecurso)
    {
        try {
            $sql = "SELECT 
            RECURSO as archivo_blob, 
            TITULO as nombre_archivo,
            MIME as tipo_mime,
            TAMANIO as tamanio, 
            DESCARGAS,
            VISUALIZACIONES
        FROM TBL_RECURSO_VIRTUAL
        WHERE ID_RECURSO_VIRTUAL = :id_resource";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(":id_resource", $idRecurso, PDO::PARAM_INT);
            $stmt->execute();

            $file = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$file) {
                return [
                    "success" => false,
                    "message" => "Recurso no encontrado",
                    "status" => 404,
                    "file" => null
                ];
            }
            // echo json_encode($file);
            return [
                "success" => true,
                "message" => "Archivo obtenido correctamente",
                "status" => 200,
                "file" => [
                    "content" => $file['archivo_blob'],
                    "name" => $file['nombre_archivo'],
                    "mime" => $file['tipo_mime'],
                    "size" => $file['tamanio'],
                    "downloads" => $file['DESCARGAS'],
                    "views" => $file['VISUALIZACIONES']
                ]
            ];

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