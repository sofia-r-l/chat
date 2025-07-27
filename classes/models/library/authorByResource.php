<?php
require_once __DIR__ . '/../../../config/db_academic_config.php';

class AuthorByResource
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = $GLOBALS['pdo'];
    }



    /**
     * Registra la relacion que exite entre un autor y los distintos recursos  
     * @param int $idautor id del autor
     * @param int $idRecurso id del registro en la tabal TBL_RECURSOS_Virtuales
     * @return int $id asignado al nuevo regitros de la relacion idAutor<->idRecurso
     * @throws PDOException Si ocurre un error en la base de datos
     * @throws Exception Si ocurre un error inesperado 
     */
    public function insertNewAuthorByResource($idAutor, $idRecurso)
    {
        try {
            $sql = "INSERT INTO TBL_AUTORES_X_RECURSOS (ID_AUTOR, ID_RECURSO) 
                VALUES (:idAutor, :idRecurso)";

            $stmt = $this->pdo->prepare($sql);

            $stmt->bindValue(":idAutor", (int) $idAutor, PDO::PARAM_INT);
            $stmt->bindValue(":idRecurso", (int) $idRecurso, PDO::PARAM_INT);

            if (!$stmt->execute()) {
                $errorInfo = $stmt->errorInfo();
                error_log("Error en ejecución SQL: " . print_r($errorInfo, true));
                throw new Exception($errorInfo[2]);
            }

            return $this->pdo->lastInsertId();

        } catch (PDOException $e) {
            error_log("Error PDO en insertNewAuthorByResource: " . $e->getMessage());
            throw new Exception("Error de base de datos al coordinar insercion en TBL_AUTHOR_BY_RESOURCE: " . $e->getMessage());
        }
    }


    /**
     * Obtiene todos los autores asociadas a una recurso
     *  
     * @param int $idResource id de etiqueta relacionada con un recurso 
     * @return array  int de ids de los autores
     * @throws PDOException Si ocurre un error en la base de datos
     * @throws Exception Si ocurre un error inesperado
     */
    public function getAllAuthorByResource($idResource)
    {
        try {
            $sql = "SELECT 
                        a1.ID_AUTOR,
                        a.AUTOR
                    FROM 
                        TBL_AUTORES_X_RECURSOS a1
                    JOIN 
                        TBL_AUTORES a ON a1.ID_AUTOR = a.ID_AUTOR
                    WHERE 
                        a1.ID_RECURSO = :idResource";
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
