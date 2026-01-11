<?php
session_start();
require_once '../config/database.php';
require_once '../src/controllers/ComentarioController.php';

$id = $_GET['id'] ?? null;
$post_id = $_GET['post_id'] ?? null;

if (!$id || !$post_id || !isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

$comentarioController = new ComentarioController($conexion);
$resultado = $comentarioController->eliminar($id);

header('Location: ver-post.php?id=' . $post_id);
exit;
?>