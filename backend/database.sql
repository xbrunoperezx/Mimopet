-- Crear base de datos si no existe
CREATE DATABASE IF NOT EXISTS mimopet;
USE mimopet;

-- Tabla de usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'empleado') NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    activo BOOLEAN DEFAULT TRUE
);

-- Tabla de servicios
CREATE TABLE IF NOT EXISTS servicios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio_base DECIMAL(10,2) NOT NULL,
    duracion_minutos INT NOT NULL,
    activo BOOLEAN DEFAULT TRUE
);

-- Tabla de clientes
CREATE TABLE IF NOT EXISTS clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    telefono VARCHAR(20),
    direccion TEXT,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de mascotas
CREATE TABLE IF NOT EXISTS mascotas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    nombre VARCHAR(50) NOT NULL,
    especie VARCHAR(50) NOT NULL,
    raza VARCHAR(50),
    edad INT,
    peso DECIMAL(5,2),
    notas TEXT,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id)
);

-- Tabla de reservas
CREATE TABLE IF NOT EXISTS reservas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    mascota_id INT NOT NULL,
    servicio_id INT NOT NULL,
    empleado_id INT,
    fecha_inicio DATETIME NOT NULL,
    fecha_fin DATETIME NOT NULL,
    estado ENUM('pendiente', 'confirmada', 'completada', 'cancelada') DEFAULT 'pendiente',
    precio_total DECIMAL(10,2) NOT NULL,
    notas TEXT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id),
    FOREIGN KEY (mascota_id) REFERENCES mascotas(id),
    FOREIGN KEY (servicio_id) REFERENCES servicios(id),
    FOREIGN KEY (empleado_id) REFERENCES usuarios(id)
);

-- Insertar servicios básicos
INSERT INTO servicios (nombre, descripcion, precio_base, duracion_minutos) VALUES
('Peluquería', 'Servicio completo de peluquería para mascotas', 25000, 60),
('Cuidado a domicilio', 'Servicio de cuidado de mascotas a domicilio', 30000, 60),
('Guardería', 'Servicio de guardería para mascotas', 35000, 60);

-- Insertar usuario administrador por defecto
-- Password: admin123 (hasheado con bcrypt)
INSERT INTO usuarios (nombre, email, password, rol) VALUES
('Administrador', 'admin@mimopet.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'); 