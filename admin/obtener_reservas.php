<?php
require_once '../backend/admin_auth.php';
require_once '../backend/conexion.php';

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Establecer la zona horaria a Madrid/España
date_default_timezone_set('Europe/Madrid');

// Verificar autenticación usando el sistema unificado
requiereAutenticacion();

try {
    // Obtener fechas del rango solicitado y mostrar para depuración
    $start = isset($_GET['start']) ? $_GET['start'] : date('Y-m-d');
    $end = isset($_GET['end']) ? $_GET['end'] : date('Y-m-d', strtotime('+1 month'));
    
    error_log("Fechas recibidas - Start: " . $start . ", End: " . $end);

    // Convertir las fechas a formato MySQL
    $start_mysql = date('Y-m-d', strtotime($start));
    $end_mysql = date('Y-m-d', strtotime($end));
    
    error_log("Fechas MySQL - Start: " . $start_mysql . ", End: " . $end_mysql);

    // Primero, verificar si hay reservas en el rango
    $check_sql = "SELECT COUNT(*) as total FROM reservas WHERE fecha_inicio BETWEEN ? AND ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->execute([$start_mysql, $end_mysql]);
    $total = $check_stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    error_log("Total de reservas encontradas: " . $total);

    // Preparar la consulta principal
    $sql = "
        SELECT 
            r.id,
            r.fecha_inicio,
            r.hora_inicio,
            r.hora_fin,
            CONCAT(c.nombre, ' - ', m.nombre, ' (', s.nombre, ')') as title,
            CASE 
                WHEN r.estado = 'pendiente' THEN '#ffc107'
                WHEN r.estado = 'confirmada' THEN '#28a745'
                WHEN r.estado = 'completada' THEN '#17a2b8'
                WHEN r.estado = 'cancelada' THEN '#dc3545'
            END as backgroundColor,
            r.estado as status,
            s.precio_base as price,
            '' as description
        FROM reservas r
        JOIN clientes c ON r.cliente_id = c.id
        JOIN mascotas m ON r.mascota_id = m.id
        JOIN servicios s ON r.servicio_id = s.id
        WHERE r.fecha_inicio BETWEEN ? AND ?
        ORDER BY r.fecha_inicio, r.hora_inicio
    ";

    error_log("SQL Query: " . $sql);

    $stmt = $conn->prepare($sql);
    $stmt->execute([$start_mysql, $end_mysql]);
    $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    error_log("Reservas encontradas: " . json_encode($reservas));

    // Formatear las fechas para el calendario
    foreach ($reservas as &$reserva) {
        // Crear objetos DateTime con la zona horaria correcta
        $fecha = new DateTime($reserva['fecha_inicio'], new DateTimeZone('Europe/Madrid'));
        $hora_inicio = new DateTime($reserva['hora_inicio'], new DateTimeZone('Europe/Madrid'));
        $hora_fin = new DateTime($reserva['hora_fin'], new DateTimeZone('Europe/Madrid'));
        
        // Obtener solo las partes de hora y minutos
        $hora_inicio_str = $hora_inicio->format('H:i:s');
        $hora_fin_str = $hora_fin->format('H:i:s');
        
        // Crear las fechas completas para el calendario
        $start_date = clone $fecha;
        $end_date = clone $fecha;
        
        // Establecer las horas
        $start_date->modify($hora_inicio_str);
        $end_date->modify($hora_fin_str);
        
        // Formatear para el calendario
        $reserva['start'] = $start_date->format('Y-m-d\TH:i:s');
        $reserva['end'] = $end_date->format('Y-m-d\TH:i:s');
        
        error_log("Fecha procesada - Start: " . $reserva['start'] . ", End: " . $reserva['end']);
    }

    echo json_encode($reservas);

} catch (PDOException $e) {
    error_log("Error PDO en obtener_reservas.php: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    http_response_code(500);
    echo json_encode([
        'error' => 'Error al obtener las reservas',
        'message' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
} catch (Exception $e) {
    error_log("Error general en obtener_reservas.php: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    http_response_code(500);
    echo json_encode([
        'error' => 'Error del servidor',
        'message' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}
?> 