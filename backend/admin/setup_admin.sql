-- Crear tabla de administradores si no existe
CREATE TABLE IF NOT EXISTS administradores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insertar administrador por defecto
-- La contraseña es 'admin123' hasheada con password_hash
INSERT INTO administradores (email, password, nombre) 
VALUES ('admin@mimopet.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador')
ON DUPLICATE KEY UPDATE 
    password = VALUES(password),
    nombre = VALUES(nombre); 