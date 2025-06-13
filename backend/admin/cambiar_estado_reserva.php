<?php
require_once '../conexion.php';
session_start();
header('Content-Type: application/json');

// Verificar autenticación
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

try {
    // Validar datos recibidos
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['reserva_id']) || !isset($data['nuevo_estado'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Datos incompletos']);
        exit;
    }

    $estados_validos = ['pendiente', 'confirmada', 'completada', 'cancelada'];
    if (!in_array($data['nuevo_estado'], $estados_validos)) {
        http_response_code(400);
        echo json_encode(['error' => 'Estado no válido']);
        exit;
    }

    // Actualizar el estado de la reserva
    $sql = "UPDATE reservas SET estado = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$data['nuevo_estado'], $data['reserva_id']]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Estado actualizado correctamente']);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Reserva no encontrada']);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al actualizar el estado: ' . $e->getMessage()]);
} 