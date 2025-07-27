<?php

require_once __DIR__ . '/../../../../../classes/controllers/library/authorController.php';

// Encabezados generales de la API
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');


try {
    $controller = new AuthorController();
    $controller->handleRequest();
   
} catch (Throwable $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'success' => false,
        'error' => 'Error interno en byMatch author - API  : ' . $e->getMessage()
    ]);
}