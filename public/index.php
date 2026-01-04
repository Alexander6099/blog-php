<?php
session_start();
require_once '../config/database.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog - Inicio</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <nav class="bg-white shadow-md">
        <div class="max-w-6xl mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-blue-600">Mi Blog</h1>
            <div class="space-x-4">
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <span class="text-gray-700">Hola, <?= htmlspecialchars($_SESSION['usuario_nombre']) ?></span>
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
        <h2 class="text-3xl font-bold mb-6">Últimos Posts</h2>
        <p class="text-gray-600">Los posts aparecerán aquí próximamente...</p>
    </div>
</body>
</html>