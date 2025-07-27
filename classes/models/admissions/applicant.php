<?php
require_once __DIR__ . "/../../../config/db_admissions_config.php";



class Applicant
{
    private $pdo;


    public function __construct()
    {
        $this->pdo = $GLOBALS['pdo'];
    }


public function insertRequest(
    $identidad,
    $nombre,
    $telefono,
    $correo_personal,
    $centro_regional,
    $carrera_principal,
    $carrera_secundaria,
    $certificado,
    $tipo_archivo,
    $tamano_archivo,
    $ancho_px,
    $alto_px,
    $estado_solicitud = "pendiente",
    $revisor_asignado = null

) {
    try {
        $sql = "INSERT INTO aspirantes 
                (identidad, nombre, telefono, correo_personal, 
                 centro_regional, carrera_principal, carrera_secundaria, 
                 certificado, tipo_archivo, tamano_archivo, ancho_px, alto_px, 
                 estado_solicitud, revisor_asignado)
                VALUES 
                (:identidad, :nombre, :telefono, :correo_personal, 
                 :centro_regional, :carrera_principal, :carrera_secundaria, 
                 :certificado, :tipo_archivo, :tamano_archivo, :ancho_px, :alto_px, 
                 :estado_solicitud, :revisor_asignado)";

        $stmt = $this->pdo->prepare($sql);

        // Manejo especial para el BLOB
        $stmt->bindParam(":certificado", $certificado, PDO::PARAM_LOB);
        
        // Resto de parámetros
        $stmt->bindValue(":identidad", $identidad);
        $stmt->bindValue(":nombre", $nombre);
        $stmt->bindValue(":telefono", $telefono);
        $stmt->bindValue(":correo_personal", $correo_personal);
        $stmt->bindValue(":centro_regional", $centro_regional);
        $stmt->bindValue(":carrera_principal", (int)$carrera_principal, PDO::PARAM_INT);
        $stmt->bindValue(":carrera_secundaria", (int)$carrera_secundaria, PDO::PARAM_INT);
        $stmt->bindValue(":tipo_archivo", $tipo_archivo);
        $stmt->bindValue(":tamano_archivo", (int)$tamano_archivo, PDO::PARAM_INT);
        $stmt->bindValue(":ancho_px", $ancho_px !== null ? (int)$ancho_px : null, PDO::PARAM_INT);
        $stmt->bindValue(":alto_px", $alto_px !== null ? (int)$alto_px : null, PDO::PARAM_INT);
        $stmt->bindValue(":estado_solicitud", $estado_solicitud);
        $stmt->bindValue(":revisor_asignado", $revisor_asignado, PDO::PARAM_INT);

        if (!$stmt->execute()) {
            $errorInfo = $stmt->errorInfo();
            error_log("Error en ejecución SQL: " . print_r($errorInfo, true));
            throw new Exception($errorInfo[2]);
        }

        return $this->pdo->lastInsertId();

    } catch (PDOException $e) {
        error_log("Error PDO en insertRequest: " . $e->getMessage());
        throw new Exception("Error de base de datos: " . $e->getMessage());
    }
    
}
    //Funcion para verificar si un solicitud exite por el id 
    public function requestExistById($id){
        try{
            $sql= "SELECT COUNT(*) FROM aspirantes WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchColumn() > 0;
        }
        catch(PDOException $e){
            echo "Error al intentar coordinar existencia de la solicitud: ". $e->getMessage();
            return false;
        }
    }


    //funcion para identificar si la solicitud ya tiene revisor asignado 
    public function hasReviewerAssing($id) {
        try{

            if(!$this->requestExistById($id)){
                return false;
            }

            $sql = "SELECT estado_solicitud FROM aspirantes WHERE id = :id ";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
    
            $state = $stmt->fetchColumn();
            return $state ==='pendiente' || $state ==='rechazado';   

        }
        catch(PDOException $e){
            echo "Error al intentar coordinar validacion de revisor basandose en el estado de solicitud: ". $e->getMessage();
            return false;
        }
    }

    //funcion para asignar una revisor a una solicitud 
    public function assingReviewer($id, $reviewer, $newState){
        try { 

            if(!$this->hasReviewerAssing($id)){
                return false;
            }
           
            $sql = "UPDATE aspirantes SET estado_solicitud = :estado , revisor_asignado = :revisor WHERE id = :id ";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(":estado", $newState, PDO::PARAM_STR);
            $stmt->bindParam(":revisor", $reviewer, PDO::PARAM_int);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);

            $stmt->execute();

            return true;


        }
        catch(PDOException $e){
            echo " ". $e->getMessage();
            return false;
        }
    }
    







    //obtener toda las solicitudes asignadas a un revisor 
    public function getRequestByReviewer($id_reviewer)
    {    
        try{
      $sql = "SELECT 
                    a.id,
                    a.identidad,
                    a.nombre,
                    a.telefono,
                    a.correo_personal,
                    c.nombre AS nombre_centro,
                    cp.nombre AS nombre_carrera_principal,
                    cs.nombre AS nombre_carrera_secundaria,
                    a.tipo_archivo,
                    a.estado_solicitud,
                    a.revisor_asignado,
                    a.fecha_registro
                FROM aspirantes a
                LEFT JOIN centros_regionales c ON a.centro_regional = c.id
                LEFT JOIN carreras cp ON a.carrera_principal = cp.id
                LEFT JOIN carreras cs ON a.carrera_secundaria = cs.id
                WHERE a.revisor_asignado = :id_revisor";
       $stmt=$this->pdo->prepare($sql);
            $stmt->bindParam(":id_revisor", $id_reviewer, PDO::PARAM_INT);
            $stmt->execute();

            $response =$stmt->fetchAll(PDO::FETCH_ASSOC);

            return $response;
        }
        catch(PDOException $e){
            echo "Error al intentar coordinar obtener solicitudes por revisores: ". $e->getMessage();
        }
        
    }
}