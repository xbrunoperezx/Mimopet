<?php
require_once '../admin_auth.php';

header('Content-Type: application/json');

// Verificar autenticación usando sistema unificado
if (!estaAutenticado()) {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

try {
    // Obtener las últimas 10 reservas con información completa
    $sql = "SELECT 
                r.id,
                r.fecha_inicio,
                r.hora_inicio,
                r.hora_fin,
                r.estado,
                c.nombre as cliente_nombre,
                m.nombre as mascota_nombre,
                m.especie as mascota_especie,
                s.nombre as servicio_nombre
            FROM reservas r
            JOIN clientes c ON r.cliente_id = c.id
            JOIN mascotas m ON r.mascota_id = m.id
            JOIN servicios s ON r.servicio_id = s.id
            ORDER BY r.fecha_inicio DESC, r.hora_inicio DESC
            LIMIT 10";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Formatear fechas y horas para mejor visualización
    foreach ($reservas as &$reserva) {
        $fecha = new DateTime($reserva['fecha_inicio']);
        $reserva['fecha_inicio'] = $fecha->format('d/m/Y');
        $reserva['hora_inicio'] = substr($reserva['hora_inicio'], 0, 5);
        $reserva['hora_fin'] = substr($reserva['hora_fin'], 0, 5);
    }
    
    echo json_encode($reservas);

} catch (PDOException $e) {
    error_log('Error en obtener_ultimas_reservas.php: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Error al obtener reservas: ' . $e->getMessage()]);
}
?> 