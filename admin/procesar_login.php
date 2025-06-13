<?php
session_start();
require_once '../backend/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    try {
        $stmt = $conn->prepare("SELECT id, nombre, password, rol FROM usuarios WHERE email = ? AND activo = 1");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($password, $usuario['password'])) {
            // Credenciales correctas
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['rol'] = $usuario['rol'];
            
            // Redirigir al calendario
            header('Location: calendario.php');
            exit;
        } else {
            // Credenciales incorrectas
            header('Location: login.html?error=1');
            exit;
        }
    } catch (PDOException $e) {
        // Error de base de datos
        header('Location: login.html?error=2');
        exit;
    }
} else {
    // Método no permitido
    header('Location: login.html');
    exit;
}
?> 