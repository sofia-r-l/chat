<?php

require_once __DIR__ . "/../../../config/db_academic_config.php";

class TagByResource
{

    private $pdo;

    public function __construct()
    {
        $this->pdo = $GLOBALS['pdo'];
    }


    /**
     * Registra la relacion que existe una etiqueta con mas de un recurso
     * 
     * @param int $idResource id del recurso que al que se asocia la etiqueta 
     * @param int $idTag id de etiqueta relacionada con un recurso 
     * @return int  del ultimo registro 
     * @throws PDOException Si ocurre un error en la base de datos
     * @throws Exception Si ocurre un error inesperado
     */
    public function insertNewTagByResource($idTag, $idResource)
    {

        //echo json_encode( "id etiquetea: ". $idEtiqueta ." y el id del recurso". $idRecurso ."");
        try {

            $sql = "INSERT INTO TBL_ETIQUETAS_X_RECURSOS (ID_ETIQUETA, ID_RECURSO) 
                VALUES (:idEtiqueta, :idRecurso)";

            $stmt = $this->pdo->prepare($sql);

            $stmt->bindValue(":idEtiqueta", (int) $idTag, PDO::PARAM_INT);
            $stmt->bindValue(":idRecurso", (int) $idResource, PDO::PARAM_INT);

            if (!$stmt->execute()) {
                $errorInfo = $stmt->errorInfo();

                // Manejo específico para violación de clave única
                if ($errorInfo[0] == '23000' && strpos($errorInfo[2], 'unique_etiqueta_recurso') !== false) {
                    throw new Exception("Esta etiqueta ya está asociada al recurso especificado");
                }
                error_log("Error en insertNewTagByResource: " . print_r($errorInfo, true));
                throw new Exception("Error al asociar etiqueta con recurso");

            }


            return $this->pdo->lastInsertId();

        } catch (PDOException $e) {
            error_log("PDOException en insertNewTagByResource: " . $e->getMessage());
            throw new Exception("Error de base de datos al coordinar la consulta para  asociar etiqueta con recurso");
        }
    }


    /**
     * Obtiene todas las etiqeutas asociadas a una recurso
     *  
     * @param int $idResource id de etiqueta relacionada con un recurso 
     * @return array  int de ids de las etiquetas
     * @throws PDOException Si ocurre un error en la base de datos
     * @throws Exception Si ocurre un error inesperado
     */
    public function getAllTagByResource($idResource)
    {
        try {
            $sql = "SELECT 
                        e1.ID_ETIQUETA,
                        e.NOMBRE_ETIQUETA
                    FROM 
                        TBL_ETIQUETAS_X_RECURSOS e1
                    JOIN 
                        TBL_ETIQUETAS e ON e1.ID_ETIQUETA = e.ID_ETIQUETA
                    WHERE 
                        e1.ID_RECURSO = :idResource";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(":idResource", (int) $idResource, PDO::PARAM_INT);

            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);


            if (empty($resultados)) {
                return [];
            }

            return $resultados;

        } catch (PDOException $e) {
            error_log("PDOException en getAllTagsByResource" . $e->getMessage());
            throw new Exception("Error de base de datos al coordinar la consulta para  obtener etiquetas de recurso");
        }
    }
}