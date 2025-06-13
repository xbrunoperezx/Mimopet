<?php
/**
 * Script para crear administrador inicial - MimoPet
 * Ejecutar una sola vez para configurar el primer administrador
 */

require_once '../conexion.php';

try {
    // Verificar si la tabla administradores existe
    $checkTable = $conn->query("SHOW TABLES LIKE 'administradores'");
    
    if ($checkTable->rowCount() == 0) {
        // Crear tabla administradores si no existe
        $createTable = "
        CREATE TABLE administradores (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(100) NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            activo TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        
        $conn->exec($createTable);
        echo "✅ Tabla 'administradores' creada exitosamente.<br>";
    }
    
    // Verificar si ya existe el administrador
    $checkAdmin = $conn->prepare("SELECT id FROM administradores WHERE email = ?");
    $checkAdmin->execute(['admin@mimopet.com']);
    
    if ($checkAdmin->rowCount() == 0) {
        // Crear administrador inicial
        $email = 'admin@mimopet.com';
        $password = 'admin123';
        $nombre = 'Administrador Principal';
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $insertAdmin = $conn->prepare("
            INSERT INTO administradores (nombre, email, password, activo) 
            VALUES (?, ?, ?, 1)
        ");
        
        $insertAdmin->execute([$nombre, $email, $hashedPassword]);
        
        echo "✅ Administrador creado exitosamente:<br>";
        echo "📧 Email: {$email}<br>";
        echo "🔑 Contraseña: {$password}<br>";
        echo "👤 Nombre: {$nombre}<br>";
        echo "<br>🎯 <strong>Ahora puedes acceder con estas credenciales.</strong><br>";
        
    } else {
        echo "⚠️ El administrador ya existe en la base de datos.<br>";
        
        // Actualizar contraseña por si acaso
        $password = 'admin123';
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $updateAdmin = $conn->prepare("
            UPDATE administradores 
            SET password = ?, activo = 1 
            WHERE email = ?
        ");
        
        $updateAdmin->execute([$hashedPassword, 'admin@mimopet.com']);
        
        echo "✅ Contraseña actualizada para admin@mimopet.com<br>";
        echo "🔑 Nueva contraseña: admin123<br>";
    }
    
    // Mostrar todos los administradores
    echo "<br>📋 <strong>Administradores en el sistema:</strong><br>";
    $admins = $conn->query("SELECT id, nombre, email, activo, created_at FROM administradores");
    
    echo "<table border='1' style='border-collapse: collapse; margin-top: 10px;'>";
    echo "<tr style='background-color: #f0f0f0;'>";
    echo "<th style='padding: 8px;'>ID</th>";
    echo "<th style='padding: 8px;'>Nombre</th>";
    echo "<th style='padding: 8px;'>Email</th>";
    echo "<th style='padding: 8px;'>Activo</th>";
    echo "<th style='padding: 8px;'>Creado</th>";
    echo "</tr>";
    
    while ($admin = $admins->fetch()) {
        $activo = $admin['activo'] ? '✅ Sí' : '❌ No';
        echo "<tr>";
        echo "<td style='padding: 8px;'>{$admin['id']}</td>";
        echo "<td style='padding: 8px;'>{$admin['nombre']}</td>";
        echo "<td style='padding: 8px;'>{$admin['email']}</td>";
        echo "<td style='padding: 8px;'>{$activo}</td>";
        echo "<td style='padding: 8px;'>{$admin['created_at']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<br><hr>";
    echo "<h3>🎯 Próximo paso:</h3>";
    echo "<p><strong>Accede al panel administrativo:</strong></p>";
    echo "<p>🔗 <a href='http://localhost/mitienda/pages/admin/index.html' target='_blank'>http://localhost/mitienda/pages/admin/index.html</a></p>";
    echo "<p>📧 Email: admin@mimopet.com</p>";
    echo "<p>🔑 Contraseña: admin123</p>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
    error_log("Error en setup_admin_inicial.php: " . $e->getMessage());
}
?> 