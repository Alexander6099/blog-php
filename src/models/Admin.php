<?php
// src/models/Admin.php

class Admin {
    private $conexion;
    
    public function __construct($conexion) {
        $this->conexion = $conexion;
    }
    
    // Obtener estadísticas generales
    public function obtenerEstadisticas() {
        $query = "SELECT 
                    (SELECT COUNT(*) FROM usuarios) as total_usuarios,
                    (SELECT COUNT(*) FROM posts) as total_posts,
                    (SELECT COUNT(*) FROM comentarios) as total_comentarios,
                    (SELECT COUNT(*) FROM categorias) as total_categorias";
        
        $resultado = $this->conexion->query($query);
        return $resultado->fetch_assoc();
    }
    
    // Obtener todos los posts con detalles
    public function obtenerTodosLosPosts() {
        $query = "SELECT p.id, p.titulo, u.nombre as autor, c.nombre as categoria, p.fecha_creacion, 
                         (SELECT COUNT(*) FROM comentarios WHERE post_id = p.id) as total_comentarios
                  FROM posts p
                  JOIN usuarios u ON p.autor_id = u.id
                  LEFT JOIN categorias c ON p.categoria_id = c.id
                  ORDER BY p.fecha_creacion DESC";
        
        $resultado = $this->conexion->query($query);
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }
    
    // Obtener todos los usuarios
    public function obtenerTodosLosUsuarios() {
        $query = "SELECT u.id, u.nombre, u.email, u.fecha_registro,
                         (SELECT COUNT(*) FROM posts WHERE autor_id = u.id) as total_posts
                  FROM usuarios u
                  ORDER BY u.fecha_registro DESC";
        
        $resultado = $this->conexion->query($query);
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }
    
    // Obtener todas las categorías
    public function obtenerTodasLasCategorias() {
        $query = "SELECT c.id, c.nombre, 
                         (SELECT COUNT(*) FROM posts WHERE categoria_id = c.id) as total_posts
                  FROM categorias c
                  ORDER BY c.nombre";
        
        $resultado = $this->conexion->query($query);
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }
    
    // Crear categoría
    public function crearCategoria($nombre) {
        $stmt = $this->conexion->prepare("INSERT INTO categorias (nombre) VALUES (?)");
        $stmt->bind_param("s", $nombre);
        return $stmt->execute() ? ['éxito' => true] : ['éxito' => false, 'error' => $stmt->error];
    }
    
    // Eliminar categoría
    public function eliminarCategoria($id) {
        $stmt = $this->conexion->prepare("DELETE FROM categorias WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    // Eliminar usuario
    public function eliminarUsuario($id) {
        $stmt = $this->conexion->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    // Eliminar post (admin)
    public function eliminarPost($id) {
        $stmt = $this->conexion->prepare("DELETE FROM posts WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>