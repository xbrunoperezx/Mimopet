<?php
require_once 'conexion.php';

try {
    // Primero verificamos si el usuario admin existe
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->execute(['admin@mimopet.com']);
    $usuario = $stmt->fetch();

    // Hash de la contraseña 'admin123'
    $password_hash = password_hash('admin123', PASSWORD_DEFAULT);

    if ($usuario) {
        // Si existe, actualizamos su contraseña
        $stmt = $pdo->prepare("UPDATE usuarios SET password = ? WHERE email = ?");
        $stmt->execute([$password_hash, 'admin@mimopet.com']);
        echo "Contraseña del administrador actualizada con éxito.";
    } else {
        // Si no existe, lo creamos
        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, ?)");
        $stmt->execute(['Administrador', 'admin@mimopet.com', $password_hash, 'admin']);
        echo "Usuario administrador creado con éxito.";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 