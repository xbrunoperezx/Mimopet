<?php
/**
 * Endpoint Unificado de Logout Administrativo - MimoPet
 * Punto único de cierre de sesión para todo el panel administrativo
 */

require_once '../admin_auth.php';

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');

try {
    // Obtener datos del admin antes de cerrar sesión (para logs)
    $adminActual = obtenerAdminActual();
    
    if ($adminActual) {
        error_log('Cerrando sesión para administrador: ' . $adminActual['email']);
    }
    
    // Cerrar sesión usando el sistema unificado
    cerrarSesionAdmin();
    
    echo json_encode([
        'success' => true,
        'message' => 'Sesión cerrada exitosamente'
    ]);
    
} catch (Exception $e) {
    error_log('Error en logout_unificado.php: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al cerrar sesión'
    ]);
}
?> 