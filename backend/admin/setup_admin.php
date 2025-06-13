<?php
require_once '../conexion.php';

try {
    // Crear la tabla de administradores
    $sql_tabla = "CREATE TABLE IF NOT EXISTS administradores (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        nombre VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($sql_tabla);
    
    // Datos del administrador
    $email = 'admin@mimopet.com';
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    $nombre = 'Administrador';
    
    // Insertar o actualizar administrador
    $sql_admin = "INSERT INTO administradores (email, password, nombre) 
                  VALUES (:email, :password, :nombre)
                  ON DUPLICATE KEY UPDATE 
                  password = VALUES(password),
                  nombre = VALUES(nombre)";
    
    $stmt = $pdo->prepare($sql_admin);
    $stmt->execute([
        ':email' => $email,
        ':password' => $password,
        ':nombre' => $nombre
    ]);
    
    echo "Configuración de administrador completada exitosamente.\n";
    echo "Email: admin@mimopet.com\n";
    echo "Contraseña: admin123\n";
    echo "Hash generado: " . $password . "\n";

} catch (PDOException $e) {
    echo "Error al configurar el administrador: " . $e->getMessage() . "\n";
}
?> 