<?php



#        if ($resultado['error']) {
#            http_response_code(500);
#        }
#        echo json_encode($resultado);
#    }
#}
#

require_once __DIR__ . "/../../services/admissions/carrerasService.php";

class CarreraController
{
    private $carreraService;

    public function __construct()
    {
        $this->carreraService = new CarrerasService();
    }

    public function handleGetRequest()
    {
        $res = $this->carreraService->getAllCarrera();

        // Solo esto debe enviarse como respuesta
        header('Content-Type: application/json');
        if ($res['error']) {
            http_response_code(500);
        }
        echo json_encode($res);
    }


    public function handleGetRestCarrera($id)
    {
        $res = $this->carreraService->getRestCarreras($id);

        header('Content-Type: application/json');
        if ($res['error']) {
            http_response_code(500);
        }
        echo json_encode($res);
    }

//función para obtener centros regionales
public function handleGetCentrosRegionales()
{
    $res = $this->carreraService->getCentrosRegionales();

    header('Content-Type: application/json');
    if ($res['error']) {
        http_response_code(500);
    }
    echo json_encode($res);
}
    public function handleGetCarrerasPorCentro($idCentro)
{
    $res = $this->carreraService->getCarrerasPorCentro($idCentro);

    header('Content-Type: application/json');
    if ($res['error']) {
        http_response_code(500);
    }
    echo json_encode($res);
}

}