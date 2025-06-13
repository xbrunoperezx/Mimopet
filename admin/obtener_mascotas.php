<?php
require_once '../backend/admin_auth.php';
require_once '../backend/conexion.php';

header('Content-Type: application/json');

// Verificar autenticación usando el sistema unificado
requiereAutenticacion();

// Validar ID del cliente
if (!isset($_GET['cliente_id']) || !is_numeric($_GET['cliente_id'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'ID de cliente no válido'
    ]);
    exit;
}

try {
    $clienteId = (int)$_GET['cliente_id'];
    
    // Obtener todas las mascotas del cliente
    $query = "SELECT * FROM mascotas WHERE cliente_id = ? ORDER BY nombre ASC";
    $stmt = $conn->prepare($query);
    $stmt->execute([$clienteId]);
    $mascotas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($mascotas);

} catch (PDOException $e) {
    error_log('Error en obtener_mascotas.php: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([]);
} catch (Exception $e) {
    error_log('Error general en obtener_mascotas.php: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([]);
}
?> 