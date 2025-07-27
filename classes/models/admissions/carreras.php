<?php
require_once __DIR__ . "/../../../config/db_admissions_config.php";

class carrera
{

    private $pdo;

    public function __construct()
    {
        $this->pdo = $GLOBALS['pdo']; //almacena la variable global que retorna la coneccion a la bd admission
    }


    //metodo para insertar una carreara a bd
    public function insertCarrera($nombre)
    {
        try {
            $sql = 'INSERT INTO CARRERAS (nombre) VALUES (:nombre)';
            $stmt = $this->pdo->prepare($sql);                      //prepara la consulta 

            //el marcador :nombre lo asociamos con la variable $nombre
            $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);

            return $stmt->execute();
        } catch (PDOException $e) {
            echo 'Error al insertar carrera : ' . $e->getMessage();
            return false;
        }
    }

    public function existeNombre($nombre)
    {
        try {
            $sql = "SELECT COUNT(*) FROM carreras WHERE LOWER(nombre) = LOWER(:nombre)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':nombre' => $nombre]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            echo "Error al verificar nombre: " . $e->getMessage();
            return true;
        }
    }

    //metodo para obter carrera por id 
    public function getCarrera($idCarrera)
    {

        try {
            $sql = 'SELECT * FROM carreras WHERE id = (:idCarrera)';
            $stmt = $this->pdo->prepare($sql);

            $stmt->bindParam(':idCarrera', $idCarrera, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            echo 'Error al obtener carrera por id :' . $e->getMessage();
            return null;
        }

    }

    //metodo para obtener todas las carreras 
    public function getAllCarreras()
    {
        try {
            $sql = 'SELECT * FROM carreras';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo 'Error al coordinar obtencion de todas las carreras a la base de datos' . $e->getMessage();
            return null;
        }
    }

    //metodo para obtener carreras exeptuando una 
    public function getAllExcept($idCarrera)
    {
        try {
            $sql = 'SELECT * FROM carreras WHERE id != (:idCarrera)';
            $stmt = $this->pdo->prepare($sql);

            $stmt->bindParam(':idCarrera', $idCarrera, PDO::PARAM_STR);
            return $stmt->execute();

        } catch (PDOException $e) {
            echo 'Error al obtener registros de carreras: ' . $e->getMessage();
            return null;
        }
    }

    
// Método para obtener carreras de un centro regional específico
public function getCarrerasPorCentro($idCentro)
{
    try {
        $sql = 'SELECT c.* 
                FROM carreras c
                INNER JOIN centro_carrera cc ON c.id = cc.carrera_id
                WHERE cc. centro_id = :idCentro';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':idCentro', $idCentro, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo 'Error al obtener carreras por centro regional: ' . $e->getMessage();
        return null;
    }
}
//Función para obtener todos los centros regionales
public function getCentrosRegionales()
{
    try {
        $sql = 'SELECT * FROM centros_regionales';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo 'Error al obtener centros regionales: ' . $e->getMessage();
        return null;
    }
}

}