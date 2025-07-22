<?php
/**
 * Sistema Unificado de Autenticación Administrativa - MimoPet
 * Centraliza toda la lógica de autenticación para el panel administrativo
 */

// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/conexion.php';

/**
 * Verifica si el administrador está autenticado
 * @return bool
 */
function estaAutenticado() {
    return isset($_SESSION['admin_id']) && 
           isset($_SESSION['admin_email']) && 
           !empty($_SESSION['admin_id']);
}

/**
 * Redirige al login si no está autenticado
 * @param string $loginUrl URL del login
 */
function requiereAutenticacion($loginUrl = null) {
    if (!estaAutenticado()) {
        if ($loginUrl === null) {
            // Detectar la ruta correcta según la ubicación del archivo
            $currentPath = $_SERVER['REQUEST_URI'];
            if (strpos($currentPath, '/pages/admin/') !== false) {
                $loginUrl = 'index.html';
            } else {
                $loginUrl = '../pages/admin/index.html';
            }
        }
        header('Location: ' . $loginUrl);
        exit;
    }
}

/**
 * Procesa el login del administrador
 * @param string $email
 * @param string $password
 * @return array Resultado del login
 */
function procesarLogin($email, $password) {
    global $conn;
    
    try {
        // Verificar conexión a la base de datos
        $conn->query("SELECT 1");
        
        // Validar campos
        if (empty($email) || empty($password)) {
            return [
                'success' => false, 
                'message' => 'Email y contraseña son requeridos'
            ];
        }
        
        // Buscar administrador en la tabla administradores
        $sql = "SELECT id, email, password, nombre FROM administradores WHERE email = ? AND activo = 1";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$email]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$admin) {
            error_log('Login fallido - Usuario no encontrado: ' . $email);
            return [
                'success' => false, 
                'message' => 'Credenciales inválidas'
            ];
        }
        
        // Verificar contraseña
        if (!password_verify($password, $admin['password'])) {
            error_log('Login fallido - Contraseña incorrecta para: ' . $email);
            return [
                'success' => false, 
                'message' => 'Credenciales inválidas'
            ];
        }
        
        // Establecer sesión unificada
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_email'] = $admin['email'];
        $_SESSION['admin_nombre'] = $admin['nombre'];
        $_SESSION['admin_login_time'] = time();
        
        error_log('Login exitoso para administrador: ' . $email);
        
        return [
            'success' => true,
            'message' => 'Inicio de sesión exitoso',
            'admin' => [
                'id' => $admin['id'],
                'email' => $admin['email'],
                'nombre' => $admin['nombre']
            ]
        ];
        
    } catch (Exception $e) {
        error_log('Error en procesarLogin: ' . $e->getMessage());
        return [
            'success' => false, 
            'message' => 'Error del servidor'
        ];
    }
}

/**
 * Cierra la sesión del administrador
 */
function cerrarSesionAdmin() {
    // Limpiar variables de sesión específicas
    unset($_SESSION['admin_id']);
    unset($_SESSION['admin_email']);
    unset($_SESSION['admin_nombre']);
    unset($_SESSION['admin_login_time']);
    
    // Si no hay otras variables de sesión, destruir completamente
    if (empty($_SESSION)) {
        session_destroy();
    }
}

/**
 * Obtiene los datos del administrador actual
 * @return array|null
 */
function obtenerAdminActual() {
    if (!estaAutenticado()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['admin_id'],
        'email' => $_SESSION['admin_email'],
        'nombre' => $_SESSION['admin_nombre'],
        'login_time' => $_SESSION['admin_login_time'] ?? null
    ];
}

/**
 * Verifica si la sesión ha expirado (opcional - 8 horas)
 * @return bool
 */
function sesionExpirada() {
    if (!isset($_SESSION['admin_login_time'])) {
        return false;
    }
    
    $tiempoExpiracion = 8 * 60 * 60; // 8 horas
    return (time() - $_SESSION['admin_login_time']) > $tiempoExpiracion;
}

/**
 * Regenera la sesión por seguridad
 */
function regenerarSesion() {
    session_regenerate_id(true);
}
?>