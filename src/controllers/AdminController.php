<?php
// src/controllers/AdminController.php

require_once '../src/models/Admin.php';

class AdminController {
    private $adminModel;
    
    public function __construct($conexion) {
        $this->adminModel = new Admin($conexion);
    }
    
    // Verificar si es admin (por ahora solo si está logueado)
    public function esAdmin() {
        return isset($_SESSION['usuario_id']);
    }
    
    // Crear categoría
    public function crearCategoria() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'crear_categoria') {
            $nombre = trim($_POST['nombre'] ?? '');
            
            if (empty($nombre)) {
                return ['error' => 'El nombre de la categoría es requerido'];
            }
            
            $resultado = $this->adminModel->crearCategoria($nombre);
            
            if ($resultado['éxito']) {
                header('Location: admin.php?tab=categorias&mensaje=Categoría creada');
                exit;
            } else {
                return ['error' => $resultado['error'] ?? 'Error al crear categoría'];
            }
        }
        return null;
    }
    
    // Eliminar categoría
    public function eliminarCategoria() {
        $id = $_GET['eliminar_categoria'] ?? null;
        
        if (!$id) {
            return null;
        }
        
        if ($this->adminModel->eliminarCategoria($id)) {
            header('Location: admin.php?tab=categorias&mensaje=Categoría eliminada');
            exit;
        }
        return ['error' => 'Error al eliminar categoría'];
    }
    
    // Obtener estadísticas
    public function obtenerEstadisticas() {
        return $this->adminModel->obtenerEstadisticas();
    }
    
    // Obtener todos los posts
    public function obtenerTodosLosPosts() {
        return $this->adminModel->obtenerTodosLosPosts();
    }
    
    // Obtener todos los usuarios
    public function obtenerTodosLosUsuarios() {
        return $this->adminModel->obtenerTodosLosUsuarios();
    }
    
    // Obtener todas las categorías
    public function obtenerTodasLasCategorias() {
        return $this->adminModel->obtenerTodasLasCategorias();
    }
    
    // Eliminar post (admin)
    public function eliminarPost() {
        $id = $_GET['eliminar_post'] ?? null;
        
        if (!$id) {
            return null;
        }
        
        if ($this->adminModel->eliminarPost($id)) {
            header('Location: admin.php?tab=posts&mensaje=Post eliminado');
            exit;
        }
        return ['error' => 'Error al eliminar post'];
    }
}
?>