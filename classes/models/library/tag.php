<?php

require_once __DIR__ . "/../../../config/db_academic_config.php";

class Tag
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = $GLOBALS['pdo'];
    }


    /**
     * inserta una nueva etiqueta en la base de datos
     * 
     * @param string $nombreEtiqueta nombre de nueva etiqueta
     * @return int id del ultimo registro en BD
     * @throws PDOException Si ocurre un error en la base de datos
     * @throws Exception Si ocurre un error inesperado
     */
    public function insertNewTag($nombreEtiqueta)
    {
        try {

            $sql = "INSERT INTO TBL_ETIQUETAS (NOMBRE_ETIQUETA) VALUES (:nombreEtiqueta)";
            $stmt = $this->pdo->prepare($sql);

            $stmt->bindValue(":nombreEtiqueta", $nombreEtiqueta, PDO::PARAM_STR);

            if (!$stmt->execute()) {
                $errorInfo = $stmt->errorInfo();
                error_log("Error en ejecución SQL al insertar etiqueta: " . print_r($errorInfo, true));
                throw new Exception("Error al insertar la etiqueta en la base de datos");
            }

            //$this->pdo->commit();

            return $this->pdo->lastInsertId();

        } catch (PDOException $e) {
            error_log("Error PDO en insertNewTag - tag: {$nombreEtiqueta} - " . $e->getMessage());
            throw new Exception("Error de base de datos al crear nueva etiqueta");
        }
    }

    /**
     * Obtiene todas las etiquetas de la base de datos
     * 
     * @return array Listado de etiquetas
     * @throws PDOException Si ocurre un error en la base de datos
     * @throws Exception Si ocurre un error inesperado
     */
    public function getAllTags()
    {
        try {
            $sql = "SELECT 
                    id_etiqueta as tag_id, 
                    nombre_etiqueta as tag_name
                FROM TBL_ETIQUETAS 
                ORDER BY nombre_etiqueta ASC";

            $stmt = $this->pdo->prepare($sql);

            if (!$stmt->execute()) {
                throw new PDOException("Error al ejecutar la consulta");
            }

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!is_array($result)) {
                throw new RuntimeException("Formato de datos inesperado");
            }

            return $result;

        } catch (PDOException $e) {
            error_log("Error PDO en getAllTags - " . $e->getMessage());
            throw new Exception("Error al obtener las etiquetas: " . $e->getMessage());
        } catch (Exception $e) {
            error_log("Error en getAllTags - " . $e->getMessage());
            throw new Exception("Error al procesar las etiquetas");
        }
    }


    /**
     * obtine todas loc ocnidencias de etiqeutas 
     * 
     * @param string $seccionString segmento de busqueda coicidencias
     * @return array coicidencias
     * @throws PDOException Si ocurre un error en la base de datos
     * @throws Exception Si ocurre un error inesperado
     */
    public function getSeccionAnyTags($seccionString)
    {


        try {
            $sql = "SELECT 
                    a.ID_ETIQUETA as id,
                    a.NOMBRE_ETIQUETA as nombre
                FROM 
                    TBL_ETIQUETAS a
                WHERE 
                    (a.NOMBRE_ETIQUETA LIKE :search )
            
                ORDER BY 
                    a.NOMBRE_ETIQUETA
                LIMIT 10";

            $stmt = $this->pdo->prepare($sql);

            // Concatenar los % aquí, no en la consulta SQL
            $searchParam = "%" . $seccionString . "%";
            $stmt->bindParam(':search', $searchParam, PDO::PARAM_STR);

            if (!$stmt->execute()) {
                $errorInfo = $stmt->errorInfo();
                error_log("Error SQL: " . print_r($errorInfo, true));
                throw new Exception("Error al ejecutar la consulta: " . $errorInfo[2]);
            }

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $result;

        } catch (PDOException $e) {
            error_log("Error PDO en getSeccionAnyTags: " . $e->getMessage());
            throw new Exception("Error de base de datos: " . $e->getMessage());
        } catch (Exception $e) {
            error_log("Error general en getSeccionAnyTags: " . $e->getMessage());
            throw $e;
        }
    }


}