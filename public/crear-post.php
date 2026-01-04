<?php
session_start();
require_once '../config/database.php';
require_once '../src/controllers/PostController.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$postController = new PostController($conexion);
$error = $postController->crear();
$categorias = $postController->obtenerCategorias();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Post - Blog</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <nav class="bg-white shadow-md">
        <div class="max-w-6xl mx-auto px-4 py-4 flex justify-between items-center">
            <a href="index.php" class="text-2xl font-bold text-blue-600">Mi Blog</a>
            <div class="space-x-4">
                <span class="text-gray-600 font-bold"><?= htmlspecialchars($_SESSION['usuario_nombre']) ?></span>
                <a href="../src/controllers/logout.php" class="text-red-600 hover:underline">Logout</a>
            </div>
        </div>
    </nav>
    
    <div class="max-w-2xl mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6">Crear Nuevo Post</h1>
        
        <?php if (isset($error['error'])): ?>
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                <?= htmlspecialchars($error['error']) ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" class="bg-white rounded-lg shadow-md p-8 space-y-6">
            <div>
                <label for="titulo" class="block text-sm font-medium text-gray-700 mb-2">Título</label>
                <input type="text" name="titulo" id="titulo" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            
            <div>
                <label for="categoria_id" class="block text-sm font-medium text-gray-700 mb-2">Categoría</label>
                <select name="categoria_id" id="categoria_id"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Sin categoría</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label for="contenido" class="block text-sm font-medium text-gray-700 mb-2">Contenido</label>
                <textarea name="contenido" id="contenido" rows="10" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
            </div>
            
            <div class="flex gap-4">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                    Publicar
                </button>
                <a href="index.php" class="bg-gray-400 text-white px-6 py-2 rounded-lg hover:bg-gray-500 transition">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</body>
</html>