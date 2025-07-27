<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../../../../../classes/controllers/library/authorByResourceController.php';

try {
    $controller = new AuthorByResourceController();
    $controller->handleRequest();
   
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error interno animo si puedo : ' . $e->getMessage(),
        'trace' => $e->getTraceAsString() // Solo para desarrollo
    ]);
}