<?php
/**
 * Script para verificar y corregir la tabla administradores - MimoPet
 */

require_once '../conexion.php';

echo "<h2>🔍 Diagnóstico de Tabla Administradores</h2>";

try {
    // Verificar si la tabla administradores existe
    $checkTable = $conn->query("SHOW TABLES LIKE 'administradores'");
    
    if ($checkTable->rowCount() == 0) {
        echo "❌ La tabla 'administradores' NO existe.<br>";
        exit;
    }
    
    echo "✅ La tabla 'administradores' existe.<br><br>";
    
    // Mostrar estructura actual de la tabla
    echo "<h3>📋 Estructura actual de la tabla:</h3>";
    $estructura = $conn->query("DESCRIBE administradores");
    
    echo "<table border='1' style='border-collapse: collapse; margin-bottom: 20px;'>";
    echo "<tr style='background-color: #f0f0f0;'>";
    echo "<th style='padding: 8px;'>Campo</th>";
    echo "<th style='padding: 8px;'>Tipo</th>";
    echo "<th style='padding: 8px;'>Null</th>";
    echo "<th style='padding: 8px;'>Key</th>";
    echo "<th style='padding: 8px;'>Default</th>";
    echo "<th style='padding: 8px;'>Extra</th>";
    echo "</tr>";
    
    $columnas_existentes = [];
    while ($col = $estructura->fetch()) {
        $columnas_existentes[] = $col['Field'];
        echo "<tr>";
        echo "<td style='padding: 8px;'>{$col['Field']}</td>";
        echo "<td style='padding: 8px;'>{$col['Type']}</td>";
        echo "<td style='padding: 8px;'>{$col['Null']}</td>";
        echo "<td style='padding: 8px;'>{$col['Key']}</td>";
        echo "<td style='padding: 8px;'>{$col['Default']}</td>";
        echo "<td style='padding: 8px;'>{$col['Extra']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Verificar columnas requeridas
    $columnas_requeridas = ['id', 'nombre', 'email', 'password', 'activo', 'created_at', 'updated_at'];
    $columnas_faltantes = array_diff($columnas_requeridas, $columnas_existentes);
    
    if (!empty($columnas_faltantes)) {
        echo "<h3>⚠️ Columnas faltantes que se necesitan agregar:</h3>";
        echo "<ul>";
        foreach ($columnas_faltantes as $columna) {
            echo "<li><strong>{$columna}</strong></li>";
        }
        echo "</ul>";
        
        // Agregar columnas faltantes
        echo "<h3>🔧 Agregando columnas faltantes...</h3>";
        
        foreach ($columnas_faltantes as $columna) {
            try {
                switch ($columna) {
                    case 'activo':
                        $conn->exec("ALTER TABLE administradores ADD COLUMN activo TINYINT(1) DEFAULT 1");
                        echo "✅ Columna 'activo' agregada<br>";
                        break;
                    case 'created_at':
                        $conn->exec("ALTER TABLE administradores ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
                        echo "✅ Columna 'created_at' agregada<br>";
                        break;
                    case 'updated_at':
                        $conn->exec("ALTER TABLE administradores ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
                        echo "✅ Columna 'updated_at' agregada<br>";
                        break;
                    case 'nombre':
                        $conn->exec("ALTER TABLE administradores ADD COLUMN nombre VARCHAR(100) NOT NULL");
                        echo "✅ Columna 'nombre' agregada<br>";
                        break;
                }
            } catch (Exception $e) {
                echo "❌ Error agregando columna '{$columna}': " . $e->getMessage() . "<br>";
            }
        }
        
        echo "<br>🔄 <strong>Columnas agregadas. Verificando nueva estructura...</strong><br><br>";
        
        // Mostrar nueva estructura
        echo "<h3>📋 Nueva estructura de la tabla:</h3>";
        $nueva_estructura = $conn->query("DESCRIBE administradores");
        
        echo "<table border='1' style='border-collapse: collapse; margin-bottom: 20px;'>";
        echo "<tr style='background-color: #e8f5e8;'>";
        echo "<th style='padding: 8px;'>Campo</th>";
        echo "<th style='padding: 8px;'>Tipo</th>";
        echo "<th style='padding: 8px;'>Null</th>";
        echo "<th style='padding: 8px;'>Key</th>";
        echo "<th style='padding: 8px;'>Default</th>";
        echo "<th style='padding: 8px;'>Extra</th>";
        echo "</tr>";
        
        while ($col = $nueva_estructura->fetch()) {
            echo "<tr>";
            echo "<td style='padding: 8px;'>{$col['Field']}</td>";
            echo "<td style='padding: 8px;'>{$col['Type']}</td>";
            echo "<td style='padding: 8px;'>{$col['Null']}</td>";
            echo "<td style='padding: 8px;'>{$col['Key']}</td>";
            echo "<td style='padding: 8px;'>{$col['Default']}</td>";
            echo "<td style='padding: 8px;'>{$col['Extra']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
    } else {
        echo "✅ <strong>Todas las columnas requeridas están presentes.</strong><br>";
    }
    
    // Verificar y crear/actualizar administrador
    echo "<h3>👤 Configurando administrador principal...</h3>";
    
    // Verificar si existe administrador
    $checkAdmin = $conn->prepare("SELECT id, nombre, email FROM administradores WHERE email = ?");
    $checkAdmin->execute(['admin@mimopet.com']);
    $adminExistente = $checkAdmin->fetch();
    
    if (!$adminExistente) {
        // Crear administrador
        $email = 'admin@mimopet.com';
        $password = 'admin123';
        $nombre = 'Administrador Principal';
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $insertAdmin = $conn->prepare("
            INSERT INTO administradores (nombre, email, password, activo) 
            VALUES (?, ?, ?, 1)
        ");
        
        $insertAdmin->execute([$nombre, $email, $hashedPassword]);
        
        echo "✅ Administrador creado exitosamente<br>";
        echo "📧 Email: {$email}<br>";
        echo "🔑 Contraseña: {$password}<br>";
        
    } else {
        // Actualizar administrador existente
        $password = 'admin123';
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $updateAdmin = $conn->prepare("
            UPDATE administradores 
            SET password = ?, activo = 1, nombre = ? 
            WHERE email = ?
        ");
        
        $updateAdmin->execute([$hashedPassword, 'Administrador Principal', 'admin@mimopet.com']);
        
        echo "✅ Administrador actualizado exitosamente<br>";
        echo "📧 Email: admin@mimopet.com<br>";
        echo "🔑 Contraseña: admin123<br>";
    }
    
    // Mostrar todos los administradores
    echo "<h3>📋 Administradores en el sistema:</h3>";
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
        $created = $admin['created_at'] ?? 'N/A';
        echo "<tr>";
        echo "<td style='padding: 8px;'>{$admin['id']}</td>";
        echo "<td style='padding: 8px;'>{$admin['nombre']}</td>";
        echo "<td style='padding: 8px;'>{$admin['email']}</td>";
        echo "<td style='padding: 8px;'>{$activo}</td>";
        echo "<td style='padding: 8px;'>{$created}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<br><hr>";
    echo "<h3>🎯 ¡Todo listo!</h3>";
    echo "<p><strong>Ahora puedes acceder al panel administrativo:</strong></p>";
    echo "<p>🔗 <a href='http://localhost/mitienda/pages/admin/index.html' target='_blank' style='font-size: 18px; color: #007bff;'>ACCEDER AL PANEL ADMINISTRATIVO</a></p>";
    echo "<div style='background-color: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<p><strong>📧 Email:</strong> admin@mimopet.com</p>";
    echo "<p><strong>🔑 Contraseña:</strong> admin123</p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
    error_log("Error en verificar_tabla_admin.php: " . $e->getMessage());
}
?> 