<?php
require_once '../backend/admin_auth.php';
require_once '../backend/conexion.php';

// Asegurar que la respuesta sea JSON
header('Content-Type: application/json');

// Verificar autenticación usando el sistema unificado
requiereAutenticacion();

// Validar método de solicitud
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
    exit;
}

// Validar datos obligatorios
$cliente_id = isset($_POST['cliente_id']) ? trim($_POST['cliente_id']) : '';
$nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
$especie = isset($_POST['especie']) ? trim($_POST['especie']) : '';

if (empty($cliente_id) || empty($nombre) || empty($especie)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Faltan datos obligatorios: ' . 
                    (empty($cliente_id) ? 'cliente_id ' : '') .
                    (empty($nombre) ? 'nombre ' : '') .
                    (empty($especie) ? 'especie' : '')
    ]);
    exit;
}

try {
    // Validar que el cliente existe y está activo
    $stmtCliente = $conn->prepare("SELECT id FROM clientes WHERE id = ? AND activo = 1");
    $stmtCliente->execute([(int)$cliente_id]);
    if (!$stmtCliente->fetch()) {
        throw new Exception('Cliente no encontrado o inactivo');
    }

    // Preparar los datos
    $datos = [
        'cliente_id' => (int)$cliente_id,
        'nombre' => $nombre,
        'especie' => $especie,
        'raza' => !empty($_POST['raza']) ? trim($_POST['raza']) : null,
        'edad' => !empty($_POST['edad']) ? (int)$_POST['edad'] : null,
        'peso' => !empty($_POST['peso']) ? (float)$_POST['peso'] : null,
        'notas' => !empty($_POST['notas']) ? trim($_POST['notas']) : null
    ];

    // Validar datos numéricos
    if ($datos['edad'] !== null && $datos['edad'] < 0) {
        throw new Exception('La edad no puede ser negativa');
    }
    if ($datos['peso'] !== null && $datos['peso'] <= 0) {
        throw new Exception('El peso debe ser mayor que 0');
    }

    // Insertar mascota
    $query = "INSERT INTO mascotas (cliente_id, nombre, especie, raza, edad, peso, notas) 
              VALUES (:cliente_id, :nombre, :especie, :raza, :edad, :peso, :notas)";
    
    $stmt = $conn->prepare($query);
    $stmt->execute($datos);

    echo json_encode([
        'success' => true,
        'message' => 'Mascota guardada correctamente',
        'id' => $conn->lastInsertId()
    ]);

} catch (PDOException $e) {
    error_log('Error en guardar_mascota.php: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al guardar la mascota en la base de datos'
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?> 