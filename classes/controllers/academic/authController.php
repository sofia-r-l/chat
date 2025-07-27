<?php
require_once '../app/models/student.php';

class AuthController {
    public function login($email, $password) {
        $user = User::findByEmail($email);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['ID_STUDENT'] = $user['id'];
            $_SESSION['USERNAME'] = $user['username'];
            session_regenerate_id(true); // ✅ Protección adicional

            echo json_encode(['status' => 'success']);
        } else {
            http_response_code(401);
            echo json_encode(['status' => 'fail']);
        }
    }

    public function logout() {
        session_unset();     // Elimina variables de $_SESSION
        session_destroy();   // Elimina la sesión completamente

        echo json_encode(['status' => 'logout']);
    }
}
