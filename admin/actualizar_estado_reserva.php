<?php
require_once '../backend/admin_auth.php';
require_once '../backend/conexion.php';

header('Content-Type: application/json');

// Verificar autenticación usando el sistema unificado
requiereAutenticacion();

// Obtener datos del cuerpo de la petición
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if ($input === false || $data === null) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Datos de entrada inválidos'
    ]);
    exit;
}

// Validar datos recibidos
if (!isset($data['id']) || !isset($data['estado']) || !is_numeric($data['id'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Datos incompletos o inválidos'
    ]);
    exit;
}

// Validar que el estado sea válido
$estados_validos = ['pendiente', 'confirmada', 'completada', 'cancelada'];
if (!in_array($data['estado'], $estados_validos)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Estado no válido'
    ]);
    exit;
}

try {
    $id = (int)$data['id'];
    $estado = $data['estado'];
    
    // Verificar si la reserva existe
    $queryVerificar = "SELECT id FROM reservas WHERE id = ?";
    $stmtVerificar = $conn->prepare($queryVerificar);
    $stmtVerificar->execute([$id]);
    
    if ($stmtVerificar->rowCount() === 0) {
        throw new Exception('Reserva no encontrada');
    }
    
    // Actualizar el estado
    $query = "UPDATE reservas SET estado = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$estado, $id]);
    
    if ($stmt->rowCount() === 0) {
        throw new Exception('No se pudo actualizar el estado');
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Estado actualizado correctamente'
    ]);

} catch (Exception $e) {
    error_log('Error en actualizar_estado_reserva.php: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al actualizar el estado de la reserva'
    ]);
}
?> 