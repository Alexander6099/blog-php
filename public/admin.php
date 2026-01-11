<?php
session_start();
require_once '../config/database.php';
require_once '../src/controllers/AdminController.php';

// Verificar si está logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}
// Validación temporal: solo el usuario con ID 1 puede acceder (el primero registrado)
if ($_SESSION['usuario_id'] !== 1) {
    echo "Acceso denegado. Solo administradores pueden acceder.";
    exit;
}

$adminController = new AdminController($conexion);

// Manejar acciones
$errorCategoria = $adminController->crearCategoria();
$adminController->eliminarCategoria();
$adminController->eliminarPost();

// Obtener datos
$estadisticas = $adminController->obtenerEstadisticas();
$posts = $adminController->obtenerTodosLosPosts();
$usuarios = $adminController->obtenerTodosLosUsuarios();
$categorias = $adminController->obtenerTodasLasCategorias();

$tab = $_GET['tab'] ?? 'dashboard';
$mensaje = $_GET['mensaje'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administrador - Blog</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-gray-900 text-white shadow-lg">
        <div class="max-w-6xl mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold">Admin Panel</h1>
            <div class="space-x-4">
                <span class="text-gray-300"><?= htmlspecialchars($_SESSION['usuario_nombre']) ?></span>
                <a href="index.php" class="text-blue-400 hover:underline">Ir al Blog</a>
                <a href="../src/controllers/logout.php" class="text-red-400 hover:underline">Logout</a>
            </div>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto px-4 py-8">
        <?php if ($mensaje): ?>
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                <?= htmlspecialchars($mensaje) ?>
            </div>
        <?php endif; ?>

        <!-- Tabs -->
        <div class="flex gap-2 mb-6 border-b">
            <a href="?tab=dashboard" class="px-4 py-2 <?= $tab === 'dashboard' ? 'bg-blue-600 text-white' : 'bg-gray-300' ?> rounded-t">
                Dashboard
            </a>
            <a href="?tab=posts" class="px-4 py-2 <?= $tab === 'posts' ? 'bg-blue-600 text-white' : 'bg-gray-300' ?> rounded-t">
                Posts
            </a>
            <a href="?tab=usuarios" class="px-4 py-2 <?= $tab === 'usuarios' ? 'bg-blue-600 text-white' : 'bg-gray-300' ?> rounded-t">
                Usuarios
            </a>
            <a href="?tab=categorias" class="px-4 py-2 <?= $tab === 'categorias' ? 'bg-blue-600 text-white' : 'bg-gray-300' ?> rounded-t">
                Categorías
            </a>
        </div>

        <!-- Dashboard -->
        <?php if ($tab === 'dashboard'): ?>
            <div class="grid grid-cols-4 gap-4 mb-8">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-gray-600 text-sm font-medium">Total Usuarios</h3>
                    <p class="text-3xl font-bold text-blue-600"><?= $estadisticas['total_usuarios'] ?></p>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-gray-600 text-sm font-medium">Total Posts</h3>
                    <p class="text-3xl font-bold text-green-600"><?= $estadisticas['total_posts'] ?></p>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-gray-600 text-sm font-medium">Total Comentarios</h3>
                    <p class="text-3xl font-bold text-purple-600"><?= $estadisticas['total_comentarios'] ?></p>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-gray-600 text-sm font-medium">Total Categorías</h3>
                    <p class="text-3xl font-bold text-orange-600"><?= $estadisticas['total_categorias'] ?></p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Posts -->
        <?php if ($tab === 'posts'): ?>
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-2xl font-bold mb-6">Gestión de Posts</h2>
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="border p-3 text-left">Título</th>
                            <th class="border p-3 text-left">Autor</th>
                            <th class="border p-3 text-left">Categoría</th>
                            <th class="border p-3 text-left">Comentarios</th>
                            <th class="border p-3 text-left">Fecha</th>
                            <th class="border p-3 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($posts as $post): ?>
                            <tr class="border-b hover:bg-gray-50">
                                <td class="border p-3"><?= htmlspecialchars(substr($post['titulo'], 0, 30)) ?></td>
                                <td class="border p-3"><?= htmlspecialchars($post['autor']) ?></td>
                                <td class="border p-3"><?= htmlspecialchars($post['categoria'] ?? 'Sin categoría') ?></td>
                                <td class="border p-3"><?= $post['total_comentarios'] ?></td>
                                <td class="border p-3"><?= date('d/m/Y', strtotime($post['fecha_creacion'])) ?></td>
                                <td class="border p-3 text-center space-x-2">
                                    <a href="ver-post.php?id=<?= $post['id'] ?>" class="text-blue-600 hover:underline text-sm">Ver</a>
                                    <a href="?tab=posts&eliminar_post=<?= $post['id'] ?>" onclick="return confirm('¿Eliminar?')" class="text-red-600 hover:underline text-sm">Eliminar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <!-- Usuarios -->
        <?php if ($tab === 'usuarios'): ?>
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-2xl font-bold mb-6">Gestión de Usuarios</h2>
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="border p-3 text-left">Nombre</th>
                            <th class="border p-3 text-left">Email</th>
                            <th class="border p-3 text-left">Posts</th>
                            <th class="border p-3 text-left">Registrado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $usuario): ?>
                            <tr class="border-b hover:bg-gray-50">
                                <td class="border p-3"><?= htmlspecialchars($usuario['nombre']) ?></td>
                                <td class="border p-3"><?= htmlspecialchars($usuario['email']) ?></td>
                                <td class="border p-3"><?= $usuario['total_posts'] ?></td>
                                <td class="border p-3"><?= date('d/m/Y', strtotime($usuario['fecha_registro'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <!-- Categorías -->
        <?php if ($tab === 'categorias'): ?>
            <div class="grid grid-cols-2 gap-6">
                <!-- Crear categoría -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-2xl font-bold mb-6">Crear Categoría</h2>
                    
                    <?php if (isset($errorCategoria['error'])): ?>
                        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                            <?= htmlspecialchars($errorCategoria['error']) ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" class="space-y-4">
                        <input type="hidden" name="accion" value="crear_categoria">
                        
                        <div>
                            <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">Nombre de la categoría</label>
                            <input type="text" name="nombre" id="nombre" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                            Crear Categoría
                        </button>
                    </form>
                </div>

                <!-- Listar categorías -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-2xl font-bold mb-6">Categorías Existentes</h2>
                    
                    <div class="space-y-2">
                        <?php if (empty($categorias)): ?>
                            <p class="text-gray-600">No hay categorías</p>
                        <?php else: ?>
                            <?php foreach ($categorias as $categoria): ?>
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded border">
                                    <div>
                                        <p class="font-medium"><?= htmlspecialchars($categoria['nombre']) ?></p>
                                        <p class="text-sm text-gray-600"><?= $categoria['total_posts'] ?> posts</p>
                                    </div>
                                    <a href="?tab=categorias&eliminar_categoria=<?= $categoria['id'] ?>" onclick="return confirm('¿Eliminar?')" class="text-red-600 hover:text-red-800 text-sm">
                                        Eliminar
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>