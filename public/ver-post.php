<?php
session_start();
require_once '../config/database.php';
require_once '../src/controllers/PostController.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: index.php');
    exit;
}

$postController = new PostController($conexion);
$post = $postController->obtenerPorId($id);

if (!$post) {
    echo "Post no encontrado";
    exit;
}

// Verificar si es edición
$error = null;
if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $post['autor_id'] && $_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $error = $postController->actualizar();
}

$puedoEditar = isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $post['autor_id'];
$modoEdicion = $puedoEditar && isset($_GET['editar']);
$categorias = $postController->obtenerCategorias();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($post['titulo']) ?> - Blog</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50">
    <nav class="bg-white shadow-md">
        <div class="max-w-6xl mx-auto px-4 py-4 flex justify-between items-center">
            <a href="index.php" class="text-2xl font-bold text-blue-600">Mi Blog</a>
            <div class="space-x-4">
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <span class="text-gray-700"><?= htmlspecialchars($_SESSION['usuario_nombre']) ?></span>
                    <a href="crear-post.php" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Nuevo Post</a>
                    <a href="../src/controllers/logout.php" class="text-red-600 hover:underline">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="text-blue-600 hover:underline">Iniciar Sesión</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="max-w-3xl mx-auto px-4 py-8">
        <?php if (isset($error['error'])): ?>
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                <?= htmlspecialchars($error['error']) ?>
            </div>
        <?php endif; ?>

        <?php if ($modoEdicion): ?>
            <!-- Modo edición -->
            <form method="POST" class="bg-white rounded-lg shadow-md p-8 space-y-6">
                <input type="hidden" name="accion" value="editar">
                <input type="hidden" name="id" value="<?= $post['id'] ?>">

                <div>
                    <label for="titulo" class="block text-sm font-medium text-gray-700 mb-2">Título</label>
                    <input type="text" name="titulo" id="titulo" value="<?= htmlspecialchars($post['titulo']) ?>" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label for="categoria_id" class="block text-sm font-medium text-gray-700 mb-2">Categoría</label>
                    <select name="categoria_id" id="categoria_id"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Sin categoría</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $post['categoria_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="contenido" class="block text-sm font-medium text-gray-700 mb-2">Contenido</label>
                    <textarea name="contenido" id="contenido" rows="10" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"><?= htmlspecialchars($post['contenido']) ?></textarea>
                </div>

                <div class="flex gap-4">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                        Guardar Cambios
                    </button>
                    <a href="?id=<?= $post['id'] ?>" class="bg-gray-400 text-white px-6 py-2 rounded-lg hover:bg-gray-500">
                        Cancelar
                    </a>
                </div>
            </form>
        <?php else: ?>
            <!-- Modo lectura -->
            <article class="bg-white rounded-lg shadow-md p-8">
                <h1 class="text-4xl font-bold mb-4"><?= htmlspecialchars($post['titulo']) ?></h1>

                <div class="flex justify-between items-center mb-6 text-gray-600 border-b pb-4">
                    <div>
                        <p>Por <strong><?= htmlspecialchars($post['autor']) ?></strong></p>
                        <p><?= date('d/m/Y H:i', strtotime($post['fecha_creacion'])) ?></p>
                        <?php if ($post['categoria']): ?>
                            <p>Categoría: <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm">
                                    <?= htmlspecialchars($post['categoria']) ?>
                                </span></p>
                        <?php endif; ?>
                    </div>

                    <?php if ($puedoEditar): ?>
                        <div class="space-x-2">
                            <a href="?id=<?= $post['id'] ?>&editar=1" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">
                                Editar
                            </a>
                            <a href="eliminar-post.php?id=<?= $post['id'] ?>" onclick="return confirm('¿Eliminar este post?')" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                                Eliminar
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="prose max-w-none">
                    <?= nl2br(htmlspecialchars($post['contenido'])) ?>
                </div>
            </article>
        <?php endif; ?>
    </div>
</body>

</html>