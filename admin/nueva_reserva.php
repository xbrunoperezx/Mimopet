<?php
require_once '../backend/admin_auth.php';
require_once '../backend/conexion.php';

// Verificar autenticación usando el sistema unificado
requiereAutenticacion();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Reserva - MimoPet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #343a40 0%, #23272b 100%);
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
            background: linear-gradient(145deg, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0.05) 100%);
            color: white;
            transform: translateX(5px);
        }
        .main-content {
            padding: 25px;
        }
        .page-header {
            background: white;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
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
        .admin-info {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #6c757d;
            background: linear-gradient(145deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 10px 20px;
            border-radius: 12px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .form-container {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
        }
        .form-section {
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 25px;
            margin-bottom: 25px;
        }
        .form-section:last-child {
            border-bottom: none;
            padding-bottom: 0;
            margin-bottom: 0;
        }
        .form-section h5 {
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .form-control, .form-select {
            border-radius: 10px;
            border: 1px solid #dee2e6;
            padding: 12px;
            transition: all 0.3s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: #4CAF50;
            box-shadow: 0 0 0 0.2rem rgba(76, 175, 80, 0.25);
        }
        .btn-primary {
            background: linear-gradient(145deg, #4CAF50 0%, #45a049 100%);
            border: none;
            padding: 12px 25px;
            border-radius: 12px;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.2);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(76, 175, 80, 0.3);
            background: linear-gradient(145deg, #45a049 0%, #3d8b40 100%);
        }
        .btn-outline-secondary {
            border: 2px solid #6c757d;
            padding: 12px 25px;
            border-radius: 12px;
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .btn-outline-secondary:hover {
            background: linear-gradient(145deg, #6c757d 0%, #5a6268 100%);
            border-color: transparent;
            transform: translateY(-2px);
        }
        .input-group {
            border-radius: 10px;
            overflow: hidden;
        }
        .input-group-text {
            background: linear-gradient(145deg, #f8f9fa 0%, #e9ecef 100%);
            border: 1px solid #dee2e6;
            padding: 12px 15px;
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
                    <a class="nav-link" href="../pages/admin/dashboard.html">
                        <i class="fas fa-tachometer-alt me-2"></i>
                        Dashboard
                    </a>
                    <a class="nav-link" href="calendario.php">
                        <i class="fas fa-calendar-alt me-2"></i>
                        Calendario
                    </a>
                    <a class="nav-link active" href="nueva_reserva.php">
                        <i class="fas fa-plus me-2"></i>
                        Nueva Reserva
                    </a>
                    <a class="nav-link" href="clientes.php">
                        <i class="fas fa-users me-2"></i>
                        Clientes
                    </a>
                    <a class="nav-link" href="servicios.php">
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
                        <i class="fas fa-calendar-plus"></i>
                        Nueva Reserva
                    </h2>
                    <div class="admin-info">
                        <i class="fas fa-user-circle"></i>
                        <span>
                            <?php 
                            $admin = obtenerAdminActual();
                            echo htmlspecialchars($admin['nombre'] ?? 'Administrador');
                            ?>
                        </span>
                    </div>
                </div>

                <div class="form-container">
                    <form id="reservaForm">
                        <!-- Sección de Cliente y Servicio -->
                        <div class="form-section">
                            <h5 class="mb-3">Información Básica</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="servicio" class="form-label">Servicio</label>
                                    <select class="form-select" id="servicio" name="servicio_id" required>
                                        <option value="">Seleccione un servicio</option>
                                        <?php
                                        $stmt = $conn->query("SELECT id, nombre, precio_base FROM servicios WHERE activo = TRUE");
                                        while ($servicio = $stmt->fetch()) {
                                            echo "<option value='{$servicio['id']}' data-precio='{$servicio['precio_base']}'>{$servicio['nombre']} ($" . number_format($servicio['precio_base'], 2) . "/hora)</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="cliente" class="form-label">Cliente</label>
                                    <select class="form-select" id="cliente" name="cliente_id" required>
                                        <option value="">Seleccione un cliente</option>
                                        <?php
                                        $stmt = $conn->query("SELECT id, nombre, email FROM clientes ORDER BY nombre");
                                        while ($cliente = $stmt->fetch()) {
                                            echo "<option value='{$cliente['id']}'>{$cliente['nombre']} ({$cliente['email']})</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="mascota" class="form-label">Mascota</label>
                                    <select class="form-select" id="mascota" name="mascota_id" required disabled>
                                        <option value="">Primero seleccione un cliente</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Sección de Fecha y Hora -->
                        <div class="form-section">
                            <h5 class="mb-3">Fecha y Hora</h5>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="fecha" class="form-label">Fecha</label>
                                    <input type="date" class="form-control" id="fecha" name="fecha" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="hora_inicio" class="form-label">Hora Inicio</label>
                                    <select class="form-select" id="hora_inicio" name="hora_inicio" required>
                                        <option value="">Seleccione hora de inicio</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="hora_fin" class="form-label">Hora Fin</label>
                                    <select class="form-select" id="hora_fin" name="hora_fin" required>
                                        <option value="">Seleccione hora de fin</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Sección de Precio y Notas -->
                        <div class="form-section">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="precio_total" class="form-label">Precio Total</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" id="precio_total" name="precio_total" readonly>
                                    </div>
                                    <small class="text-muted">Calculado automáticamente según duración y servicio</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="notas" class="form-label">Notas</label>
                                    <textarea class="form-control" id="notas" name="notas" rows="3" placeholder="Instrucciones especiales..."></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de acción -->
                        <div class="d-flex justify-content-between">
                            <a href="calendario.php" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>
                                Volver al Calendario
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>
                                Guardar Reserva
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Manejar cambio de cliente
            document.getElementById('cliente').addEventListener('change', function() {
                const clienteId = this.value;
                const mascotaSelect = document.getElementById('mascota');
                
                if (clienteId) {
                    fetch(`obtener_mascotas.php?cliente_id=${clienteId}`)
                        .then(response => response.json())
                        .then(mascotas => {
                            mascotaSelect.innerHTML = '<option value="">Seleccione una mascota</option>';
                            if (mascotas && mascotas.length > 0) {
                                mascotas.forEach(mascota => {
                                    const option = document.createElement('option');
                                    option.value = mascota.id;
                                    option.textContent = `${mascota.nombre} (${mascota.especie})`;
                                    mascotaSelect.appendChild(option);
                                });
                                mascotaSelect.disabled = false;
                            } else {
                                mascotaSelect.innerHTML = '<option value="">No hay mascotas registradas para este cliente</option>';
                                mascotaSelect.disabled = true;
                            }
                        })
                        .catch(error => {
                            console.error('Error al cargar mascotas:', error);
                            mascotaSelect.innerHTML = '<option value="">Error al cargar mascotas</option>';
                            mascotaSelect.disabled = true;
                        });
                } else {
                    mascotaSelect.innerHTML = '<option value="">Primero seleccione un cliente</option>';
                    mascotaSelect.disabled = true;
                }
            });

            // Función para generar las horas disponibles
            function generarHoras() {
                const horaInicio = document.getElementById('hora_inicio');
                const horaFin = document.getElementById('hora_fin');
                
                // Limpiar las opciones existentes
                horaInicio.innerHTML = '<option value="">Seleccione hora de inicio</option>';
                horaFin.innerHTML = '<option value="">Seleccione hora de fin</option>';
                
                // Generar horas desde las 8:00 hasta las 20:00
                for (let hora = 8; hora <= 20; hora++) {
                    const horaFormateada = hora.toString().padStart(2, '0') + ':00';
                    
                    // Añadir opción a hora inicio
                    const optionInicio = document.createElement('option');
                    optionInicio.value = horaFormateada;
                    optionInicio.textContent = horaFormateada;
                    horaInicio.appendChild(optionInicio);
                    
                    // Añadir opción a hora fin
                    const optionFin = document.createElement('option');
                    optionFin.value = horaFormateada;
                    optionFin.textContent = horaFormateada;
                    horaFin.appendChild(optionFin);
                }
            }

            // Actualizar hora fin cuando cambie hora inicio
            document.getElementById('hora_inicio').addEventListener('change', function() {
                const horaInicio = this.value;
                const horaFin = document.getElementById('hora_fin');
                const [horaInicioNum] = horaInicio.split(':').map(Number);
                
                // Limpiar y actualizar opciones de hora fin
                horaFin.innerHTML = '<option value="">Seleccione hora de fin</option>';
                
                // Generar horas desde una hora después de la hora de inicio hasta las 20:00
                for (let hora = horaInicioNum + 1; hora <= 20; hora++) {
                    const horaFormateada = hora.toString().padStart(2, '0') + ':00';
                    const option = document.createElement('option');
                    option.value = horaFormateada;
                    option.textContent = horaFormateada;
                    horaFin.appendChild(option);
                }
                
                // Habilitar el select de hora fin
                horaFin.disabled = false;
            });

            // Generar las horas al cargar la página
            generarHoras();

            // Calcular precio total
            function calcularPrecioTotal() {
                const servicio = document.getElementById('servicio');
                const horaInicio = document.getElementById('hora_inicio').value;
                const horaFin = document.getElementById('hora_fin').value;
                
                if (servicio.value && horaInicio && horaFin) {
                    const precioBase = parseFloat(servicio.options[servicio.selectedIndex].dataset.precio);
                    const inicio = new Date(`2000-01-01T${horaInicio}`);
                    const fin = new Date(`2000-01-01T${horaFin}`);
                    const duracionHoras = (fin - inicio) / (1000 * 60 * 60);
                    
                    if (duracionHoras > 0) {
                        document.getElementById('precio_total').value = (precioBase * duracionHoras).toFixed(2);
                    } else {
                        document.getElementById('precio_total').value = '0.00';
                    }
                }
            }

            ['servicio', 'hora_inicio', 'hora_fin'].forEach(id => {
                document.getElementById(id).addEventListener('change', calcularPrecioTotal);
            });

            // Establecer fecha mínima como hoy
            const fechaInput = document.getElementById('fecha');
            const hoy = new Date().toISOString().split('T')[0];
            fechaInput.min = hoy;
            fechaInput.value = hoy;

            // Manejar envío del formulario
            document.getElementById('reservaForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                
                fetch('guardar_reserva.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            text: 'Reserva creada con éxito',
                            confirmButtonText: 'Aceptar'
                        }).then(() => {
                            window.location.href = 'calendario.php';
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error al crear la reserva: ' + data.message,
                            confirmButtonText: 'Aceptar'
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error de conexión: ' + error.message,
                        confirmButtonText: 'Aceptar'
                    });
                });
            });
        });

        // Cerrar sesión con sistema unificado
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
                        window.location.href = '../pages/admin/index.html';
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