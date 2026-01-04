<?php
// src/controllers/PostController.php

require_once '../src/models/Post.php';
require_once '../src/models/Categoria.php';

class PostController
{
    private $postModel;
    private $categoriaModel;

    public function __construct($conexion)
    {
        $this->postModel = new Post($conexion);
        $this->categoriaModel = new Categoria($conexion);
    }

    public function crear()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titulo = trim($_POST['titulo'] ?? '');
            $contenido = trim($_POST['contenido'] ?? '');
            $categoria_id = $_POST['categoria_id'] ?? null;

            if (empty($titulo) || empty($contenido)) {
                return ['error' => 'Título y contenido son requeridos'];
            }

            if (!isset($_SESSION['usuario_id'])) {
                return ['error' => 'Debes iniciar sesión'];
            }
            ////////////////
            $resultado = $this->postModel->crear($titulo, $contenido, $_SESSION['usuario_id'], $categoria_id);

            // DEBUG - Mostrar qué pasó

            if ($resultado['éxito']) {
                header('Location: ver-post.php?id=' . $resultado['id']);
                exit;
            } else {
                return ['error' => $resultado['mensaje']];
            }
            ///////////////
        }
        return null;
    }

    public function obtenerTodos()
    {
        return $this->postModel->obtenerTodos();
    }

    public function obtenerPorId($id)
    {
        return $this->postModel->obtenerPorId($id);
    }

    public function actualizar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'editar') {
            $id = $_POST['id'] ?? null;
            $titulo = trim($_POST['titulo'] ?? '');
            $contenido = trim($_POST['contenido'] ?? '');
            $categoria_id = $_POST['categoria_id'] ?? null;

            if (empty($titulo) || empty($contenido) || !$id) {
                return ['error' => 'Datos incompletos'];
            }

            $post = $this->postModel->obtenerPorId($id);

            if (!$post || $post['autor_id'] != $_SESSION['usuario_id']) {
                return ['error' => 'No tienes permiso para editar este post'];
            }

            $categoria_id = empty($categoria_id) ? null : (int)$categoria_id;
            $resultado = $this->postModel->actualizar($id, $titulo, $contenido, $categoria_id);

            if ($resultado['éxito']) {
                header('Location: ver-post.php?id=' . $id);
                exit;
            } else {
                return ['error' => 'Error al actualizar'];
            }
        }
        return null;
    }

    public function eliminar($id)
    {
        $post = $this->postModel->obtenerPorId($id);

        if (!$post || $post['autor_id'] != $_SESSION['usuario_id']) {
            return ['error' => 'No tienes permiso'];
        }

        if ($this->postModel->eliminar($id)) {
            header('Location: index.php');
            exit;
        }
        return ['error' => 'Error al eliminar'];
    }

    public function obtenerCategorias()
    {
        return $this->categoriaModel->obtenerTodas();
    }
    public function buscar($termino)
    {
        return $this->postModel->buscar($termino);
    }
}
