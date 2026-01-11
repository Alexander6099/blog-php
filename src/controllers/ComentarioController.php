<?php
// src/controllers/ComentarioController.php

require_once '../src/models/Comentario.php';

class ComentarioController {
    private $comentarioModel;
    
    public function __construct($conexion) {
        $this->comentarioModel = new Comentario($conexion);
    }
    
    public function crear() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'comentar') {
            $contenido = trim($_POST['contenido'] ?? '');
            $post_id = $_POST['post_id'] ?? null;
            
            if (empty($contenido) || !$post_id) {
                return ['error' => 'Contenido y post requeridos'];
            }
            
            if (!isset($_SESSION['usuario_id'])) {
                return ['error' => 'Debes iniciar sesión para comentar'];
            }
            
            if (strlen($contenido) < 3) {
                return ['error' => 'El comentario debe tener al menos 3 caracteres'];
            }
            
            $resultado = $this->comentarioModel->crear($contenido, $_SESSION['usuario_id'], $post_id);
            
            return $resultado;
        }
        return null;
    }
    
    public function obtenerPorPost($post_id) {
        return $this->comentarioModel->obtenerPorPost($post_id);
    }
    
    public function eliminar($id) {
        if (!isset($_SESSION['usuario_id'])) {
            return ['error' => 'No autorizado'];
        }
        
        return $this->comentarioModel->eliminar($id, $_SESSION['usuario_id']) 
            ? ['éxito' => true] 
            : ['éxito' => false, 'error' => 'Error al eliminar'];
    }
}
?>