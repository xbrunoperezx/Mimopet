<?php
header('Content-Type: application/json');
require_once 'conexion.php';

try {
    // Validar y obtener parámetros
    $params = ['fecha', 'hora_inicio', 'hora_fin'];
    foreach ($params as $param) {
        if (!isset($_GET[$param]) || empty($_GET[$param])) {
            throw new Exception("Falta el parámetro requerido: $param");
        }
    }

    // Obtener y sanitizar parámetros
    $fecha = $_GET['fecha'];
    $hora_inicio = $_GET['hora_inicio'];
    $hora_fin = $_GET['hora_fin'];

    // Log de parámetros recibidos
    error_log("Parámetros recibidos - fecha: $fecha, hora_inicio: $hora_inicio, hora_fin: $hora_fin");

    // Consulta simplificada
    $sql = "SELECT COUNT(*) as count FROM reservas 
            WHERE fecha_inicio = ? 
            AND (
                (hora_inicio < ? AND hora_fin > ?) OR
                (hora_inicio < ? AND hora_fin > ?) OR
                (hora_inicio >= ? AND hora_fin <= ?)
            )
            AND estado != 'cancelada'";

    $stmt = $conn->prepare($sql);
    
    // Pasar parámetros en el orden correcto
    $stmt->execute([
        $fecha,
        $hora_fin,   // Para verificar si hay una reserva que incluya nuestro horario
        $hora_inicio,
        $hora_inicio, // Para verificar si la hora de inicio cae dentro de otra reserva
        $hora_fin,
        $hora_inicio, // Para verificar si la hora de fin cae dentro de otra reserva
        $hora_fin
    ]);

    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    $disponible = (int)$resultado['count'] === 0;

    error_log("Resultado de la consulta: " . print_r($resultado, true));
    error_log("Disponibilidad: " . ($disponible ? "Disponible" : "No disponible"));

    echo json_encode([
        'success' => true,
        'disponible' => $disponible,
        'message' => $disponible ? 'Horario disponible' : 'Horario no disponible'
    ]);

} catch (Exception $e) {
    error_log("Error en verificar_disponibilidad.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'disponible' => false,
        'message' => 'Error al verificar disponibilidad: ' . $e->getMessage()
    ]);
}
?> 