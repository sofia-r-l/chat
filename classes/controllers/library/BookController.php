<?php
require_once __DIR__ . '/../../services/library/listBooks.php';

class BookController {
    private $bookService;

    public function __construct($bookService) {
        $this->bookService = $bookService;
    }

    public function listBooksExtended() {
        try {
            $books = $this->bookService->getAllBooksExtended();
            echo json_encode(['success' => true, 'libros' => $books], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }



   /*  public function listBooks() {
        $books = $this->service->getAllBooks();
        echo json_encode([
            'success' => true,
            'libros' => $books
        ], JSON_UNESCAPED_UNICODE);
    } */
}
