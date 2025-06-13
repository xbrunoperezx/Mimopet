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
    // Obtener conteo de reservas por estado
    $sql = "SELECT estado, COUNT(*) as total FROM reservas GROUP BY estado";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Inicializar contadores
    $estadisticas = [
        'pendientes' => 0,
        'confirmadas' => 0,
        'completadas' => 0,
        'canceladas' => 0
    ];
    
    // Procesar resultados
    foreach ($resultados as $resultado) {
        switch ($resultado['estado']) {
            case 'pendiente':
                $estadisticas['pendientes'] = (int)$resultado['total'];
                break;
            case 'confirmada':
                $estadisticas['confirmadas'] = (int)$resultado['total'];
                break;
            case 'completada':
                $estadisticas['completadas'] = (int)$resultado['total'];
                break;
            case 'cancelada':
                $estadisticas['canceladas'] = (int)$resultado['total'];
                break;
        }
    }
    
    echo json_encode($estadisticas);

} catch (PDOException $e) {
    error_log('Error en obtener_estadisticas.php: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Error al obtener estadísticas: ' . $e->getMessage()]);
}
?> 