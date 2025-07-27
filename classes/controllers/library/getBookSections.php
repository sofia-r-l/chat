<?php
include_once __DIR__ . '/../../../../classes/requestController.php';
include_once __DIR__ . '/../../../../config/db_academic_config.php';

Request::isWrongRequestMethod('GET');

session_start();
header("Content-Type: application/json");

if (!isset($_SESSION["ID_STUDENT"])) {
    http_response_code(401);
    echo json_encode(["status" => "failure", "message" => "User not logged in", "code" => 401]);
    return;
}

$studentId = $_SESSION["ID_STUDENT"];

$page = isset($_GET["page"]) ? (int)$_GET["page"] : 1;
$limit = 6;
$offset = $limit * ($page - 1);

$db = Database::getDatabaseInstace();
$mysqli = $db->getConnection();

try {
    $countQuery = "
        SELECT COUNT(DISTINCT b.BOOK_ID) AS total
        FROM TBL_BOOKS b
        INNER JOIN TBL_SECTIONS s ON s.ID_CLASS = b.ID_CLASS
        INNER JOIN TBL_SECTIONS_X_STUDENTS sx ON sx.ID_SECTION = s.ID_SECTION
        WHERE sx.ID_STUDENT = ? AND b.ACTIVE = TRUE
    ";
    $stmt = $mysqli->prepare($countQuery);
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    $totalResult = $stmt->get_result();
    $totalBooks = $totalResult->fetch_assoc()["total"];
    $totalPages = ceil($totalBooks / $limit);
    $stmt->close();

    $query = "CALL SP_GET_BOOKS_BY_STUDENT(?, ?, ?)";
    $result = $db->callStoredProcedure($query, "iii", [$studentId, $offset, $limit], $mysqli);

    if ($result->num_rows === 0) {
        $mysqli->close();
        echo json_encode(["status" => "success", "data" => [], "totalPages" => $totalPages, "currentPage" => $page]);
        return;
    }

    $books = [];
    while ($row = $result->fetch_assoc()) {
        $row['tags'] = !empty($row['tags']) ? explode(', ', $row['tags']) : [];
        $books[] = $row;
    }

    $mysqli->close();

    echo json_encode([
        "status" => "success",
        "data" => $books,
        "totalPages" => $totalPages,
        "currentPage" => $page
    ]);
} catch (Throwable $err) {
    $mysqli->close();
    http_response_code(500);
    echo json_encode([
        "status" => "failure",
        "message" => "Error fetching books: " . $err->getMessage(),
        "code" => 500
    ]);
}
