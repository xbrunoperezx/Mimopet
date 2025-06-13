<?php
require_once '../backend/auth.php';
require_once '../backend/conexion.php';

// Verificar autenticación
verificarAutenticacion();

// Obtener y validar el ID
$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['id']) || !is_numeric($data['id'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'ID de mascota no válido'
    ]);
    exit;
}

try {
    $id = (int)$data['id'];
    
    // Eliminar mascota
    $query = "DELETE FROM mascotas WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$id]);
    
    // Verificar si se eliminó la mascota
    if ($stmt->rowCount() === 0) {
        throw new Exception('Mascota no encontrada');
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Mascota eliminada correctamente'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al eliminar la mascota: ' . $e->getMessage()
    ]);
}
?> 