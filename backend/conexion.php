<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $host = 'ikw4cos008ksg4w4g04cso4k';
    $dbname = 'mimopet';
    $username = 'bperez';
    $password = 'Arriondas1996';
    
    // Intentar crear la conexión con opciones específicas
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
    ];
    
    $conn = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        $options
    );
    
    // Verificar la conexión
    $testQuery = $conn->query("SELECT 1");
    if ($testQuery) {
        error_log("Conexión exitosa a la base de datos mimopet");
    } else {
        throw new PDOException("La prueba de conexión falló");
    }

} catch (PDOException $e) {
    error_log("Error de conexión a la base de datos: " . $e->getMessage());
    error_log("Código de error: " . $e->getCode());
    
    // Verificar si el error es de autenticación
    if ($e->getCode() == 1045) {
        error_log("Error de autenticación: Verifica el usuario y contraseña");
    }
    // Verificar si la base de datos no existe
    else if ($e->getCode() == 1049) {
        error_log("Error: La base de datos 'mimopet' no existe");
    }
    
    throw new PDOException("Error de conexión a la base de datos. Por favor, verifica la configuración.");
}
?> 