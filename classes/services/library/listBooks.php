<?php
require_once __DIR__ . '/../../../config/conexion.php';
require_once __DIR__ . '/../../models/library/Book.php';

class BookService {
    private $bookModel;

    public function __construct($bookModel) {
        $this->bookModel = $bookModel;
    }

    public function getAllBooksExtended() {
        return $this->bookModel->getAllBooksExtended();
    }
}



