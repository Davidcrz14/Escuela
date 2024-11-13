-- Crear la base de datos
CREATE DATABASE universidad_panel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE universidad_panel;

-- Tabla de Alumnos
CREATE TABLE alumnos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    correo VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    matricula VARCHAR(20) NOT NULL UNIQUE,
    carrera VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de Noticias de Rectoría
CREATE TABLE rectoria_noticias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    informacion TEXT NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    imagen_principal VARCHAR(255) NOT NULL,
    imagenes TEXT,
    documentos TEXT,
    prioridad TINYINT(1) DEFAULT 0,
    datos_extra TEXT,
    creado_por VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de Administradores
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    correo VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    pertenece ENUM('Rectoria', 'Instituto') NOT NULL,
    cv TEXT,
    descripcion TEXT,
    instituto VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

