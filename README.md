# MimoPet - Sistema de Gestión de servicios para Mascotas (peluqueria, guarderia, cuidado domicilio)

## Descripción
MimoPet es un sistema de gestión integral para  mascotas que permite administrar citas, clientes y sus mascotas de manera eficiente. El sistema cuenta con una interfaz moderna y fácil de usar tanto para administradores como para clientes.

## Características Principales

### Gestión de Clientes
- Registro y administración de clientes
- Sistema de desactivación de clientes (soft delete)
- Gestión de múltiples mascotas por cliente
- Búsqueda y filtrado de clientes

### Sistema de Citas
- Calendario interactivo para visualización de citas
- Gestión de estados de citas (pendiente, completada, cancelada)
- Verificación automática de disponibilidad
- Notificaciones y confirmaciones mediante SweetAlert2

### Panel de Administración
- Sistema de autenticación seguro
- Gestión de servicios y precios
- Vista de calendario con detalles de citas
- Interfaz intuitiva para gestión de clientes y mascotas

## Tecnologías Utilizadas
- PHP 7.4+
- MySQL 5.7+
- HTML5
- CSS3
- JavaScript
- Bootstrap 5
- jQuery
- FullCalendar.js
- SweetAlert2
- Font Awesome

## Requisitos del Sistema
- Servidor web (Apache/Nginx)
- PHP 7.4 o superior
- MySQL 5.7 o superior
- Extensiones PHP:
  - PDO
  - PDO_MySQL
  - JSON
  - Session

## Guía de Instalación

### 1. Preparación del Entorno
1. Instalar XAMPP, WAMP o similar que incluya Apache, PHP y MySQL
2. Asegurarse que el servidor web y MySQL estén funcionando

### 2. Configuración de la Base de Datos
1. Crear una nueva base de datos en MySQL
2. Importar el archivo `sql_scripts/mimopet2.sql`
3. Configurar las credenciales de la base de datos en `backend/conexion.php`

### 3. Despliegue del Proyecto
1. Clonar o descargar el repositorio
2. Colocar los archivos en el directorio web (htdocs para XAMPP)
3. Configurar los permisos de directorios si es necesario
4. Acceder a través del navegador: `http://localhost/mitienda`

### 4. Configuración Inicial
1. Acceder al panel de administración: `http://localhost/mitienda/admin`
2. Credenciales por defecto:
   - Usuario: admin@mimopet.com
   - Contraseña: admin123
3. ¡Importante! Cambiar la contraseña después del primer inicio de sesión

## Estructura del Proyecto
```
mitienda/
├── admin/              # Panel de administración
├── assets/            # Recursos estáticos (CSS, JS, imágenes)
├── backend/           # Lógica de negocio y conexión BD
├── pages/             # Páginas públicas
└── index.html         # Página principal
```

## Cambios Recientes
- Unificación del sistema de autenticación
- Mejora en la gestión de estados de citas
- Implementación de soft delete para clientes
- Optimización del calendario de citas
- Mejora en la interfaz de usuario
- Sistema de notificaciones mejorado
- Correcciones de seguridad y rendimiento

## Seguridad
- Sistema de autenticación centralizado
- Protección contra SQL Injection
- Validación de datos en frontend y backend
- Manejo seguro de sesiones
- Sanitización de inputs

## Soporte
Para reportar problemas o solicitar soporte, por favor crear un issue en el repositorio o contactar al equipo de desarrollo.

## Licencia
Este proyecto está bajo la licencia MIT. Ver el archivo LICENSE para más detalles.