<?php
/**
 * Endpoint Unificado de Verificación de Autenticación - MimoPet
 * Verifica el estado de autenticación para el panel administrativo
 */

require_once '../admin_auth.php';

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

try {
    $autenticado = estaAutenticado();
    $admin = null;
    
    if ($autenticado) {
        // Verificar si la sesión ha expirado
        if (sesionExpirada()) {
            cerrarSesionAdmin();
            $autenticado = false;
        } else {
            $admin = obtenerAdminActual();
        }
    }
    
    echo json_encode([
        'autenticado' => $autenticado,
        'admin' => $admin,
        'timestamp' => time()
    ]);
    
} catch (Exception $e) {
    error_log('Error en verificar_auth_unificado.php: ' . $e->getMessage());
    echo json_encode([
        'autenticado' => false,
        'admin' => null,
        'error' => 'Error del servidor'
    ]);
}
?> 