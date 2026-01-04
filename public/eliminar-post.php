<?php
session_start();
require_once '../config/database.php';
require_once '../src/controllers/PostController.php';

$id = $_GET['id'] ?? null;

if (!$id || !isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

$postController = new PostController($conexion);
$error = $postController->eliminar($id);

if (isset($error['error'])) {
    echo $error['error'];
}
?>