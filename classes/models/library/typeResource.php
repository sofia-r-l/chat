<?php

require_once __DIR__ . "/../../../config/db_academic_config.php";

class TypeResource
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = $GLOBALS['PDO'];
    }
    
   
    /**
     * Verifica la existencia de un tipo de recurso por id 
     * 
     * @return boolean true si este existe 
     * @throws PDOException Si ocurre un error en la base de datos
     * @throws Exception Si ocurre un error inesperado
     */
    public function existTypeResource($id)
    {
        try {
            $sql = "SELECT * FROM TBL_TIPO_RECURSO_VIRTUAL WHERE ID_TIPO_RECURSO_VIRTUAL = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);
            if (!$stmt->execute()) {
                $errorInfo = $stmt->errorInfo();
                error_log("Error en existTypeResource" . print_r($errorInfo, true));
                throw new Exception("Error al verificar existencia tipo recurso");
            }
            return true;

        } catch (PDOException $e) {
            error_log("PDOException en existTypeResource" . $e->getMessage());
            throw new Exception("Error al coordinar consulta en existTypeResource - model");
        }
    }


    /**
     * Obtiene todas los recursos de un mismo tipo
     * 
     * @param string nombre del tipo de recurso 
     * @return array Listado de recursos 
     * @throws PDOException Si ocurre un error en la base de datos
     * @throws Exception Si ocurre un error inesperado
     */
    public function getResourcesTypeId($typeResource)
    {
        try {
            $sql = "SELECT ID_TIPO_RECURSO 
                    FROM TBL_TIPOS_RECURSO 
                    WHERE LOWER(TRIM(TIPO_RECURSO)) = LOWER(TRIM(:nombre_tipo)) 
                    LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(":nombre_tipo", $typeResource, PDO::PARAM_STR);

            if (!$stmt->execute()) {
                $errorInfo = $stmt->errorInfo();
                error_log("Error al ejecutar en typeResource - Modelo" . print_r($errorInfo, true));
                throw new Exception("Error al obtener id de tipo recurso");
            }

            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("PDOException en getResourceTypeId - Modelo" . $e->getMessage());
            throw new Exception("Error al obtener id de tipo recurso");
        }

    }
}