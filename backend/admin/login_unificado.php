<?php
/**
 * Endpoint Unificado de Login Administrativo - MimoPet
 * Punto único de autenticación para todo el panel administrativo
 */

require_once '../admin_auth.php';

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');

// Solo permitir POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false, 
        'message' => 'Método no permitido'
    ]);
    exit;
}

try {
    // Obtener datos del formulario
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Log de intento de login (sin mostrar la contraseña)
    error_log('Intento de login administrativo para: ' . $email);
    
    // Procesar login usando el sistema unificado
    $resultado = procesarLogin($email, $password);
    
    // Establecer código de respuesta HTTP apropiado
    if (!$resultado['success']) {
        http_response_code(401);
    }
    
    // Regenerar ID de sesión por seguridad en login exitoso
    if ($resultado['success']) {
        regenerarSesion();
    }
    
    echo json_encode($resultado);
    
} catch (Exception $e) {
    error_log('Error crítico en login_unificado.php: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error interno del servidor'
    ]);
}
?>