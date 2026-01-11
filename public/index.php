<?php
session_start();
require_once '../config/database.php';
require_once '../src/controllers/PostController.php';

$postController = new PostController($conexion);
$posts = $postController->obtenerTodos();
$termino = $_GET['buscar'] ?? '';

if ($termino) {
    $posts = $postController->buscar($termino);
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog - Inicio</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-tr from-sky-200 via-blue-400 to-indigo-900 min-h-screen">
    <nav class="bg-white shadow-md">
        <div class="max-w-6xl mx-auto px-4 py-4 flex justify-between items-center">
            <a href="index.php" class="text-2xl font-bold text-blue-600">Mi Blog</a>
            <div class="space-x-4">
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <span class="text-gray-700">Hola, <?= htmlspecialchars($_SESSION['usuario_nombre']) ?></span>
                    <a href="admin.php" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">Admin</a>
                    <a href="crear-post.php" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Nuevo Post</a>
                    <a href="../src/controllers/logout.php" class="text-red-600 hover:underline">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="text-blue-600 hover:underline">Iniciar Sesión</a>
                    <a href="registro.php" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Registrarse</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto px-4 py-8">
        <h1 class="text-4xl font-bold mb-8">Últimos Posts</h1>

        <!-- Búsqueda -->
        <form method="GET" class="mb-8">
            <div class="flex gap-2">
                <input type="text" name="buscar" placeholder="Buscar posts..." value="<?= htmlspecialchars($termino) ?>"
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    Buscar
                </button>
                <?php if ($termino): ?>
                    <a href="index.php" class="bg-gray-400 text-white px-6 py-2 rounded-lg hover:bg-gray-500">
                        Limpiar
                    </a>
                <?php endif; ?>
            </div>
        </form>

        <!-- Posts -->
        <?php if (empty($posts)): ?>
            <p class="text-gray-600 text-center py-8">No hay posts disponibles</p>
        <?php else: ?>
            <div class="grid gap-6">
                <?php foreach ($posts as $post): ?>
                    <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
                        <h2 class="text-2xl font-bold mb-2">
                            <a href="ver-post.php?id=<?= $post['id'] ?>" class="text-blue-600 hover:underline">
                                <?= htmlspecialchars($post['titulo']) ?>
                            </a>
                        </h2>
                        <p class="text-gray-600 mb-3">Por <?= htmlspecialchars($post['autor']) ?> • <?= date('d/m/Y', strtotime($post['fecha_creacion'])) ?></p>
                        <?php if ($post['categoria']): ?>
                            <p class="mb-3">
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm">
                                    <?= htmlspecialchars($post['categoria']) ?>
                                </span>
                            </p>
                        <?php endif; ?>
                        <p class="text-gray-700 mb-4">
                            <?= substr(htmlspecialchars($post['contenido']), 0, 200) ?>...
                        </p>
                        <a href="ver-post.php?id=<?= $post['id'] ?>" class="text-blue-600 hover:underline font-medium">
                            Leer más →
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>