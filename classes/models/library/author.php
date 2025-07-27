<?php
require_once __DIR__ . "/../../../config/db_academic_config.php";

class Author
{

    private $pdo;


       public function __construct()
    {
        $this->pdo = $GLOBALS['pdo'];
    }

     /**
     * Agrega un nuevo autor que no probablemente no existe en la BD
     * @param string $autor nombre del autor
     * @return int $id asignado al nuevo regitros de autor
     * @throws PDOException Si ocurre un error en la base de datos
     * @throws Exception Si ocurre un error inesperado 
     */
    public function insertNewAuthor($autor)
    {
        try {
            $sql = "INSERT INTO TBL_AUTORES (AUTOR) VALUES (:autor)";
            $stmt = $this->pdo->prepare($sql);

            $stmt->bindValue(":autor", $autor);

            if (!$stmt->execute()) {
                $errorInfo = $stmt->errorInfo();
                error_log("Error en ejecución SQL: " . print_r($errorInfo, true));
                throw new Exception($errorInfo[2]);
            }

            return $this->pdo->lastInsertId();

        } catch (PDOException $e) {
            error_log("Error PDO en insertNewAuthor: " . $e->getMessage());
            throw new Exception("Error de base de datos al coordinar la consulta TBL_author: " . $e->getMessage());
        }
    }



    /**
     * obtiene todas las concicendias 
     * 
     * @param string $seccionString segmento de busqueda coicidencias
     * @return array coicidencias
     * @throws PDOException Si ocurre un error en la base de datos
     * @throws Exception Si ocurre un error inesperado
     */
    public function getSeccionAnyAuthor($seccionString)
    {


        try {
            $sql = "SELECT 
                    a.ID_AUTOR as id,
                    a.AUTOR as nombre
                FROM 
                    TBL_AUTORES a
                WHERE 
                    (a.AUTOR LIKE :search )
            
                ORDER BY 
                    a.AUTOR
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
            error_log("Error PDO en getSeccionAnyAuthor: " . $e->getMessage());
            throw new Exception("Error de base de datos: " . $e->getMessage());
        } catch (Exception $e) {
            error_log("Error general en getSeccionAnyAuthor: " . $e->getMessage());
            throw $e;
        }
    }




}