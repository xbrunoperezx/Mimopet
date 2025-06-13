<?php
require_once 'conexion.php';
header('Content-Type: application/json');

// Establecer la zona horaria a Madrid/España
date_default_timezone_set('Europe/Madrid');

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

try {
    $conn->beginTransaction();

    // Validar datos recibidos
    $data = $_POST;
    error_log('Datos recibidos en procesar_reserva_frontend.php: ' . print_r($data, true));

    // Validar campos requeridos
    $campos_requeridos = [
        'nombreCompleto',
        'email',
        'telefono',
        'nombreMascota',
        'especieMascota',
        'tipoServicio',
        'fechaReserva',
        'horaDesde',
        'horaHasta'
    ];

    foreach ($campos_requeridos as $campo) {
        if (empty($data[$campo])) {
            throw new Exception("El campo $campo es requerido");
        }
    }

    // 1. Insertar o actualizar cliente
    $sql_cliente = "INSERT INTO clientes (nombre, email, telefono) 
                   VALUES (?, ?, ?) 
                   ON DUPLICATE KEY UPDATE 
                   nombre = VALUES(nombre), 
                   telefono = VALUES(telefono)";
    
    $stmt = $conn->prepare($sql_cliente);
    $stmt->execute([
        $data['nombreCompleto'],
        $data['email'],
        $data['telefono']
    ]);

    // Obtener ID del cliente
    $clienteId = $conn->lastInsertId();
    if (!$clienteId) {
        $stmt = $conn->prepare("SELECT id FROM clientes WHERE email = ?");
        $stmt->execute([$data['email']]);
        $clienteId = $stmt->fetchColumn();
    }

    // 2. Insertar o actualizar mascota
    $sql_mascota = "INSERT INTO mascotas (nombre, cliente_id, especie) 
                    VALUES (?, ?, ?) 
                    ON DUPLICATE KEY UPDATE 
                    nombre = VALUES(nombre),
                    especie = VALUES(especie)";
    
    $stmt = $conn->prepare($sql_mascota);
    $stmt->execute([
        $data['nombreMascota'],
        $clienteId,
        $data['especieMascota']
    ]);

    // Obtener ID de la mascota
    $mascotaId = $conn->lastInsertId();
    if (!$mascotaId) {
        $stmt = $conn->prepare("SELECT id FROM mascotas WHERE nombre = ? AND cliente_id = ?");
        $stmt->execute([$data['nombreMascota'], $clienteId]);
        $mascotaId = $stmt->fetchColumn();
    }

    // 3. Obtener ID del servicio
    $stmt = $conn->prepare("SELECT id FROM servicios WHERE nombre = ?");
    $stmt->execute([$data['tipoServicio']]);
    $servicioId = $stmt->fetchColumn();

    if (!$servicioId) {
        throw new Exception('Servicio no encontrado');
    }

    // 4. Formatear fecha y horas para MySQL
    // Crear un objeto DateTime con la zona horaria de Madrid
    $dateTime = new DateTime($data['fechaReserva'], new DateTimeZone('Europe/Madrid'));
    $fecha = $dateTime->format('Y-m-d');
    
    // Formatear las horas manteniendo la zona horaria
    $hora_inicio = new DateTime($data['horaDesde'], new DateTimeZone('Europe/Madrid'));
    $hora_fin = new DateTime($data['horaHasta'], new DateTimeZone('Europe/Madrid'));
    
    $hora_inicio = $hora_inicio->format('H:i:s');
    $hora_fin = $hora_fin->format('H:i:s');

    // Log para depuración
    error_log("Fecha y hora recibidas del frontend:");
    error_log("fechaReserva original: " . $data['fechaReserva']);
    error_log("horaDesde original: " . $data['horaDesde']);
    error_log("horaHasta original: " . $data['horaHasta']);
    error_log("Después de formatear:");
    error_log("fecha: " . $fecha);
    error_log("hora_inicio: " . $hora_inicio);
    error_log("hora_fin: " . $hora_fin);

    // 5. Verificar disponibilidad
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
        throw new Exception('El horario seleccionado no está disponible');
    }

    // 6. Insertar la reserva
    $sql_reserva = "INSERT INTO reservas 
                    (cliente_id, mascota_id, servicio_id, fecha_inicio, hora_inicio, hora_fin, estado) 
                    VALUES 
                    (?, ?, ?, ?, ?, ?, 'pendiente')";
    
    $stmt = $conn->prepare($sql_reserva);
    $stmt->execute([
        $clienteId,
        $mascotaId,
        $servicioId,
        $fecha,
        $hora_inicio,
        $hora_fin
    ]);

    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Reserva creada exitosamente'
    ]);

} catch (Exception $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    error_log('Error en procesar_reserva_frontend.php: ' . $e->getMessage());
    error_log('Stack trace: ' . $e->getTraceAsString());
    
    echo json_encode([
        'success' => false,
        'message' => 'Error al procesar la reserva: ' . $e->getMessage()
    ]);
}
?> 