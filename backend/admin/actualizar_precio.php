<?php
require_once '../conexion.php';
require_once '../admin_auth.php';

// Verificar autenticación
requiereAutenticacion();

header('Content-Type: application/json');

try {
    // Obtener datos
    $id = intval($_POST['id']);
    $precio_base = floatval($_POST['precio']);

    // Validaciones
    if ($precio_base <= 0) {
        throw new Exception('El precio debe ser mayor a 0');
    }

    // Actualizar solo el precio
    $stmt = $conn->prepare("UPDATE servicios SET precio_base = ? WHERE id = ?");
    $stmt->execute([$precio_base, $id]);

    if ($stmt->rowCount() === 0) {
        throw new Exception('No se encontró el servicio especificado');
    }

    echo json_encode([
        'success' => true,
        'message' => 'Precio actualizado exitosamente'
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 