<?php
require_once '../conexion.php';
require_once '../admin_auth.php';

// Verificar autenticación
requiereAutenticacion();

header('Content-Type: application/json');

try {
    // Obtener datos del body
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['id'])) {
        throw new Exception('ID del servicio no proporcionado');
    }

    $id = intval($data['id']);

    // Eliminación física del registro
    $stmt = $conn->prepare("DELETE FROM servicios WHERE id = ?");
    $stmt->execute([$id]);

    if ($stmt->rowCount() === 0) {
        throw new Exception('No se encontró el servicio especificado');
    }

    echo json_encode([
        'success' => true,
        'message' => 'Servicio eliminado exitosamente'
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 