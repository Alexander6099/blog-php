<?php
// src/models/Usuario.php

class Usuario {
    private $conexion;
    
    public function __construct($conexion) {
        $this->conexion = $conexion;
    }
    
    // Registrar usuario
    public function registrar($nombre, $email, $password) {
        // Verificar si email ya existe
        $stmt = $this->conexion->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if ($resultado->num_rows > 0) {
            return ['éxito' => false, 'mensaje' => 'El email ya está registrado'];
        }
        
        // Hashear contraseña
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        
        // Insertar usuario
        $stmt = $this->conexion->prepare("INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nombre, $email, $password_hash);
        
        if ($stmt->execute()) {
            return ['éxito' => true, 'mensaje' => 'Usuario registrado correctamente'];
        } else {
            return ['éxito' => false, 'mensaje' => 'Error al registrar'];
        }
    }
    
    // Iniciar sesión
    public function login($email, $password) {
        $stmt = $this->conexion->prepare("SELECT id, nombre, email, password FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if ($resultado->num_rows === 0) {
            return ['éxito' => false, 'mensaje' => 'Email no encontrado'];
        }
        
        $usuario = $resultado->fetch_assoc();
        
        if (!password_verify($password, $usuario['password'])) {
            return ['éxito' => false, 'mensaje' => 'Contraseña incorrecta'];
        }
        
        return ['éxito' => true, 'usuario' => $usuario];
    }
    
    // Obtener usuario por ID
    public function obtenerPorId($id) {
        $stmt = $this->conexion->prepare("SELECT id, nombre, email FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
?>