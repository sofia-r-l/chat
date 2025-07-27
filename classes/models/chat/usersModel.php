<?php
class Usuario {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

public function obtenerContactos($idUsuario) {
    try {
        $query = "SELECT u.id, u.nombre, u.email, u.estado, u.ultima_conexion,
                  (SELECT COUNT(*) FROM mensajes m 
                   WHERE m.receptor_id = u.id AND m.emisor_id = ? AND m.leido = 0) AS mensajes_no_leidos
                  FROM contactos c
                  JOIN usuarios u ON u.id = c.contacto_id
                  WHERE c.usuario_id = ? AND c.estado = 'aceptado'
                  ORDER BY u.estado DESC, u.nombre ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$idUsuario, $idUsuario]);
        
        $contactos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Calcular tiempo desde última conexión para offline
        foreach ($contactos as &$contacto) {
            if ($contacto['estado'] === 'offline' && $contacto['ultima_conexion']) {
                $ultimaConexion = new DateTime($contacto['ultima_conexion']);
                $ahora = new DateTime();
                $diferencia = $ahora->diff($ultimaConexion);
                
                if ($diferencia->d > 0) {
                    $contacto['ultima_conexion'] = "Hace {$diferencia->d} días";
                } elseif ($diferencia->h > 0) {
                    $contacto['ultima_conexion'] = "Hace {$diferencia->h} horas";
                } else {
                    $contacto['ultima_conexion'] = "Hace {$diferencia->i} minutos";
                }
            }
        }
        
        return $contactos;
        
    } catch (PDOException $e) {
        error_log("Error en obtenerContactos: " . $e->getMessage());
        return [];
    }
}
}