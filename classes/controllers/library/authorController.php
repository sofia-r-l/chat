 <?php

 
 require_once __DIR__ . "/../../services/library/authorService.php";

class AuthorController
{

    private $service;

    public function __construct()
    {
        $this->service = new AuthorService();
    }


    public function handleRequest()
    {
        header('Content-Type: application/json');

        try {
            switch ($_SERVER['REQUEST_METHOD']) {
                case 'GET':
                    if (isset($_GET['string_search'])) {
                        $this->handleGetRequest();
                    } else {
                      //  $this->getAllTags();
                    }
                    break;
                case 'POST':
                   // $this->createTag();
                    break;
                case 'PUT':
                    //$this->editarLibro();
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
 
 

    private function handleGetRequest()
    {
        if (!isset($_GET['string_search'])) {
            throw new Exception("Parámetro 'string_search' faltante", 400);
        }

        $searchTerm = $_GET['string_search'];
        //$typeInput = $_GET['type_input'] ?? 'etiquetas'; // Valor por defecto

        if (strlen($searchTerm) < 2) {
            throw new Exception("El término de búsqueda debe tener al menos 2 caracteres", 400);
        }

        $results = $this->service->getMatchAuthor($searchTerm);

        echo json_encode([
            'success' => true,
            'data' => $results
        ]);
    }

}
