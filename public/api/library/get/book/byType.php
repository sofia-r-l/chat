<?php
header('Content-Type: application/json');

// Ruta corregida (ajusta según tu estructura real)
require_once __DIR__ . '/../../../../../classes/controllers/library/virtualResourceController.php';

try {
    $controller = new VirtualResourceController();
    $controller->handleRequest();
   
} catch (Throwable $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'success' => false,
        'error' => 'Error interno animo : ' . $e->getMessage()
    ]);
}