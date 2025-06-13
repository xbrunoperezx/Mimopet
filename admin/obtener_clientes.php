<?php
require_once '../backend/admin_auth.php';
require_once '../backend/conexion.php';

header('Content-Type: application/json');

// Verificar autenticación usando el sistema unificado
requiereAutenticacion();

try {
    // Determinar si se deben incluir clientes inactivos
    $incluirInactivos = isset($_GET['incluir_inactivos']) && $_GET['incluir_inactivos'] == '1';
    
    // Consulta para obtener clientes con el número de mascotas
    $query = "
        SELECT 
            c.*,
            COUNT(m.id) as num_mascotas
        FROM clientes c
        LEFT JOIN mascotas m ON c.id = m.cliente_id
        " . (!$incluirInactivos ? "WHERE c.activo = 1" : "") . "
        GROUP BY c.id
        ORDER BY c.activo DESC, c.nombre ASC
    ";
    
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Formatear fechas para mejor visualización
    foreach ($clientes as &$cliente) {
        if (isset($cliente['fecha_registro'])) {
            $fecha = new DateTime($cliente['fecha_registro']);
            $cliente['fecha_registro'] = $fecha->format('d/m/Y H:i');
        }
    }
    
    echo json_encode([
        'success' => true,
        'data' => $clientes
    ]);

} catch (PDOException $e) {
    error_log('Error en obtener_clientes.php: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener los clientes'
    ]);
} catch (Exception $e) {
    error_log('Error general en obtener_clientes.php: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error del servidor'
    ]);
}
?> 