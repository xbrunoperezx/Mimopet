<?php
require_once '../admin_auth.php';
require_once '../conexion.php';

header('Content-Type: application/json');

// Verificar la conexión a la base de datos
try {
    $conn->query("SELECT 1");
    error_log("Conexión a la base de datos exitosa");
} catch (PDOException $e) {
    error_log("Error de conexión a la base de datos: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

try {
    $data = $_POST;
    error_log('Datos recibidos en login: ' . print_r($data, true));
    
    // Usar el sistema unificado de autenticación
    $resultado = procesarLogin($data['email'], $data['password']);
    
    if ($resultado['success']) {
        error_log('Login exitoso para: ' . $data['email']);
        error_log('Datos de sesión: ' . print_r($_SESSION, true));
    } else {
        error_log('Login fallido para: ' . $data['email']);
        http_response_code(401);
    }
    
    echo json_encode($resultado);

} catch (Exception $e) {
    error_log('Error en login: ' . $e->getMessage());
    error_log('Stack trace: ' . $e->getTraceAsString());
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?> 