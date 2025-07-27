<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../../../../../classes/controllers/library/tagController.php';

try {
    $controller = new tagController();
    $controller->handleRequest();
   
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error interno animo : ' . $e->getMessage(),
        'trace' => $e->getTraceAsString() // Solo para desarrollo
    ]);
}