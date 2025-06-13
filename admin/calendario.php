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
    <title>Panel Administrativo - MimoPet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
            padding: 20px;
            color: white;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,.8);
            padding: 10px 15px;
            margin: 5px 0;
            border-radius: 5px;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: rgba(255,255,255,.1);
            color: white;
        }
        .main-content {
            padding: 20px;
        }
        .header {
            background: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .admin-info {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #6c757d;
        }
        #calendario {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
            height: calc(100vh - 200px);
        }
        .btn-accion {
            margin-right: 10px;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8em;
            font-weight: 500;
        }
        .status-pendiente { background-color: #ffc107; color: #000; }
        .status-confirmada { background-color: #28a745; color: #fff; }
        .status-completada { background-color: #17a2b8; color: #fff; }
        .status-cancelada { background-color: #dc3545; color: #fff; }
        
        /* Estilos para los eventos del calendario */
        .fc-event {
            border: none !important;
            border-radius: 4px !important;
            padding: 6px 8px !important;
            margin: 2px !important;
            cursor: pointer;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1) !important;
        }

        .fc-event-title {
            font-weight: 600 !important;
            font-size: 0.95em !important;
            padding: 2px 0 !important;
            color: #fff !important;
        }

        .fc-event-time {
            font-weight: bold !important;
            font-size: 0.9em !important;
            color: #fff !important;
        }

        /* Estilos según el estado de la cita */
        .evento-pendiente {
            background-color: #ff9800 !important;
            border-left: 4px solid #e65100 !important;
            color: #fff !important;
        }

        .evento-confirmada {
            background-color: #2196F3 !important;
            border-left: 4px solid #1565C0 !important;
            color: #fff !important;
        }

        .evento-completada {
            background-color: #4CAF50 !important;
            border-left: 4px solid #2E7D32 !important;
            color: #fff !important;
        }

        .evento-cancelada {
            background-color: #f44336 !important;
            border-left: 4px solid #c62828 !important;
            color: #fff !important;
            text-decoration: line-through !important;
            opacity: 0.85 !important;
        }

        /* Estilos para la vista de día y semana */
        .fc-timegrid-event {
            min-height: 45px !important;
        }

        .fc-timegrid-event .fc-event-main {
            padding: 6px 8px !important;
        }

        .fc-timegrid-event .fc-event-title-container {
            padding: 2px 0 !important;
        }

        /* Estilos para la vista de mes */
        .fc-daygrid-event {
            white-space: normal !important;
            align-items: center !important;
            margin: 2px 4px !important;
        }

        /* Mejoras en la cabecera del calendario */
        .fc-header-toolbar {
            background: #f8f9fa !important;
            padding: 15px !important;
            border-radius: 8px !important;
            margin-bottom: 15px !important;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1) !important;
        }

        .fc-button-primary {
            background-color: #1976D2 !important;
            border-color: #1976D2 !important;
            font-weight: 500 !important;
        }

        .fc-button-primary:hover {
            background-color: #1565C0 !important;
            border-color: #1565C0 !important;
        }

        /* Mejoras en las celdas del calendario */
        .fc-timegrid-slot {
            height: 50px !important;
        }

        .fc-timegrid-slot-label {
            font-size: 0.9em !important;
            color: #333 !important;
            font-weight: 500 !important;
        }

        .fc-day-today {
            background-color: #f3f4f6 !important;
        }

        /* Estilos para la información adicional en los eventos */
        .evento-info {
            color: #fff !important;
            font-size: 0.9em !important;
            opacity: 0.95 !important;
            margin-top: 2px !important;
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
                        Panel de mando
                    </a>
                    <a class="nav-link active" href="calendario.php">
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
                <div class="header">
                    <div>
                        <h2>Calendario de Citas</h2>
                    </div>
                    <div class="admin-info">
                        <i class="fas fa-user-circle"></i>
                        <span id="adminName">
                            <?php 
                            $admin = obtenerAdminActual();
                            echo htmlspecialchars($admin['nombre'] ?? 'Administrador');
                            ?>
                        </span>
                    </div>
                </div>
                
                <div class="d-flex justify-content-end mb-3">
                    <a href="nueva_reserva.php" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        Nueva Reserva
                    </a>
                </div>
                
                <div id="calendario"></div>
            </div>
        </div>
    </div>

    <!-- Modal para detalles de reserva -->
    <div class="modal fade" id="reservaModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalles de la Reserva</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <h6>Cliente y Mascota</h6>
                        <p id="modalCliente" class="mb-1"></p>
                        <p id="modalMascota" class="text-muted mb-0"></p>
                    </div>
                    <div class="mb-3">
                        <h6>Estado</h6>
                        <span id="modalEstado" class="status-badge"></span>
                    </div>
                    <div class="mb-3">
                        <h6>Servicio</h6>
                        <p id="modalServicio" class="mb-1"></p>
                        <p id="modalPrecio" class="text-primary fw-bold"></p>
                    </div>
                    <div class="mb-3">
                        <h6>Horario</h6>
                        <p id="modalHorario"></p>
                    </div>
                    <div>
                        <h6>Notas</h6>
                        <p id="modalNotas" class="text-muted"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger btn-accion" id="btnCancelar">
                        <i class="fas fa-times me-1"></i> Cancelar
                    </button>
                    <button type="button" class="btn btn-outline-success btn-accion" id="btnCompletar">
                        <i class="fas fa-check me-1"></i> Completar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales-all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let calendar; // Declarar calendar como variable global

        document.addEventListener('DOMContentLoaded', function() {
            calendar = new FullCalendar.Calendar(document.getElementById('calendario'), {
                initialView: 'timeGridWeek',
                locale: 'es',
                timeZone: 'Europe/Madrid',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                slotMinTime: '08:00:00',
                slotMaxTime: '20:00:00',
                slotDuration: '01:00:00',
                events: {
                    url: 'obtener_reservas.php',
                    failure: function() {
                        console.error('Error al cargar los eventos');
                    }
                },
                eventClick: mostrarDetallesReserva,
                height: '100%',
                dayMaxEvents: true,
                nowIndicator: true,
                eventTimeFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false,
                    meridiem: false
                },
                eventContent: function(arg) {
                    return {
                        html: `
                            <div class="fc-event-main-frame">
                                <div class="fc-event-time" style="color: #fff; font-weight: bold;">
                                    ${arg.timeText}
                                </div>
                                <div class="fc-event-title-container">
                                    <div class="fc-event-title" style="font-size: 1em; font-weight: bold; color: #fff;">
                                        ${arg.event.title.split(' - ')[0]}
                                    </div>
                                    <div class="evento-info">
                                        ${arg.event.title.split(' - ')[1]} • ${arg.event.title.split('(')[1].replace(')', '')}
                                    </div>
                                </div>
                            </div>
                        `
                    };
                },
                eventDidMount: function(info) {
                    // Aplicar clase según el estado
                    if (info.event.extendedProps.status) {
                        info.el.classList.add('evento-' + info.event.extendedProps.status);
                    }
                }
            });
            calendar.render();
        });

        function mostrarDetallesReserva(info) {
            const evento = info.event;
            const reservaId = evento.id;
            const estadoActual = evento.extendedProps.status;

            document.getElementById('modalCliente').textContent = evento.title.split(' - ')[0];
            document.getElementById('modalMascota').textContent = evento.title.split(' - ')[1];
            document.getElementById('modalServicio').textContent = evento.title.split('(')[1].replace(')', '');
            document.getElementById('modalHorario').textContent = `${evento.start.toLocaleTimeString()} - ${evento.end.toLocaleTimeString()}`;
            document.getElementById('modalPrecio').textContent = `$${evento.extendedProps.price}`;
            document.getElementById('modalNotas').textContent = evento.extendedProps.description || 'Sin notas';
            
            const statusBadge = document.getElementById('modalEstado');
            statusBadge.textContent = estadoActual.toUpperCase();
            statusBadge.className = `status-badge status-${estadoActual}`;

            // Configurar botones según el estado
            const btnCancelar = document.getElementById('btnCancelar');
            const btnCompletar = document.getElementById('btnCompletar');

            // Habilitar/deshabilitar botones según el estado
            btnCancelar.disabled = estadoActual === 'cancelada' || estadoActual === 'completada';
            btnCompletar.disabled = estadoActual === 'cancelada' || estadoActual === 'completada';

            // Configurar eventos de los botones
            btnCancelar.onclick = async () => {
                const result = await Swal.fire({
                    title: '¿Cancelar reserva?',
                    text: '¿Estás seguro de que deseas cancelar esta reserva?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonText: 'No',
                    confirmButtonText: 'Sí, cancelar'
                });

                if (result.isConfirmed) {
                    await actualizarEstadoReserva(reservaId, 'cancelada');
                }
            };

            btnCompletar.onclick = async () => {
                const result = await Swal.fire({
                    title: '¿Completar reserva?',
                    text: '¿Confirmas que el servicio ha sido completado?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonText: 'No',
                    confirmButtonText: 'Sí, completar'
                });

                if (result.isConfirmed) {
                    await actualizarEstadoReserva(reservaId, 'completada');
                }
            };

            const modal = new bootstrap.Modal(document.getElementById('reservaModal'));
            modal.show();
        }

        async function actualizarEstadoReserva(id, estado) {
            try {
                const response = await fetch('actualizar_estado_reserva.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ id, estado })
                });

                const resultado = await response.json();
                
                if (!resultado.success) {
                    throw new Error(resultado.message);
                }

                await Swal.fire({
                    icon: 'success',
                    title: 'Estado actualizado',
                    text: resultado.message,
                    timer: 1500,
                    showConfirmButton: false
                });

                // Cerrar el modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('reservaModal'));
                modal.hide();

                // Recargar el calendario
                calendar.refetchEvents();

            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'Error al actualizar el estado'
                });
            }
        }

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