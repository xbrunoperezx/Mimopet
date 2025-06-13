<?php
require_once '../conexion.php';
require_once '../admin_auth.php';

// Verificar autenticación
requiereAutenticacion();

header('Content-Type: application/json');

try {
    // Obtener y validar datos
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $precio_base = floatval($_POST['precio']);

    // Validaciones
    if (empty($nombre)) {
        throw new Exception('El nombre del servicio es requerido');
    }
    if ($precio_base <= 0) {
        throw new Exception('El precio debe ser mayor a 0');
    }

    // Crear nuevo servicio
    $stmt = $conn->prepare("INSERT INTO servicios (nombre, descripcion, precio_base, duracion_minutos, activo) VALUES (?, ?, ?, 60, 1)");
    $stmt->execute([$nombre, $descripcion, $precio_base]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Servicio creado exitosamente'
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 