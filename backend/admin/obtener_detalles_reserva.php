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
    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'ID de reserva no proporcionado']);
        exit;
    }

    $sql = "SELECT 
                r.id,
                r.fecha_inicio,
                r.hora_inicio,
                r.hora_fin,
                r.estado,
                r.created_at,
                c.nombre as cliente_nombre,
                c.email as cliente_email,
                c.telefono as cliente_telefono,
                m.nombre as mascota_nombre,
                m.especie as mascota_especie,
                m.raza as mascota_raza,
                m.edad as mascota_edad,
                s.nombre as servicio_nombre,
                s.descripcion as servicio_descripcion,
                s.precio_base as servicio_precio
            FROM reservas r
            JOIN clientes c ON r.cliente_id = c.id
            JOIN mascotas m ON r.mascota_id = m.id
            JOIN servicios s ON r.servicio_id = s.id
            WHERE r.id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$_GET['id']]);
    $reserva = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($reserva) {
        echo json_encode([
            'success' => true,
            'reserva' => $reserva
        ]);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Reserva no encontrada']);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al obtener detalles de la reserva: ' . $e->getMessage()]);
} 