<?php
session_start();

// Si el usuario ya está autenticado, redirigir al panel
if (isset($_SESSION['usuario_id']) && isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin') {
    header('Location: calendario.php');
    exit;
}

// Si no está autenticado, redirigir al login
header('Location: login.html');
exit;
?> 