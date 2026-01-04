<?php
// src/models/Post.php

class Post {
    private $conexion;
    
    public function __construct($conexion) {
        $this->conexion = $conexion;
    }
    
    // Crear post
    public function crear($titulo, $contenido, $autor_id, $categoria_id = null) {
        //si categoria_id es null, asignar NULL en la base de datos
        $categoria_id = empty($categoria_id) ? null : (int)$categoria_id;
        //////////
        $stmt = $this->conexion->prepare("INSERT INTO posts (titulo, contenido, autor_id, categoria_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssii", $titulo, $contenido, $autor_id, $categoria_id);
        
        if ($stmt->execute()) {
            return ['éxito' => true, 'id' => $this->conexion->insert_id];
        } else {
            return ['éxito' => false, 'mensaje' => 'Error al crear post'];
        }
    }
    
    // Obtener todos los posts
    public function obtenerTodos() {
        $query = "SELECT p.id, p.titulo, p.contenido, p.fecha_creacion, u.nombre as autor, c.nombre as categoria 
                  FROM posts p 
                  JOIN usuarios u ON p.autor_id = u.id 
                  LEFT JOIN categorias c ON p.categoria_id = c.id 
                  ORDER BY p.fecha_creacion DESC";
        
        $resultado = $this->conexion->query($query);
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }
    
    // Obtener post por ID
    public function obtenerPorId($id) {
        $stmt = $this->conexion->prepare("SELECT p.id, p.titulo, p.contenido, p.fecha_creacion, p.fecha_actualizacion, u.nombre as autor, u.id as autor_id, c.nombre as categoria, c.id as categoria_id 
                                          FROM posts p 
                                          JOIN usuarios u ON p.autor_id = u.id 
                                          LEFT JOIN categorias c ON p.categoria_id = c.id 
                                          WHERE p.id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    // Actualizar post
    public function actualizar($id, $titulo, $contenido, $categoria_id = null) {
        $stmt = $this->conexion->prepare("UPDATE posts SET titulo = ?, contenido = ?, categoria_id = ? WHERE id = ?");
        $stmt->bind_param("ssii", $titulo, $contenido, $categoria_id, $id);
        
        return $stmt->execute() ? ['éxito' => true] : ['éxito' => false];
    }
    
    // Eliminar post
    public function eliminar($id) {
        $stmt = $this->conexion->prepare("DELETE FROM posts WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    // Buscar posts
    public function buscar($termino) {
        $termino = "%$termino%";
        $stmt = $this->conexion->prepare("SELECT p.id, p.titulo, p.contenido, p.fecha_creacion, u.nombre as autor, c.nombre as categoria 
                                          FROM posts p 
                                          JOIN usuarios u ON p.autor_id = u.id 
                                          LEFT JOIN categorias c ON p.categoria_id = c.id 
                                          WHERE p.titulo LIKE ? OR p.contenido LIKE ? 
                                          ORDER BY p.fecha_creacion DESC");
        $stmt->bind_param("ss", $termino, $termino);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>