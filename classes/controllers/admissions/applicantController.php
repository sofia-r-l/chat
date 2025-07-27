<?php

require_once __DIR__ . "/../../services/admissions/applicantService.php";

class AplicantController
{
    private $archivoService;

    public function __construct()
    {
        $this->archivoService = new ApplicantService();
        error_log("Paso 1: Llegó al controlador"); // En el constructor del controlador
        error_log("POST data: " . print_r($_POST, true));
        error_log("FILES data: " . print_r($_FILES, true));
    }
    
    public function handlePostRequest()
    {
        try {
            error_log("=== INICIANDO HANDLE POST ===");

            // Debug avanzado para ver qué está llegando
            error_log("Método: " . $_SERVER['REQUEST_METHOD']);
            error_log("Content-Type: " . $_SERVER['CONTENT_TYPE']);
            error_log("POST raw: " . file_get_contents('php://input'));
            error_log("POST data: " . print_r($_POST, true));
            error_log("FILES data: " . print_r($_FILES, true));

            // Verificar que sea POST
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Método no permitido', 405);
            }

            // Verificar campos obligatorios (adaptado para multipart/form-data)
            $requiredFields = [
                'identidad', 'nombre', 'telefono', 'correo', 'centro_regional', 'carrera_p', 'carrera_s'];
            $missingFields = [];

            foreach ($requiredFields as $field) {
                if (!isset($_POST[$field])) {
                    $missingFields[] = $field;
                }
            }

            if (!empty($missingFields)) {
                throw new Exception("Faltan campos obligatorios: " . implode(', ', $missingFields), 400);
            }

            // Verificar archivo
            if (empty($_FILES['certificado'])) {
                throw new Exception('El certificado es requerido', 400);
            }

            // Procesar archivo
            $archivoProcesado = $this->archivoService->procesarArchivo($_FILES['certificado']);
            if ($archivoProcesado['error']) {
                throw new Exception($archivoProcesado['mensaje'], 400);
            }

            // Insertar en la base de datos
            $resultado = $this->archivoService->insertarSolicitud($_POST, $archivoProcesado);

            if ($resultado['error']) {
                throw new Exception($resultado['mensaje'], 500);
            }
            $GLOBALS['pdo']->query("CALL asignar_revisor_a_aspirante()");
            // Éxito
            echo json_encode([
                'success' => true,
                'mensaje' => 'Solicitud registrada exitosamente'
            ]);

        } catch (Exception $e) {
            error_log("ERROR en handlePostRequest: " . $e->getMessage());
            http_response_code(is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
            echo json_encode([
                'error' => true,
                'mensaje' => $e->getMessage()
            ]);
        }
    }


}