<?php
class ChatService {
    private $usuario;
    private $mensaje;

    public function __construct($usuario, $mensaje) {
        $this->usuario = $usuario;
        $this->mensaje = $mensaje;
    }

public function obtenerContactosClasificados($userId) {
    try {
        $todos = $this->usuario->obtenerContactos($userId);
        
        if (!is_array($todos)) {
            throw new Exception('Error al obtener contactos');
        }
        
        return [
            'online' => array_values(array_filter($todos, fn($c) => $c['estado'] === 'online')),
            'offline' => array_values(array_filter($todos, fn($c) => $c['estado'] === 'offline'))
        ];
    } catch (Exception $e) {
        error_log("Error en obtenerContactosClasificados: " . $e->getMessage());
        return ['online' => [], 'offline' => []];
    }
}

    public function obtenerMensajesChat($emisorId, $receptorId) {
        return $this->mensaje->obtenerMensajes($emisorId, $receptorId);
    }

    public function enviarMensajeChat($emisorId, $receptorId, $mensaje) {
        return $this->mensaje->insertarMensaje($emisorId, $receptorId, $mensaje);
    }
}