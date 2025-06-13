<?php
require_once '../backend/admin_auth.php';
require_once '../backend/conexion.php';

// Verificar autenticación usando el sistema unificado
requiereAutenticacion();

// Actualizar precios si es necesario
$precios_actualizados = [
    'Peluquería' => 25,
    'Cuidado a Domicilio' => 8,
    'Guardería' => 5
];

foreach ($precios_actualizados as $servicio => $precio) {
    $stmt = $conn->prepare("UPDATE servicios SET precio_base = ? WHERE nombre = ? AND precio_base != ?");
    $stmt->execute([$precio, $servicio, $precio]);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Servicios - MimoPet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Poppins', sans-serif;
        }
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(145deg, #343a40 0%, #212529 100%);
            padding: 20px;
            color: white;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,.8);
            padding: 12px 15px;
            margin: 8px 0;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: rgba(255,255,255,.15);
            color: white;
            transform: translateX(5px);
        }
        .main-content {
            padding: 30px;
        }
        .service-card {
            transition: all 0.3s ease;
            border-radius: 15px;
            overflow: hidden;
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            background: white;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .service-card .card-body {
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .service-card .card-title {
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 1rem;
            font-size: 1.25rem;
        }
        .service-card .card-text {
            color: #666;
            font-size: 0.95rem;
            line-height: 1.5;
            margin-bottom: 1.5rem;
            flex-grow: 1;
        }
        .service-card .card-actions {
            border-top: 1px solid #eee;
            padding-top: 1.5rem;
            margin-top: auto;
        }
        .price-tag {
            background: linear-gradient(145deg, #4CAF50 0%, #45a049 100%);
            color: white;
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: 500;
            box-shadow: 0 2px 10px rgba(76, 175, 80, 0.2);
            display: inline-block;
            margin-bottom: 1rem;
        }
        .btn-action {
            border-radius: 10px;
            padding: 8px 20px;
            transition: all 0.3s ease;
            font-weight: 500;
            width: 100%;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .btn-action i {
            margin-right: 8px;
        }
        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .btn-action.btn-outline-success {
            border-color: #4CAF50;
            color: #4CAF50;
        }
        .btn-action.btn-outline-success:hover {
            background: linear-gradient(145deg, #4CAF50 0%, #45a049 100%);
            color: white;
            border-color: transparent;
        }
        .btn-action.btn-outline-danger {
            border-color: #dc3545;
            color: #dc3545;
        }
        .btn-action.btn-outline-danger:hover {
            background: linear-gradient(145deg, #dc3545 0%, #c82333 100%);
            color: white;
            border-color: transparent;
        }
        .modal-content {
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .modal-header {
            background: linear-gradient(145deg, #343a40 0%, #212529 100%);
            color: white;
            border-radius: 15px 15px 0 0;
            padding: 20px;
        }
        .modal-body {
            padding: 25px;
        }
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 1px solid #dee2e6;
            font-size: 0.95rem;
        }
        .form-control:focus {
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.25);
            border-color: #4CAF50;
        }
        .input-group-text {
            border-radius: 10px 0 0 10px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
        }
        .btn-new-service {
            background: linear-gradient(145deg, #4CAF50 0%, #45a049 100%);
            border: none;
            color: white;
            padding: 12px 25px;
            border-radius: 12px;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.2);
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.95rem;
        }
        .btn-new-service:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(76, 175, 80, 0.3);
            color: white;
            background: linear-gradient(145deg, #45a049 0%, #3d8b40 100%);
        }
        .btn-new-service i {
            font-size: 1.1rem;
        }
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding: 1rem;
            background: white;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .page-title {
            font-size: 1.75rem;
            font-weight: 600;
            color: #2c3e50;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .page-title i {
            color: #4CAF50;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Menú lateral -->
            <div class="col-md-2 sidebar">
                <h4 class="mb-4">
                    <i class="fas fa-paw me-2"></i>
                    MimoPet
                </h4>
                <nav class="nav flex-column">
                    <a class="nav-link" href="calendario.php">
                        <i class="fas fa-calendar-alt me-2"></i>
                        Calendario
                    </a>
                    <a class="nav-link" href="nueva_reserva.php">
                        <i class="fas fa-plus me-2"></i>
                        Nueva Reserva
                    </a>
                    <a class="nav-link" href="clientes.php">
                        <i class="fas fa-users me-2"></i>
                        Clientes
                    </a>
                    <a class="nav-link active" href="servicios.php">
                        <i class="fas fa-concierge-bell me-2"></i>
                        Servicios
                    </a>
                    <a class="nav-link" href="#" id="btnLogout">
                        <i class="fas fa-sign-out-alt me-2"></i>
                        Cerrar Sesión
                    </a>
                </nav>
            </div>

            <!-- Contenido principal -->
            <div class="col-md-10 main-content">
                <div class="page-header">
                    <h2 class="page-title">
                        <i class="fas fa-concierge-bell"></i>
                        Gestión de Servicios
                    </h2>
                    <button class="btn btn-new-service" data-bs-toggle="modal" data-bs-target="#nuevoServicioModal">
                        <i class="fas fa-plus-circle"></i>
                        Nuevo Servicio
                    </button>
                </div>

                <!-- Servicios en cards -->
                <div class="row">
                    <?php
                    $stmt = $conn->query("SELECT * FROM servicios");
                    while ($servicio = $stmt->fetch()) {
                        echo '
                        <div class="col-md-4 mb-4">
                            <div class="card service-card">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fas fa-concierge-bell me-2 text-success"></i>
                                        ' . htmlspecialchars($servicio['nombre']) . '
                                    </h5>
                                    <p class="card-text">' . htmlspecialchars($servicio['descripcion']) . '</p>
                                    <div class="card-actions">
                                        <div class="text-center mb-3">
                                            <span class="price-tag">
                                                <i class="fas fa-tag me-2"></i>
                                                $' . number_format($servicio['precio_base'], 2) . '/hora
                                            </span>
                                        </div>
                                        <button class="btn btn-outline-success btn-action" 
                                                onclick="editarPrecio(' . $servicio['id'] . ', \'' . 
                                                htmlspecialchars($servicio['nombre']) . '\', ' . 
                                                $servicio['precio_base'] . ')">
                                            <i class="fas fa-edit"></i>
                                            Editar Precio
                                        </button>
                                        <button class="btn btn-outline-danger btn-action" 
                                                onclick="eliminarServicio(' . $servicio['id'] . ', \'' . 
                                                htmlspecialchars($servicio['nombre']) . '\')">
                                            <i class="fas fa-trash"></i>
                                            Eliminar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Nuevo Servicio -->
    <div class="modal fade" id="nuevoServicioModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-plus-circle me-2"></i>
                        Nuevo Servicio
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="nuevoServicioForm">
                        <div class="mb-4">
                            <label class="form-label">Nombre del Servicio</label>
                            <input type="text" class="form-control" name="nombre" required 
                                   placeholder="Ej: Peluquería Canina">
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Descripción</label>
                            <textarea class="form-control" name="descripcion" rows="3" required
                                      placeholder="Describe los detalles del servicio..."></textarea>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Precio por Hora</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" name="precio" 
                                       step="0.01" required min="0" placeholder="0.00">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-action" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success btn-action" onclick="guardarServicio()">
                        <i class="fas fa-save me-2"></i>
                        Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Editar Precio -->
    <div class="modal fade" id="editarPrecioModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-edit me-2"></i>
                        Editar Precio
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editarPrecioForm">
                        <input type="hidden" id="servicioId" name="id">
                        <div class="mb-4">
                            <h5 id="nombreServicio" class="text-center mb-4"></h5>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Nuevo Precio por Hora</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="nuevoPrecio" 
                                       name="precio" step="0.01" required min="0">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-action" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success btn-action" onclick="actualizarPrecio()">
                        <i class="fas fa-save me-2"></i>
                        Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Variables globales
        const nuevoServicioModal = new bootstrap.Modal(document.getElementById('nuevoServicioModal'));
        const editarPrecioModal = new bootstrap.Modal(document.getElementById('editarPrecioModal'));

        // Función para guardar nuevo servicio
        async function guardarServicio() {
            const formData = new FormData(document.getElementById('nuevoServicioForm'));
            
            try {
                const response = await fetch('../backend/admin/guardar_servicio.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    nuevoServicioModal.hide();
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'Error al guardar el servicio'
                });
            }
        }

        // Función para mostrar modal de edición de precio
        function editarPrecio(id, nombre, precio) {
            document.getElementById('servicioId').value = id;
            document.getElementById('nombreServicio').textContent = nombre;
            document.getElementById('nuevoPrecio').value = precio;
            editarPrecioModal.show();
        }

        // Función para actualizar precio
        async function actualizarPrecio() {
            const formData = new FormData(document.getElementById('editarPrecioForm'));
            
            try {
                const response = await fetch('../backend/admin/actualizar_precio.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    editarPrecioModal.hide();
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'Error al actualizar el precio'
                });
            }
        }

        // Función para eliminar servicio
        async function eliminarServicio(id, nombre) {
            const result = await Swal.fire({
                title: '¿Eliminar servicio?',
                text: `¿Estás seguro de que deseas eliminar el servicio "${nombre}"? Esta acción no se puede deshacer.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            });

            if (result.isConfirmed) {
                try {
                    const response = await fetch('../backend/admin/eliminar_servicio.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ id })
                    });

                    const data = await response.json();

                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        throw new Error(data.message);
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message || 'Error al eliminar el servicio'
                    });
                }
            }
        }

        // Cerrar sesión
        document.getElementById('btnLogout').addEventListener('click', async function(e) {
            e.preventDefault();
            
            const result = await Swal.fire({
                title: '¿Cerrar sesión?',
                text: '¿Estás seguro de que deseas cerrar sesión?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, cerrar sesión',
                cancelButtonText: 'Cancelar'
            });

            if (result.isConfirmed) {
                try {
                    const response = await fetch('../backend/admin/logout_unificado.php');
                    const data = await response.json();
                    
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Sesión cerrada',
                            text: 'Redirigiendo al login...',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        
                        setTimeout(() => {
                            window.location.href = '../pages/admin/index.html';
                        }, 1500);
                    }
                } catch (error) {
                    console.error('Error al cerrar sesión:', error);
                    window.location.href = '../pages/admin/index.html';
                }
            }
        });
    </script>
</body>
</html> 