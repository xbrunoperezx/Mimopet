<?php
require_once '../backend/admin_auth.php';
require_once '../backend/conexion.php';

header('Content-Type: application/json');

// Verificar autenticación usando el sistema unificado
requiereAutenticacion();

// Obtener datos del cuerpo de la petición
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if ($input === false || $data === null) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Datos de entrada inválidos'
    ]);
    exit;
}

// Validar datos recibidos
if (empty($data['nombre']) || empty($data['email']) || empty($data['telefono'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Todos los campos son obligatorios'
    ]);
    exit;
}

try {
    $nombre = trim($data['nombre']);
    $email = trim($data['email']);
    $telefono = trim($data['telefono']);
    $direccion = !empty($data['direccion']) ? trim($data['direccion']) : null;
    $id = isset($data['id']) ? (int)$data['id'] : null;

    // Verificar si el email ya existe (excepto para el mismo cliente en caso de edición)
    $queryVerificar = "SELECT id FROM clientes WHERE email = ? AND id != ? AND activo = 1";
    $stmtVerificar = $conn->prepare($queryVerificar);
    $stmtVerificar->execute([$email, $id ?? 0]);
    
    if ($stmtVerificar->fetch()) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Ya existe un cliente con este email'
        ]);
        exit;
    }

    // Iniciar transacción
    $conn->beginTransaction();

    try {
        // Preparar la consulta según sea inserción o actualización
        if ($id) {
            $query = "UPDATE clientes SET nombre = ?, email = ?, telefono = ?, direccion = ? WHERE id = ? AND activo = 1";
            $params = [$nombre, $email, $telefono, $direccion, $id];
        } else {
            $query = "INSERT INTO clientes (nombre, email, telefono, direccion, activo) VALUES (?, ?, ?, ?, 1)";
            $params = [$nombre, $email, $telefono, $direccion];
        }

        // Ejecutar la consulta
        $stmt = $conn->prepare($query);
        $stmt->execute($params);

        if ($stmt->rowCount() === 0 && $id) {
            throw new Exception('Cliente no encontrado o sin cambios');
        }

        // Confirmar transacción
        $conn->commit();

        echo json_encode([
            'success' => true,
            'message' => $id ? 'Cliente actualizado correctamente' : 'Cliente guardado correctamente',
            'id' => $id ?: $conn->lastInsertId()
        ]);

    } catch (Exception $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        throw $e;
    }

} catch (Exception $e) {
    error_log('Error en guardar_cliente.php: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al guardar el cliente'
    ]);
}
?> 