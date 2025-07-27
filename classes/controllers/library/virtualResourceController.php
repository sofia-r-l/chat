<?php

require_once __DIR__ . "/../../services/library/virtualResourceService.php";
require_once __DIR__ . "/../../services/library/tagService.php";
require_once __DIR__ . "/../../services/library/tagByResourceService.php";

class VirtualResourceController
{

    private $defaultValues = [
        'id_docente' => 1, // Valor temporal mientras implemento obtener el id del docente 
        'tipo_recurso' => 2 // PDF (tmabien fijado por el momento
    ];

    private $service;
    private $tagService;
    private $tagByResource;

    public function __construct()
    {
        $this->service = new VirtualResourceService;
        $this->tagService = new TagService;
        $this->tagByResource = new TagByResourceService;
    }

    public function handleRequest()
    {
        header('Content-Type: application/json');

        try {
            switch ($_SERVER['REQUEST_METHOD']) {
                case 'GET':
                    if (isset($_GET['id_resource'])) {
                        $this->getInfoResource((int) $_GET['id_resource']);
                    }
                    if (isset($_GET['id_type_resource'])) {
                        $this->getResourceByType((int) $_GET['id_type_resource']);
                    }
                    if (isset($_GET['id_resource_get'])) {
                        $this->getToViewOrDownload((int) $_GET['id_resource_get']);
                    }


                    break;
                case 'POST':
                    $this->uploadResource();
                    break;
                case 'PUT':
                    $this->editarLibro();
                    break;
                case 'DELETE':
                    $input = json_decode(file_get_contents('php://input'), true);
                    $id_resource = $input['id_resource'] ?? null;
                    $this->deleteResource($id_resource);
                    break;
                default:
                    http_response_code(405);
                    throw new Exception("Método no permitido");
            }
        } catch (Exception $e) {
            http_response_code($e->getCode() ?: 500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function uploadResource()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                throw new Exception("Método no permitido. Use POST");
            }

            $this->validateInputs();

            // Obtener ID docente
            $idDocente = isset($_POST['id_docente']) && is_numeric($_POST['id_docente'])
                ? (int) $_POST['id_docente']
                : $this->defaultValues['id_docente'];

            // Procesar y guardar archivo principal
            $resultado = $this->service->processAndSaveFile(
                $_FILES['archivo'],
                htmlspecialchars($_POST['titulo']),
                $idDocente,
                2
            );

            //echo json_encode($resultado);

            if (!isset($resultado['id'])) {
                throw new Exception("No se pudo obtener el ID del recurso creado");
            }

            $idRecurso = $resultado['id'];
            $etiquetasProcesadas = ['nuevas' => [], 'existentes' => []];

            // Procesar etiquetas si fueron enviadas
            if (!empty($_POST['etiquetas'])) {
                $etiquetasData = json_decode($_POST['etiquetas'], true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new InvalidArgumentException("Formato de etiquetas inválido");
                }

                // Procesar etiquetas nuevas
                if (!empty($etiquetasData['nuevas'])) {
                    foreach ($etiquetasData['nuevas'] as $etiquetaNombre) {
                        $etiquetaNombre = trim($etiquetaNombre);
                        if (!empty($etiquetaNombre)) {
                            // Insertar nueva etiqueta
                            $tagResult = $this->tagService->createTag($etiquetaNombre);
                            // Asociar con recurso


                            $associationResult = $this->tagByResource->associateTagWithResource($tagResult['data']['tag_id'], $idRecurso);
                            // echo json_encode($associationResult['data']);
                            $etiquetasProcesadas['nuevas'][] = [
                                'nombre' => $etiquetaNombre,
                                'id_etiqueta' => $tagResult['data']['tag_id'],
                                'id_asociacion' => $associationResult['data']['relation_id']
                            ];
                        }
                    }
                }

                // Procesar etiquetas existentes
                if (!empty($etiquetasData['existentes'])) {
                    foreach ($etiquetasData['existentes'] as $idEtiqueta) {
                        $idEtiqueta = (int) $idEtiqueta;
                        if ($idEtiqueta > 0) {
                            // Asociar etiqueta existente
                            $associationResult = $this->tagByResource->associateTagWithResource($idEtiqueta, $idRecurso);
                            // echo json_encode($associationResult);
                            $etiquetasProcesadas['existentes'][] = [
                                'id_etiqueta' => $idEtiqueta,
                                'id_asociacion' => $associationResult['data']['relation_id']
                            ];
                        }
                    }
                }
            }

            http_response_code(201);
            echo json_encode([
                'success' => true,
                'data' => [
                    'id_recurso' => $idRecurso,
                    'metadata' => $resultado['metadata'],
                    'etiquetas' => $etiquetasProcesadas
                ]
            ]);

        } catch (InvalidArgumentException $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
    ///////////////////////////////////////////////////////////////////



























    private function validateInputs()
    {

        if (!isset($_FILES['archivo'])) {
            throw new InvalidArgumentException("No se envió ningún archivo");
        }
        if ($_FILES['archivo']['error'] !== UPLOAD_ERR_OK) {
            throw new InvalidArgumentException("Error al subir el archivo: Código " . $_FILES['archivo']['error']);
        }


        if (empty($_POST['titulo'])) {
            throw new InvalidArgumentException("El título no puede estar vacío");
        }
        if (strlen($_POST['titulo']) < 5) {
            throw new InvalidArgumentException("El título debe tener al menos 5 caracteres");
        }

        return true;
    }

    public function getResourceByType($id_type_resource)
    {
        try {
            if (!isset($id_type_resource)) {
                throw new Exception("Parámetro id_type_resource requerido", 400);
            }

            if (!is_numeric($id_type_resource) || $id_type_resource <= 0) {
                throw new InvalidArgumentException("ID de tipo de recurso NO válido");
            }

            $recursos = $this->service->getAllResourcesByType((int) $id_type_resource);

            if (empty($recursos)) {
                http_response_code(404);
                echo json_encode([
                    'success' => true,
                    'data' => [],
                    'message' => 'No se encontraron recursos para este tipo'
                ]);
                return;
            }

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'data' => $recursos
            ]);

        } catch (Exception $e) {
            throw new Exception("Error al obtener recursos: " . $e->getMessage(), 500);
        }
    }




