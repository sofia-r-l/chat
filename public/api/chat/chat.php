<?php
require_once '../config/session.php';
require_once '../config/db.php';
require_once '../models/userModel.php';
require_once '../models/messageModel.php';
require_once '../services/ChatService.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

$usuario = new Usuario($pdo);
$mensaje = new Mensaje($pdo);
$chat = new ChatService($usuario, $mensaje);

$method = $_SERVER['REQUEST_METHOD'];


if ($method === 'GET') {
    if (!isset($_SESSION['usuario_id'])) {
        echo json_encode(['status' => false, 'error' => 'No autorizado']);
        exit;
    }

    if (isset($_GET['receptor_id'])) {
        $receptorId = (int) $_GET['receptor_id'];
        $emisorId = $_SESSION['usuario_id'];
        $mensajes = $chat->obtenerMensajesChat($emisorId, $receptorId);
        echo json_encode($mensajes);
        exit;
    }

    $response = $chat->obtenerContactosClasificados($_SESSION['usuario_id']);
    echo json_encode([
        'status' => true,
        'contact_Active' => array_merge($response['online'], $response['offline'])
    ]);
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $emisor = $_SESSION['usuario_id'];
    $receptor = $data['receptor_id'];
    $mensajeTexto = $data['mensaje'];

    $chat->enviarMensajeChat($emisor, $receptor, $mensajeTexto);
    echo json_encode(['status' => true]);
}