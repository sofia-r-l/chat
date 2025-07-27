<?php

require_once __DIR__ . "/../../../config/db_academic_config.php";

class ResourceByClass
{

    private $pdo;
    public function __construct()
    {
        $this->pdo = $GLOBALS['pdo'];
    }

     /**
     * Registra la relacion que exite entre una Asignatura y los distintos recursos (recursos cuyo contenido tiene relacion con mas de una calse )  
     * @param int $idRecurso id el regusro guardado en BD tabal TBL_RECURSOS_Virtuales
     * @param int $idAsignatura id del registro asignatura en TBL_ASIGNATURAS
     * @return int $id asignado al nuevo regitros de la relacion idAsignatira<->idRecurso
     * @throws PDOException Si ocurre un error en la base de datos
     * @throws Exception Si ocurre un error inesperado 
     */
    public function insertNewResourceByClass($idRecurso, $idAsignatura)
    {
        try {

            $sql = "INSERT INTO TBL_RECURSOS_X_CLASE (ID_RECURSO, ID_ASIGNATURA) 
                VALUES (:idRecurso, :idAsignatura)";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(":idRecurso", (int) $idRecurso, PDO::PARAM_INT);
            $stmt->bindValue(":idAsignatura", (int) $idAsignatura, PDO::PARAM_INT);

            if (!$stmt->execute()) {
                throw new Exception("Error al ejecutar la inserción");
            }

            return $this->pdo->lastInsertId(); // Retorna el ID

        } catch (PDOException $e) {
            error_log("Error al asociar recurso-asignatura: " . $e->getMessage());
            throw new Exception("Error al guardar la relación recurso-asignatura");
        }
    }
}