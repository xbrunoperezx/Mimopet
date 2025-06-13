<?php
session_start();
header('Content-Type: application/json');

// Verificar si el usuario está autenticado
$autenticado = isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);

echo json_encode([
    'autenticado' => $autenticado
]);
?> 