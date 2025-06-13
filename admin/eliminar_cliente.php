<?php
// Configurar el archivo de log específico para este script
ini_set('log_errors', 1);
ini_set('error_log', dirname(__FILE__) . '/delete_client_errors.log');

require_once '../backend/admin_auth.php';
require_once '../backend/conexion.php';

// Activar la visualización de errores para debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Verificar autenticación usando el sistema unificado
requiereAutenticacion();

header('Content-Type: application/json');

// Log para verificar que el script se está ejecutando
error_log("=== Inicio de eliminación de cliente ===");

// Obtener y validar el ID
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Log de datos recibidos
error_log("Datos recibidos: " . print_r($input, true));
error_log("Datos decodificados: " . print_r($data, true));

if ($input === false || $data === null) {
    http_response_code(400);
    error_log("Error: Datos de entrada inválidos");
    echo json_encode([
        'success' => false,
        'message' => 'Datos de entrada inválidos'
    ]);
    exit;
}

if (!isset($data['id']) || !is_numeric($data['id']) || $data['id'] <= 0) {
    http_response_code(400);
    error_log("Error: ID de cliente no válido - " . print_r($data, true));
    echo json_encode([
        'success' => false,
        'message' => 'ID de cliente no válido'
    ]);
    exit;
}

try {
    $id = (int)$data['id'];
    error_log("Procesando eliminación del cliente ID: " . $id);
    
    // Verificar la conexión
    if (!$conn || !($conn instanceof PDO)) {
        throw new Exception('Error de conexión a la base de datos');
    }
    
    // Verificar si el cliente existe
    $queryVerificar = "SELECT id FROM clientes WHERE id = ?";
    $stmtVerificar = $conn->prepare($queryVerificar);
    $stmtVerificar->execute([$id]);
    
    if ($stmtVerificar->rowCount() === 0) {
        error_log("Cliente no encontrado: ID " . $id);
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Cliente no encontrado'
        ]);
        exit;
    }
    
    error_log("Cliente encontrado, iniciando proceso de eliminación");
    
    // Iniciar transacción
    $conn->beginTransaction();
    error_log("Transacción iniciada");
    
    try {
        // Primero, obtener todas las mascotas del cliente
        $queryMascotas = "SELECT id FROM mascotas WHERE cliente_id = ?";
        $stmtMascotas = $conn->prepare($queryMascotas);
        $stmtMascotas->execute([$id]);
        $mascotasIds = $stmtMascotas->fetchAll(PDO::FETCH_COLUMN);
        
        error_log("Mascotas encontradas: " . implode(', ', $mascotasIds));
        
        if (!empty($mascotasIds)) {
            // Primero eliminar las reservas asociadas a las mascotas
            $placeholders = str_repeat('?,', count($mascotasIds) - 1) . '?';
            $queryReservas = "DELETE FROM reservas WHERE mascota_id IN ($placeholders)";
            $stmtReservas = $conn->prepare($queryReservas);
            $stmtReservas->execute($mascotasIds);
            error_log("Reservas eliminadas: " . $stmtReservas->rowCount());
            
            // Luego eliminar las mascotas
            $queryEliminarMascotas = "DELETE FROM mascotas WHERE cliente_id = ?";
            $stmtEliminarMascotas = $conn->prepare($queryEliminarMascotas);
            $stmtEliminarMascotas->execute([$id]);
            error_log("Mascotas eliminadas: " . $stmtEliminarMascotas->rowCount());
        }
        
        // En lugar de eliminar, marcar como inactivo
        $queryDesactivar = "UPDATE clientes SET activo = 0 WHERE id = ?";
        $stmtDesactivar = $conn->prepare($queryDesactivar);
        $stmtDesactivar->execute([$id]);
        
        if ($stmtDesactivar->rowCount() === 0) {
            throw new Exception('Cliente no encontrado');
        }
        
        // Confirmar transacción
        $conn->commit();
        error_log("Transacción completada exitosamente");
        
        echo json_encode([
            'success' => true,
            'message' => 'Cliente desactivado correctamente',
            'details' => [
                'mascotas_eliminadas' => count($mascotasIds),
                'cliente_eliminado' => true
            ]
        ]);
        
    } catch (PDOException $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
            error_log("Transacción revertida debido a error en eliminación");
        }
        throw $e;
    }

} catch (Exception $e) {
    error_log("=== Error en el proceso de eliminación ===");
    error_log("Mensaje de error: " . $e->getMessage());
    error_log("Código de error: " . $e->getCode());
    
    if ($e instanceof PDOException) {
        error_log("SQL State: " . $e->errorInfo[0]);
        error_log("Error Code: " . $e->errorInfo[1]);
        error_log("Error Message: " . $e->errorInfo[2]);
    }
    
    if (isset($conn) && $conn->inTransaction()) {
        $conn->rollBack();
        error_log("Transacción revertida debido a error");
    }
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al desactivar el cliente',
        'debug_message' => $e->getMessage()
    ]);
}

error_log("=== Fin del proceso de eliminación ===");
?> 