<?php
require_once '../backend/admin_auth.php';
require_once '../backend/conexion.php';

header('Content-Type: application/json');

// Verificar autenticación usando el sistema unificado
requiereAutenticacion();

// Obtener y validar el ID y estado
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

if (!isset($data['id']) || !is_numeric($data['id']) || $data['id'] <= 0 || !isset($data['estado'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Datos incompletos o inválidos'
    ]);
    exit;
}

try {
    $id = (int)$data['id'];
    $nuevoEstado = (bool)$data['estado'];
    
    // Iniciar transacción
    $conn->beginTransaction();
    
    try {
        // Actualizar estado del cliente
        $query = "UPDATE clientes SET activo = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$nuevoEstado ? 1 : 0, $id]);
        
        if ($stmt->rowCount() === 0) {
            throw new Exception('Cliente no encontrado');
        }
        
        // Confirmar transacción
        $conn->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Estado del cliente actualizado correctamente'
        ]);
        
    } catch (Exception $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        throw $e;
    }

} catch (Exception $e) {
    error_log('Error en toggle_estado_cliente.php: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al actualizar el estado del cliente'
    ]);
}
?> 