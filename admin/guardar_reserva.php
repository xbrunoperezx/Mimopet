<?php
require_once '../backend/admin_auth.php';
require_once '../backend/conexion.php';

// Asegurar que la respuesta sea JSON
header('Content-Type: application/json');

// Establecer la zona horaria a Madrid/España
date_default_timezone_set('Europe/Madrid');

// Verificar autenticación usando el sistema unificado
requiereAutenticacion();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

try {
    // Validar datos requeridos
    $campos_requeridos = ['servicio_id', 'cliente_id', 'mascota_id', 'fecha', 'hora_inicio', 'hora_fin'];
    foreach ($campos_requeridos as $campo) {
        if (!isset($_POST[$campo]) || empty($_POST[$campo])) {
            throw new Exception("El campo $campo es requerido");
        }
    }

    // Formatear la fecha para MySQL (DATE)
    $fecha = date('Y-m-d', strtotime($_POST['fecha']));
    
    // Formatear las horas para MySQL (TIME)
    $hora_inicio = date('H:i:s', strtotime($_POST['hora_inicio']));
    $hora_fin = date('H:i:s', strtotime($_POST['hora_fin']));

    // Verificar que la hora de fin sea posterior a la hora de inicio
    if (strtotime($hora_fin) <= strtotime($hora_inicio)) {
        throw new Exception("La hora de fin debe ser posterior a la hora de inicio");
    }

    // Verificar disponibilidad
    $stmt = $conn->prepare("
        SELECT COUNT(*) FROM reservas 
        WHERE fecha_inicio = ? 
        AND estado != 'cancelada'
        AND (
            (hora_inicio < ? AND hora_fin > ?)
            OR (hora_inicio BETWEEN ? AND ?)
            OR (hora_fin BETWEEN ? AND ?)
        )
    ");
    $stmt->execute([
        $fecha,
        $hora_fin,
        $hora_inicio,
        $hora_inicio,
        $hora_fin,
        $hora_inicio,
        $hora_fin
    ]);
    
    if ($stmt->fetchColumn() > 0) {
        throw new Exception("Ya existe una reserva en ese horario");
    }

    // Insertar la reserva
    $stmt = $conn->prepare("
        INSERT INTO reservas (
            cliente_id, mascota_id, servicio_id, 
            fecha_inicio, hora_inicio, hora_fin, 
            estado
        ) VALUES (?, ?, ?, ?, ?, ?, 'pendiente')
    ");

    $stmt->execute([
        $_POST['cliente_id'],
        $_POST['mascota_id'],
        $_POST['servicio_id'],
        $fecha,
        $hora_inicio,
        $hora_fin
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Reserva creada con éxito',
        'id' => $conn->lastInsertId()
    ]);

} catch (PDOException $e) {
    error_log('Error en guardar_reserva.php: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al guardar la reserva en la base de datos'
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?> 