    public function getInfoResource($id_resource)
    {
        try {
            // 1. Validar método HTTP
            if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
                http_response_code(405);
                throw new RuntimeException("Método no permitido. Solo se aceptan solicitudes GET");
            }

            // 2. Validar parámetro de entrada
            if (!isset($id_resource) || empty($id_resource)) {
                http_response_code(400);
                throw new InvalidArgumentException("El parámetro 'id_resource' es requerido");
            }

            if (!is_numeric($id_resource)) {
                http_response_code(400);
                throw new InvalidArgumentException("El ID del recurso debe ser numérico");
            }

            $id_resource = (int) $id_resource;
            if ($id_resource <= 0) {
                http_response_code(400);
                throw new InvalidArgumentException("El ID del recurso debe ser un número positivo");
            }

            // 3. Obtener datos del servicio
            $recurso = $this->service->getInfoResource($id_resource);

            // 4. Validar respuesta del servicio
            if (empty($recurso)) {
                http_response_code(404);
                throw new RuntimeException("Recurso no encontrado con el ID proporcionado");
            }

            // 5. Estructurar respuesta exitosa
            http_response_code(200);
            header('Content-Type: application/json');

            return json_encode([
                'success' => true,
                'status' => 200,
                'message' => 'Recurso obtenido correctamente',
                'data' => $recurso
            ]);

        } catch (InvalidArgumentException $e) {
            // Errores de validación de parámetros
            http_response_code(400);
            return json_encode([
                'success' => false,
                'status' => 400,
                'message' => $e->getMessage(),
                'data' => null
            ]);

        } catch (RuntimeException $e) {
            // Errores de lógica/negocio
            $statusCode = http_response_code() ?: 500;
            return json_encode([
                'success' => false,
                'status' => $statusCode,
                'message' => $e->getMessage(),
                'data' => null
            ]);

        } catch (Exception $e) {
            // Errores inesperados
            http_response_code(500);
            error_log("Error en getInfoResource: " . $e->getMessage());
            return json_encode([
                'success' => false,
                'status' => 500,
                'message' => 'Error interno del servidor',
                'data' => null
            ]);
        }
    }


    //tenemos pensado validar si el idusuario puede elimar ese recurso 
    public function deleteResource($id_resource)
    {

        if (empty($id_resource) || !is_numeric($id_resource)) {
            http_response_code(400); // Bad Request
            return json_encode(['error' => 'ID inválido o no proporcionado']);
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            http_response_code(405); // Method Not Allowed
            return json_encode(['error' => 'Método no permitido. Use DELETE']);
        }

        try {

            if (!$this->service->existResourceById($id_resource)) {
                http_response_code(404); // Not Found
                return json_encode(['error' => 'Recurso no encontrado']);
            }

            // Lógica de eliminación
            $recurso = $this->service->deleteResourceById($id_resource);

            if (!$recurso) {
                http_response_code(400);
            }

            http_response_code(204);
            //return null; 
            return json_encode(['success' => true]);

        } catch (RuntimeException $e) {
            http_response_code(400);
            return json_encode(['error' => $e->getMessage()]);
        } catch (Exception $e) {
            http_response_code(500);
            return json_encode(['error' => 'Error interno del servidor']);
        }

    }


    /**
     * Retrieve a resource for viewing or downloading
     * 
     * @param int $id_resource Resource identifier
     * @return blob JSON response or file content
     */
    public function getToViewOrDownload($id)
    {
        try {
   
            if (empty($id) || !is_numeric($id)) {
                throw new Exception("ID inválido", 400);
            }

            $result = $this->service->getFileById($id);

            if (!$result['success'] || empty($result['file'])) {
                throw new Exception($result['message'] ?? "Archivo no encontrado", 404);
            }

              $file = $result['file'];
        
        // Determinar si es para visualización o descarga
        $mode = $_GET['mode'] ?? 'download';
        
        if ($mode === 'view') {
            // Modo visualización (sin descarga)
            header('Content-Type: ' . $file['mime']);
            header('Content-Disposition: inline; filename="' . $file['name'] . '"');
            header('Content-Length: ' . $file['size']);
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        } else {
            // Modo descarga (comportamiento actual)
            header('Content-Type: ' . $file['mime']);
            header('Content-Disposition: attachment; filename="' . $file['name'] . '"');
            header('Content-Length: ' . $file['size']);
            header('Content-Transfer-Encoding: binary');
        }

        echo $file['content'];
        exit;

        } catch (Exception $e) {
            
            header_remove();
            header('Content-Type: application/json');
            http_response_code($e->getCode() ?: 500);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage(),
                'debug' => ['id_buscado' => $id]
            ]);
            exit;
        }
    }





}




///* 
     