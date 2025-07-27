<?php
require_once __DIR__ . '/../../../config/conexion.php';



try {
    $stmt = $pdo->query("
        SELECT cc.Codigo_Clase, a.NOMBRE_ASIGNATURA
        FROM TBL_ASIGNATURA a
        JOIN Codigo_Clase cc ON a.ID_CC = cc.ID_CC
    ");

    $subjects = [];
    while ($row = $stmt->fetch()) {
        $subjects[$row['Codigo_Clase']] = $row['NOMBRE_ASIGNATURA'];
    }

    echo json_encode($subjects);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

/* $subjects = [
    "IS410" => "Análisis y Diseño de Sistemas",
    "IS311" => "Estructura de Datos",
    "IS211" => "Programación I",
    "IS212" => "Programación II",
    "IS101" => "Introducción a la Informática"
];

echo json_encode([
    'success' => true,
    'data' => $subjects
]);
 */