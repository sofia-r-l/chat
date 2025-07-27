# Endpoints  Biblioteca (library)

## 1. Listar Libros

- **Ruta:** `/public/api/library/listBooks.php`
- **Método:** GET
- **Descripción:** Devuelve la lista de todos los libros registrados.

### Ejemplo de Request

```h
curl -X GET "http://localhost/proyecto-unah-main/public/api/library/listBooks.php"
```

### Ejemplo de Response

```json
[
  {
    "id": 1,
    "titulo": "Introducción a la Física",
    "autor": "Isaac Newton",
    "tipo": "Libro",
    "etiquetas": ["Ciencia", "Física"],
    "archivo": "uploads/1_fisica.pdf"
  },
  ...
]
```

---

## 2. Obtener Detalle de un Libro

- **Ruta:** `/public/api/library/get/book/allInfo.php`
- **Método:** GET
- **Parámetros:** `id` (ID del libro)
- **Descripción:** Devuelve toda la información de un libro específico.

### Ejemplo de Request

```h
curl -X GET "http://localhost/proyecto-unah-main/public/api/library/get/book/allInfo.php?id=1"
```

### Ejemplo de Response

```json
{
  "id": 1,
  "titulo": "Introducción a la Física",
  "autor": "Isaac Newton",
  "tipo": "Libro",
  "etiquetas": ["Ciencia", "Física"],
  "archivo": "uploads/1_fisica.pdf",
}
```

---

## 3. Ver Recurso de Libro

- **Ruta:** `/public/api/library/get/book/viewResource.php`
- **Método:** GET
- **Parámetros:** `id` (ID del recurso)
- **Descripción:** Devuelve el archivo asociado a un libro.

### Ejemplo de Request

```h
curl -X GET "http://localhost/proyecto-unah-main/public/api/library/get/book/viewResource.php?id=1" -o libro.pdf
```

### Ejemplo de Response

- Devuelve el archivo PDF o recurso solicitado.

---

## 4. Filtrar Recursos por Tipo

- **Ruta:** `/public/api/library/get/book/byType.php`
- **Método:** GET
- **Parámetros:** `type` (tipo de recurso)
- **Descripción:** Devuelve recursos filtrados por tipo.

### Ejemplo de Request

```h
curl -X GET "http://localhost/proyecto-unah-main/public/api/library/get/book/byType.php?type=Libro"
```

### Ejemplo de Response

```json
[
  {
    "id": 1,
    "titulo": "Introducción a la Física",
    "autor": "Isaac Newton",
    "tipo": "Libro"
  }
]
```

---

## 5. Crear Nuevo Libro

- **Ruta:** `/public/api/library/post/book/saveBook.php`
- **Método:** POST
- **Descripción:** Registra un nuevo libro.

### Ejemplo de Request

```h
curl -X POST "http://localhost/proyecto-unah-main/public/api/library/post/book/saveBook.php" \
     -H "Content-Type: application/json" \
     -d '{
           "titulo": "Nuevo Libro",
           "autor": "Autor Desconocido",
           "tipo": "Libro",
           "etiquetas": ["Educación"],
           "archivo": "base64pdf..."
         }'
```

### Ejemplo de Response

```json
{
  "success": true,
  "message": "Libro registrado correctamente"
}
```

---

## 6. Editar Libro

- **Ruta:** `/public/api/library/put/editBook.php`
- **Método:** PUT
- **Descripción:** Edita la información de un libro existente.

### Ejemplo de Request

```h
curl -X PUT "http://localhost/proyecto-unah-main/public/api/library/put/editBook.php" \
     -H "Content-Type: application/json" \
     -d '{
           "id": 1,
           "titulo": "Física Moderna",
           "autor": "Isaac Newton"
         }'
```

### Ejemplo de Response

```json
{
  "success": true,
  "message": "Libro actualizado correctamente"
}
```

---

## 7. Eliminar Recurso de Libro

- **Ruta:** `/public/api/library/delete/book/resourceById.php`
- **Método:** DELETE
- **Parámetros:** `id` (ID del recurso)
- **Descripción:** Elimina un recurso de la biblioteca.

### Ejemplo de Request

```h
curl -X DELETE "http://localhost/proyecto-unah-main/public/api/library/delete/book/resourceById.php?id=1"```

### Ejemplo de Response

```json
{
  "success": true,
  "message": "Recurso eliminado correctamente"
}
```

---

## 8. Etiquetas y Autores

- **Obtener todas las etiquetas:**  
  - `  curl -X GET "http://localhost/proyecto-unah-main/public/api/library/get/tag/allTags.php"` (GET)
- **Obtener etiquetas por recurso:**  
  - `  curl -X GET "http://localhost/proyecto-unah-main/public/api/library/get/tag/tagByIdResource.php?id=1"` (GET)
- **Crear etiqueta:**  
  - `   curl -X POST "http://localhost/proyecto-unah-main/public/api/library/post/tag/ createTag.  - php" \ -H "Content-Type: application/json" \  -d '{"nombre": "Nueva Etiqueta"}' ` (POST)
- **Obtener autores por recurso:**  
  - `  curl -X GET "http://localhost/proyecto-unah-main/public/api/library/get/author/authorsByIdResource.php?id=1"` (GET)

---