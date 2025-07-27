<?php
require_once __DIR__ . "/../../models/admissions/carreras.php";

class carrerasService {
    private $model;

    public function __construct(){
        $this->model = new Carrera();
    }
    
    //funcion pra registrar una nueva carrera 
    public function registrarCarrera($nombre){
        if(empty(trim($nombre))){
            return ['error' => true , 'mensaje' => 'no se identifica ningun nombre' ];
        }
        if($this->model->existeNombre($nombre)){
            return ['error'=> true , 'mensaje' => 'El nombre de la carrera ya se encuentra registrado en BD'];
        }
        $resultado = $this->model->insertCarrera($nombre);
        return $resultado ? ['error' => false , 'mensaje' => "Registro insertado con exito"] 
                          : ['error' => true , 'mensaje' => 'Error al insertar nuevo registro'];
    }

    //funcion para  cargar carreras disponibles 
      public function getAllCarrera() {
        try {
            $resultado = $this->model->getAllCarreras();
            
            if (empty($resultado)) {
                return ['error' => true, 'mensaje' => 'No hay carreras registradas'];
            }
            
            return ['error' => false, 'data' => $resultado];
            
        } catch (Exception $e) {
            return [
                'error' => true, 
                'mensaje' => 'Error del servidor: ' . $e->getMessage()
            ];
        }
      }

      //Funcion para obtener carreras de un centro especifico
public function getCarrerasPorCentro($idCentro) {
    try {
        $resultado = $this->model->getCarrerasPorCentro($idCentro);
        if (empty($resultado)) {
            return ['error' => true, 'mensaje' => 'No hay carreras para este centro regional'];
        }
        return ['error' => false, 'data' => $resultado];
    } catch (Exception $e) {
        return [
            'error' => true,
            'mensaje' => 'Error del servidor: ' . $e->getMessage()
        ];
    }
}

//funcion para obtener centros regionales
public function getCentrosRegionales() {
    try {
        $resultado = $this->model->getCentrosRegionales();
        if (empty($resultado)) {
            return ['error' => true, 'mensaje' => 'No hay centros regionales registrados'];
        }
        return ['error' => false, 'data' => $resultado];
    } catch (Exception $e) {
        return [
            'error' => true,
            'mensaje' => 'Error del servidor: ' . $e->getMessage()
        ];
    }
}

      //funcion para cargar todas las demas carreras excepto la seleccionada incialmente 
      public function getRestCarreras($id){
        try{
            $resultado = $this->model->getAllExcept($id);
            if (empty($resultado)) {
                return ['error'=> true, 'mensaje' => 'el id esta vacio '];
            }

            return ['error' => false, 'data'=>$resultado];
        }
        catch(Exception $e){
            return [
                'error' => true, 
                'mensaje' => 'Error del servidor: ' . $e->getMessage()
            ];
        }

      }
}