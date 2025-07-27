<?php
session_start();
header('Content-Type: application/json');

// Simulación de una base de datos de usuarios
$usuarios = [
    ['usuario' => 'admin', 'password' => 'admin123'],
    ['usuario' => 'user', 'password' => 'user123']
];

// Obtener datos del formulario
$usuario = $_POST['usuario'] ?? '';
$password = $_POST['password'] ?? '';

// Validar credenciales
foreach ($usuarios as $user) {
    if ($user['usuario'] === $usuario && $user['password'] === $password) {
        $_SESSION['usuario'] = $usuario;
        echo json_encode(['success' => true]);
        exit;
    }
}

// Si llegamos aquí, las credenciales son inválidas
echo json_encode(['error' => true, 'mensaje' => 'Usuario o contraseña incorrectos']);