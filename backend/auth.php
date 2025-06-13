<?php
session_start();

// Verifica si el usuario está autenticado, si no lo está, lo redirige a la página de login
function verificarAutenticacion() {
    if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol'])) {
        header('Location: login.html');
        exit;
    }
    
    if ($_SESSION['rol'] !== 'admin') {
        header('Location: ../index.html');
        exit;
    }
}

// Verifica si el usuario tiene rol de administrador
// Si no lo tiene, lo redirige a la página de acceso denegado
function verificarRolAdmin() {
    verificarAutenticacion();
    if ($_SESSION['rol'] !== 'admin') {
        header('Location: ../admin/acceso-denegado.html');
        exit();
    }
}

// Retorna un array con la información del usuario actual (id, nombre y rol)
// Si no hay sesión activa, retorna valores nulos
function obtenerUsuarioActual() {
    return [
        'id' => $_SESSION['usuario_id'] ?? null,
        'nombre' => $_SESSION['usuario_nombre'] ?? null,
        'rol' => $_SESSION['rol'] ?? null
    ];
}

function cerrarSesion() {
    session_start();
    session_destroy();
    header('Location: login.html');
    exit;
}
?> 