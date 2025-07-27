<?php
class Book {
    private $conn;
    private $table = "tbl_libros";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllBooksExtended() {
        $sql = "
    SELECT 
        b.ID_LIBRO AS id, b.TITULO AS titulo, b.ANIO_PUBLICACION AS anio_publicacion, b.EDITORIAL, b.ISBN,
        a.ID_AUTOR AS autor_id, a.NOMBRE_AUTOR AS autor_nombre,
        c.ID_CARRERA AS carrera_id, c.NOMBRE_CARRERA AS carrera_nombre,
        ca.Codigo_Clase AS codigo_clase
    FROM tbl_libros b
    LEFT JOIN tbl_libro_autor la ON b.ID_LIBRO = la.ID_LIBRO
    LEFT JOIN tbl_autores a ON la.ID_AUTOR = a.ID_AUTOR
    LEFT JOIN tbl_libro_carrera lc ON b.ID_LIBRO = lc.ID_LIBRO
    LEFT JOIN tbl_carreras c ON lc.ID_CARRERA = c.ID_CARRERA
    LEFT JOIN tbl_asignatura asig ON asig.ID_CARRERA = c.ID_CARRERA
    LEFT JOIN codigo_clase ca ON asig.ID_CC = ca.ID_CC
    ORDER BY b.ID_LIBRO;
";


        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Organizar datos agrupando autores y carreras por libro
        $books = [];
        foreach ($rows as $row) {
            $id = $row['id'];
            if (!isset($books[$id])) {
                $books[$id] = [
                    'id' => $id,
                    'titulo' => $row['titulo'],
                    'anio_publicacion' => $row['anio_publicacion'],
                    'editorial' => $row['EDITORIAL'],
                    'isbn' => $row['ISBN'],
                    'autores' => [],
                    'carreras' => [],
                    'codigos_clase' => [],
                ];
            }

            // Agregar autor si no está
            if ($row['autor_id'] && !in_array($row['autor_nombre'], $books[$id]['autores'])) {
                $books[$id]['autores'][] = $row['autor_nombre'];
            }

            // Agregar carrera si no está
            if ($row['carrera_id'] && !in_array($row['carrera_nombre'], $books[$id]['carreras'])) {
                $books[$id]['carreras'][] = $row['carrera_nombre'];
            }

            // Agregar código de clase si no está
            if ($row['codigo_clase'] && !in_array($row['codigo_clase'], $books[$id]['codigos_clase'])) {
                $books[$id]['codigos_clase'][] = $row['codigo_clase'];
            }
        }

        // Reindexar el arreglo para que sea un array numérico simple
        return array_values($books);
    }
}


    /* public function getAllBooks() {
        $sql = "SELECT 
                    ID_LIBRO AS id, 
                    TITULO AS titulo, 
                    ANIO_PUBLICACION AS anio_publicacion, 
                    EDITORIAL, 
                    ISBN 
                FROM " . $this->table;

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } */

