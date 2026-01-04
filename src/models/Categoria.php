<?php
// src/models/Categoria.php

class Categoria {
    private $conexion;
    
    public function __construct($conexion) {
        $this->conexion = $conexion;
    }
    
    public function obtenerTodas() {
        $resultado = $this->conexion->query("SELECT id, nombre FROM categorias ORDER BY nombre");
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }
    
    public function crear($nombre) {
        $stmt = $this->conexion->prepare("INSERT INTO categorias (nombre) VALUES (?)");
        $stmt->bind_param("s", $nombre);
        return $stmt->execute();
    }
}
?>