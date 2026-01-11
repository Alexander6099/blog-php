<?php
// src/models/Comentario.php

class Comentario {
    private $conexion;
    
    public function __construct($conexion) {
        $this->conexion = $conexion;
    }
    
    public function crear($contenido, $usuario_id, $post_id) {
        $stmt = $this->conexion->prepare("INSERT INTO comentarios (contenido, usuario_id, post_id) VALUES (?, ?, ?)");
        $stmt->bind_param("sii", $contenido, $usuario_id, $post_id);
        
        return $stmt->execute() ? ['éxito' => true] : ['éxito' => false, 'error' => $stmt->error];
    }
    
    public function obtenerPorPost($post_id) {
        $stmt = $this->conexion->prepare("SELECT c.id, c.contenido, c.fecha_comentario, u.nombre as usuario, u.id as usuario_id 
                                          FROM comentarios c 
                                          JOIN usuarios u ON c.usuario_id = u.id 
                                          WHERE c.post_id = ? 
                                          ORDER BY c.fecha_comentario DESC");
        $stmt->bind_param("i", $post_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    public function eliminar($id, $usuario_id) {
        $stmt = $this->conexion->prepare("DELETE FROM comentarios WHERE id = ? AND usuario_id = ?");
        $stmt->bind_param("ii", $id, $usuario_id);
        return $stmt->execute();
    }
}
?>