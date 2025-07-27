<?php


require_once __DIR__ . "/../../../config/db_academic_config.php";

class Subjects {


    private $pdo ;

    public function __construct(){
        $this->pdo = $GLOBALS['pdo'];
    }
   

     /**
     * inserta una nueva etiqueta en la base de datos
     * 
     * @param string $nombreEtiqueta nombre de nueva etiqueta
     * @return array id del ultimo registro en BD
     * @throws PDOException Si ocurre un error en la base de datos
     * @throws Exception Si ocurre un error inesperado
     */
    public function getSeccionAnySubjects($seccionString)
    {


      try {
        $sql = "SELECT 
                    a.ID_ASIGNATURA as id,
                    a.CODIGO_ASIGNATURA as codigo,
                    a.NOMBRE_ASIGNATURA as nombre,
                    a.ID_CARRERA
                FROM 
                    TBL_ASIGNATURAS a
                WHERE 
                    (a.CODIGO_ASIGNATURA LIKE :search OR a.NOMBRE_ASIGNATURA LIKE :search)
                    AND a.ESTADO = 1
                ORDER BY 
                    a.CODIGO_ASIGNATURA
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
        error_log("Error PDO en getSeccionAnySubjects: " . $e->getMessage());
        throw new Exception("Error de base de datos: " . $e->getMessage());
    } catch (Exception $e) {
        error_log("Error general en getSeccionAnySubjects: " . $e->getMessage());
        throw $e;
    }
    }



}