<?php
#header('Content-Type: application/pdf');
#
#$bookId = $_GET['id'] ?? '';
#$safeBookId = preg_replace('/[^a-zA-Z0-9-_]/', '', $bookId);
#
#// 1. Verificar autenticación si es necesario
#// 2. Buscar en la base de datos la ruta del archivo PDF
#// 3. Validar que el usuario tiene permiso para verlo
#
#$filePath = __DIR__ . "/../../../../../upload{$safeBookId}.pdf";
#
#if (file_exists($filePath)) {
#    header('Content-Disposition: inline; filename="' . basename($filePath) . '"');
#    readfile($filePath);
#} else {
#    header('HTTP/1.0 404 Not Found');
#    echo 'PDF no encontrado';
#}
#exit;



##
##// Configuración de rutas (AJUSTA ESTA RUTA)
##$uploadDir = __DIR__ . '/../../../../../upload/';
##
##// Validar parámetro ID
##$bookId = $_GET['id'] ?? '';
##if (!preg_match('/^LIBRO_\d{6}$/', $bookId)) {
##    header("HTTP/1.0 400 Bad Request");
##    exit(json_encode(['error' => 'ID de libro inválido']));
##}
##
##// Cargar datos de libros
##$librosJson = file_get_contents(__DIR__.'/../../../../../upload/libros.json');
##$librosData = json_decode($librosJson, true);
##
##// Buscar el libro
##$libro = null;
##foreach ($librosData['libros'] as $item) {
##    if ($item['id'] === $bookId) {
##        $libro = $item;
##        break;
##    }
##}
##
##if (!$libro) {
##    header("HTTP/1.0 404 Not Found");
##    exit(json_encode(['error' => 'Libro no encontrado']));
##}
##
##// Construir ruta completa
##$pdfPath = $uploadDir . $libro['archivo'];
##
##// Verificar archivo
##if (!file_exists($pdfPath)) {
##    header("HTTP/1.0 404 Not Found");
##    exit(json_encode(['error' => 'Archivo no encontrado: '.$pdfPath]));
##}
##
##// Verificar tipo MIME
##$finfo = finfo_open(FILEINFO_MIME_TYPE);
##$mime = finfo_file($finfo, $pdfPath);
##finfo_close($finfo);
##
##if ($mime !== 'application/pdf') {
##    header("HTTP/1.0 415 Unsupported Media Type");
##    exit(json_encode(['error' => 'El archivo no es un PDF válido']));
##}
##
##// Enviar el PDF con los headers correctos
##header('Content-Type: application/pdf');
##header('Content-Disposition: inline; filename="'.$libro['nombre_original'].'"');
##header('Content-Length: ' . filesize($pdfPath));
##readfile($pdfPath);
##exit;




// Configuración de rutas
$uploadDir = realpath(__DIR__ . '/../../../../../upload/') . DIRECTORY_SEPARATOR;
if (!is_dir($uploadDir)) {
    header("HTTP/1.0 500 Internal Server Error");
    exit(json_encode(['error' => 'Directorio upload no existe']));
}

// Validar parámetro ID
$bookId = $_GET['id'] ?? '';
if (!preg_match('/^LIBRO_\d{6}$/', $bookId)) {
    header("HTTP/1.0 400 Bad Request");
    exit(json_encode(['error' => 'ID de libro inválido']));
}

// Cargar datos de libros con límite de tamaño
$librosJson = file_get_contents(__DIR__.'/../../../../../upload/libros.json', false, null, 0, 1024 * 1024);
if ($librosJson === false) {
    header("HTTP/1.0 500 Internal Server Error");
    exit(json_encode(['error' => 'Error al leer datos de libros']));
}

$librosData = json_decode($librosJson, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    header("HTTP/1.0 500 Internal Server Error");
    exit(json_encode(['error' => 'Datos de libros corruptos']));
}

// Buscar el libro
$libro = null;
foreach ($librosData['libros'] ?? [] as $item) {
    if ($item['id'] === $bookId) {
        $libro = $item;
        break;
    }
}

if (!$libro) {
    header("HTTP/1.0 404 Not Found");
    exit(json_encode(['error' => 'Libro no encontrado']));
}

// Validar nombre de archivo
if (!preg_match('/^[a-zA-Z0-9_\-\.]+\.pdf$/i', $libro['archivo'])) {
    header("HTTP/1.0 400 Bad Request");
    exit(json_encode(['error' => 'Nombre de archivo inválido']));
}

// Construir y validar ruta
$pdfPath = realpath($uploadDir . $libro['archivo']);
if ($pdfPath === false || strpos($pdfPath, $uploadDir) !== 0) {
    header("HTTP/1.0 403 Forbidden");
    exit(json_encode(['error' => 'Ruta de archivo no permitida']));
}

// Verificar archivo
if (!file_exists($pdfPath)) {
    header("HTTP/1.0 404 Not Found");
    exit(json_encode(['error' => 'Archivo no encontrado']));
}

// Verificar tipo MIME
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $pdfPath);
finfo_close($finfo);

if ($mime !== 'application/pdf') {
    header("HTTP/1.0 415 Unsupported Media Type");
    exit(json_encode(['error' => 'El archivo no es un PDF válido']));
}

// Configurar caché
header('Cache-Control: public, max-age=3600');
header('Expires: '.gmdate('D, d M Y H:i:s', time() + 3600).' GMT');

// Enviar PDF
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="'.basename($libro['nombre_original']).'"');
header('Content-Length: ' . filesize($pdfPath));

// Lectura eficiente para archivos grandes
if ($fd = fopen($pdfPath, 'rb')) {
    while (!feof($fd)) {
        echo fread($fd, 1024 * 1024); // 1MB chunks
        ob_flush();
        flush();
    }
    fclose($fd);
} else {
    header("HTTP/1.0 500 Internal Server Error");
    exit(json_encode(['error' => 'Error al leer el archivo PDF']));
}
exit;
?>