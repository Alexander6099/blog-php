<?php
// src/controllers/AuthController.php

require_once '../src/models/Usuario.php';

class AuthController {
    private $usuarioModel;
    
    public function __construct($conexion) {
        $this->usuarioModel = new Usuario($conexion);
    }
    
    // Manejar registro
    public function registro() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $password_confirm = $_POST['password_confirm'] ?? '';
            
            // Validaciones
            if (empty($nombre) || empty($email) || empty($password)) {
                return ['error' => 'Todos los campos son requeridos'];
            }
            
            if ($password !== $password_confirm) {
                return ['error' => 'Las contraseñas no coinciden'];
            }
            
            if (strlen($password) < 6) {
                return ['error' => 'La contraseña debe tener al menos 6 caracteres'];
            }
            
            $resultado = $this->usuarioModel->registrar($nombre, $email, $password);
            
            if ($resultado['éxito']) {
                header('Location: ../public/login.php?mensaje=Registro exitoso');
                exit;
            } else {
                return ['error' => $resultado['mensaje']];
            }
        }
        return null;
    }
    
    // Manejar login
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            
            if (empty($email) || empty($password)) {
                return ['error' => 'Email y contraseña requeridos'];
            }
            
            $resultado = $this->usuarioModel->login($email, $password);
            
            if ($resultado['éxito']) {
                $_SESSION['usuario_id'] = $resultado['usuario']['id'];
                $_SESSION['usuario_nombre'] = $resultado['usuario']['nombre'];
                header('Location: ../public/index.php');
                exit;
            } else {
                return ['error' => $resultado['mensaje']];
            }
        }
        return null;
    }
    
    // Logout
    public function logout() {
        session_destroy();
        header('Location: ../index.php');
        exit;
    }
}
?>