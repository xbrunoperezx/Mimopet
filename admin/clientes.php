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
    <title>Gestión de Clientes - MimoPet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
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
        .card {
            transition: all 0.3s ease;
            border-radius: 15px;
            overflow: hidden;
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            background: white;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .card-body {
            padding: 1.5rem;
        }
        .table th { 
            background: #f8f9fa;
            color: #2c3e50;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
        }
        .table td {
            vertical-align: middle;
            color: #666;
        }
        .btn {
            border-radius: 10px;
            padding: 8px 20px;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        .btn-primary {
            background: linear-gradient(145deg, #4CAF50 0%, #45a049 100%);
            border: none;
            color: white;
            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.2);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(76, 175, 80, 0.3);
            background: linear-gradient(145deg, #45a049 0%, #3d8b40 100%);
        }
        .btn-action {
            width: 35px;
            height: 35px;
            padding: 0;
            line-height: 35px;
            text-align: center;
            margin: 0 3px;
            border-radius: 10px;
            transition: all 0.3s ease;
            border: none;
            color: white;
        }
        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }
        .btn-action.btn-edit {
            background: linear-gradient(145deg, #2196F3 0%, #1976D2 100%);
        }
        .btn-action.btn-edit:hover {
            background: linear-gradient(145deg, #1976D2 0%, #1565C0 100%);
            box-shadow: 0 4px 10px rgba(33, 150, 243, 0.3);
        }
        .btn-action.btn-pets {
            background: linear-gradient(145deg, #4CAF50 0%, #45a049 100%);
        }
        .btn-action.btn-pets:hover {
            background: linear-gradient(145deg, #45a049 0%, #388E3C 100%);
            box-shadow: 0 4px 10px rgba(76, 175, 80, 0.3);
        }
        .btn-action.btn-toggle-active {
            background: linear-gradient(145deg, #dc3545 0%, #c82333 100%);
        }
        .btn-action.btn-toggle-active:hover {
            background: linear-gradient(145deg, #c82333 0%, #bd2130 100%);
            box-shadow: 0 4px 10px rgba(220, 53, 69, 0.3);
        }
        .btn-action.btn-toggle-inactive {
            background: linear-gradient(145deg, #28a745 0%, #218838 100%);
        }
        .btn-action.btn-toggle-inactive:hover {
            background: linear-gradient(145deg, #218838 0%, #1e7e34 100%);
            box-shadow: 0 4px 10px rgba(40, 167, 69, 0.3);
        }
        .input-group {
            border-radius: 10px;
            overflow: hidden;
        }
        .input-group-text {
            border-radius: 10px 0 0 10px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
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
        .modal-footer {
            border-top: 1px solid #eee;
            padding: 20px;
        }
        .form-check-input:checked {
            background-color: #4CAF50;
            border-color: #4CAF50;
        }
        .mascota-item {
            background: white;
            padding: 20px;
            margin: 15px 0;
            border-radius: 15px;
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        .mascota-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
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
                    <a class="nav-link active" href="clientes.php">
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
                <!-- Encabezado de página -->
                <div class="page-header">
                    <h2 class="page-title">
                        <i class="fas fa-users"></i>
                        Gestión de Clientes
                    </h2>
                    <button class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#clienteModal">
                        <i class="fas fa-plus"></i>
                        Nuevo Cliente
                    </button>
                </div>

                <!-- Buscador -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text" class="form-control" id="buscarCliente" 
                                           placeholder="Buscar por nombre, email o teléfono...">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="mostrarInactivos">
                                    <label class="form-check-label" for="mostrarInactivos">
                                        <i class="fas fa-user-clock me-2"></i>
                                        Mostrar clientes inactivos
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla de clientes -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Email</th>
                                        <th>Teléfono</th>
                                        <th>Dirección</th>
                                        <th>Mascotas</th>
                                        <th>Registro</th>
                                        <th class="text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaClientes">
                                    <!-- Los clientes se cargarán dinámicamente -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Cliente -->
    <div class="modal fade" id="clienteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-user-plus me-2"></i>
                        <span id="modalTitle">Nuevo Cliente</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formCliente">
                        <input type="hidden" id="clienteId">
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-user me-2"></i>
                                Nombre completo
                            </label>
                            <input type="text" class="form-control" id="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-envelope me-2"></i>
                                Email
                            </label>
                            <input type="email" class="form-control" id="email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-phone me-2"></i>
                                Teléfono
                            </label>
                            <input type="tel" class="form-control" id="telefono" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-map-marker-alt me-2"></i>
                                Dirección
                            </label>
                            <input type="text" class="form-control" id="direccion">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>
                        Cancelar
                    </button>
                    <button type="button" class="btn btn-primary" id="guardarCliente">
                        <i class="fas fa-save me-2"></i>
                        Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Mascotas -->
    <div class="modal fade" id="mascotasModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-paw me-2"></i>
                        Mascotas del Cliente
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="listaMascotas" class="mb-4">
                        <!-- Lista de mascotas -->
                    </div>
                    <form id="formMascota" class="border-top pt-4">
                        <h6 class="d-flex align-items-center gap-2 mb-3">
                            <i class="fas fa-plus-circle"></i>
                            Agregar Nueva Mascota
                        </h6>
                        <input type="hidden" id="mascotaClienteId">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-tag me-2"></i>
                                    Nombre
                                </label>
                                <input type="text" class="form-control" id="mascotaNombre" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-paw me-2"></i>
                                    Especie
                                </label>
                                <select class="form-select" id="mascotaEspecie" required>
                                    <option value="Perro">Perro</option>
                                    <option value="Gato">Gato</option>
                                    <option value="Conejo">Conejo</option>
                                    <option value="Otro">Otro</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-dog me-2"></i>
                                    Raza
                                </label>
                                <input type="text" class="form-control" id="mascotaRaza">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-birthday-cake me-2"></i>
                                    Edad (años)
                                </label>
                                <input type="number" class="form-control" id="mascotaEdad">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-weight me-2"></i>
                                    Peso (kg)
                                </label>
                                <input type="number" step="0.01" class="form-control" id="mascotaPeso">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-sticky-note me-2"></i>
                                    Notas
                                </label>
                                <textarea class="form-control" id="mascotaNotas" rows="2"></textarea>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>
                            Agregar Mascota
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
    <script>
        // Variables globales
        let clientesModal;
        let mascotasModal;
        let clientesData = []; // Variable global para almacenar los clientes

        // Inicialización
        document.addEventListener('DOMContentLoaded', function() {
            clientesModal = new bootstrap.Modal(document.getElementById('clienteModal'));
            mascotasModal = new bootstrap.Modal(document.getElementById('mascotasModal'));
            
            cargarClientes();
            inicializarEventListeners();
        });

        // Inicializar event listeners
        function inicializarEventListeners() {
            // Botón nuevo cliente
            document.querySelector('[data-bs-target="#clienteModal"]').addEventListener('click', function() {
                limpiarFormularioCliente();
                document.querySelector('#modalTitle').textContent = 'Nuevo Cliente';
            });

            // Formulario de cliente
            document.getElementById('formCliente').addEventListener('submit', (e) => e.preventDefault());
            document.getElementById('guardarCliente').addEventListener('click', guardarCliente);

            // Formulario de mascota
            document.getElementById('formMascota').addEventListener('submit', function(e) {
                e.preventDefault();
                guardarMascota();
            });

            // Buscador
            document.getElementById('buscarCliente').addEventListener('input', function(e) {
                const busqueda = e.target.value.toLowerCase();
                const filas = document.querySelectorAll('#tablaClientes tr');
                
                filas.forEach(fila => {
                    const texto = fila.textContent.toLowerCase();
                    fila.style.display = texto.includes(busqueda) ? '' : 'none';
                });
            });

            // Filtro de clientes inactivos
            document.getElementById('mostrarInactivos').addEventListener('change', async function(e) {
                await cargarClientes();
            });

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
        }

        // Cargar clientes
        async function cargarClientes() {
            try {
                const mostrarInactivos = document.getElementById('mostrarInactivos').checked;
                const response = await fetch(`obtener_clientes.php${mostrarInactivos ? '?incluir_inactivos=1' : ''}`);
                const resultado = await response.json();
                
                if (!resultado.success) {
                    throw new Error(resultado.message);
                }

                clientesData = resultado.data;
                mostrarClientes(clientesData);

            } catch (error) {
                console.error('Error:', error);
                mostrarError('Error al cargar los clientes');
            }
        }

        function mostrarClientes(clientes) {
            const tbody = document.getElementById('tablaClientes');
            tbody.innerHTML = '';
            
            clientes.forEach(cliente => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${cliente.id}</td>
                    <td>${cliente.nombre}</td>
                    <td>${cliente.email}</td>
                    <td>${cliente.telefono || '-'}</td>
                    <td>${cliente.direccion || '-'}</td>
                    <td>${cliente.num_mascotas} mascota(s)</td>
                    <td>${cliente.fecha_registro}</td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-action btn-edit" 
                                onclick="editarCliente(${cliente.id})"
                                title="Editar cliente">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-action btn-pets" 
                                onclick="verMascotas(${cliente.id})"
                                title="Ver mascotas">
                            <i class="fas fa-paw"></i>
                        </button>
                        <button class="btn btn-sm btn-action ${cliente.activo ? 'btn-toggle-active' : 'btn-toggle-inactive'}" 
                                onclick="toggleEstadoCliente(${cliente.id})" 
                                title="${cliente.activo ? 'Desactivar cliente' : 'Activar cliente'}">
                            <i class="fas ${cliente.activo ? 'fa-ban' : 'fa-check'}"></i>
                        </button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        // Guardar cliente
        async function guardarCliente() {
            try {
                const clienteId = document.getElementById('clienteId').value;
                const nombre = document.getElementById('nombre').value;
                const email = document.getElementById('email').value;
                const telefono = document.getElementById('telefono').value;
                const direccion = document.getElementById('direccion').value;

                if (!nombre || !email || !telefono) {
                    throw new Error('Por favor, completa todos los campos obligatorios');
                }

                const data = {
                    nombre,
                    email,
                    telefono,
                    direccion
                };

                if (clienteId) {
                    data.id = parseInt(clienteId);
                }

                const response = await fetch('guardar_cliente.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const resultado = await response.json();
                
                if (!resultado.success) {
                    throw new Error(resultado.message);
                }

                await Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: resultado.message
                });

                // Cerrar modal y recargar clientes
                const modal = bootstrap.Modal.getInstance(document.getElementById('clienteModal'));
                modal.hide();
                document.getElementById('formCliente').reset();
                await cargarClientes();

            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'Error al guardar el cliente'
                });
            }
        }

        // Ver mascotas
        async function verMascotas(clienteId) {
            try {
                const response = await fetch(`obtener_mascotas.php?cliente_id=${clienteId}`);
                const mascotas = await response.json();
                const cliente = clientesData.find(c => c.id === clienteId);
                
                let contenidoHTML = `
                    <div class="mb-3">
                        <button class="btn btn-primary btn-sm" onclick="mostrarFormularioMascota(${clienteId})">
                            <i class="fas fa-plus"></i> Añadir Mascota
                        </button>
                    </div>
                    <div class="list-group">`;

                if (!mascotas || mascotas.length === 0) {
                    contenidoHTML += '<div class="text-center p-3">Este cliente no tiene mascotas registradas</div>';
                } else {
                    mascotas.forEach(mascota => {
                        contenidoHTML += `
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">${mascota.nombre}</h6>
                                        <small>
                                            ${mascota.especie}
                                            ${mascota.raza ? ` - ${mascota.raza}` : ''}
                                        </small>
                                    </div>
                                    <div>
                                        ${mascota.edad ? `<span class="badge bg-info me-2">${mascota.edad} años</span>` : ''}
                                        ${mascota.peso ? `<span class="badge bg-success">${mascota.peso} kg</span>` : ''}
                                    </div>
                                </div>
                                ${mascota.notas ? `<p class="mb-1 small text-muted">${mascota.notas}</p>` : ''}
                            </div>
                        `;
                    });
                }
                contenidoHTML += '</div>';

                Swal.fire({
                    title: `Mascotas de ${cliente.nombre}`,
                    html: contenidoHTML,
                    width: '600px',
                    showConfirmButton: true,
                    confirmButtonText: 'Cerrar'
                });
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al cargar las mascotas'
                });
            }
        }

        // Mostrar formulario de mascota
        function mostrarFormularioMascota(clienteId) {
            Swal.close(); // Cerrar el modal de mascotas
            
            Swal.fire({
                title: 'Nueva Mascota',
                html: `
                    <form id="formNuevaMascota" class="text-start">
                        <input type="hidden" id="mascotaClienteId" value="${clienteId}">
                        <div class="mb-3">
                            <label for="mascotaNombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="mascotaNombre" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="mascotaEspecie" class="form-label">Especie</label>
                            <select class="form-select" id="mascotaEspecie" name="especie" required>
                                <option value="">Seleccione una especie</option>
                                <option value="Perro">Perro</option>
                                <option value="Gato">Gato</option>
                                <option value="Ave">Ave</option>
                                <option value="Conejo">Conejo</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="mascotaRaza" class="form-label">Raza</label>
                            <input type="text" class="form-control" id="mascotaRaza" name="raza">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="mascotaEdad" class="form-label">Edad (años)</label>
                                <input type="number" class="form-control" id="mascotaEdad" name="edad" min="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="mascotaPeso" class="form-label">Peso (kg)</label>
                                <input type="number" class="form-control" id="mascotaPeso" name="peso" step="0.1" min="0">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="mascotaNotas" class="form-label">Notas</label>
                            <textarea class="form-control" id="mascotaNotas" name="notas" rows="2"></textarea>
                        </div>
                    </form>
                `,
                showCancelButton: true,
                confirmButtonText: 'Guardar',
                cancelButtonText: 'Cancelar',
                didOpen: () => {
                    // Agregar event listener al formulario
                    document.getElementById('formNuevaMascota').addEventListener('submit', (e) => {
                        e.preventDefault();
                    });
                },
                preConfirm: () => {
                    const form = document.getElementById('formNuevaMascota');
                    const nombre = form.querySelector('#mascotaNombre').value.trim();
                    const especie = form.querySelector('#mascotaEspecie').value.trim();
                    
                    console.log('Valores del formulario:', {
                        nombre,
                        especie,
                        clienteId
                    });

                    if (!nombre) {
                        Swal.showValidationMessage('El nombre de la mascota es obligatorio');
                        return false;
                    }
                    if (!especie) {
                        Swal.showValidationMessage('Debe seleccionar una especie');
                        return false;
                    }

                    const formData = new FormData();
                    formData.append('cliente_id', clienteId);
                    formData.append('nombre', nombre);
                    formData.append('especie', especie);
                    
                    // Agregar campos opcionales
                    const campos = ['raza', 'edad', 'peso', 'notas'];
                    campos.forEach(campo => {
                        const valor = form.querySelector(`#mascota${campo.charAt(0).toUpperCase() + campo.slice(1)}`).value.trim();
                        if (valor) {
                            formData.append(campo, valor);
                        }
                    });

                    // Mostrar los datos que se van a enviar
                    for (let pair of formData.entries()) {
                        console.log(pair[0] + ': ' + pair[1]);
                    }

                    return fetch('guardar_mascota.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => {
                                throw new Error(err.message || 'Error al guardar la mascota');
                            });
                        }
                        return response.json();
                    })
                    .then(result => {
                        if (!result.success) {
                            throw new Error(result.message || 'Error al guardar la mascota');
                        }
                        return result;
                    });
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Mascota guardada',
                        text: 'La mascota se ha guardado correctamente'
                    }).then(() => {
                        verMascotas(clienteId); // Recargar la lista de mascotas
                    });
                }
            }).catch(error => {
                console.error('Error al guardar mascota:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'Error al guardar la mascota'
                });
            });
        }

        // Editar cliente
        function editarCliente(id) {
            const cliente = clientesData.find(c => c.id === id);
            if (cliente) {
                document.getElementById('clienteId').value = cliente.id;
                document.getElementById('nombre').value = cliente.nombre;
                document.getElementById('email').value = cliente.email;
                document.getElementById('telefono').value = cliente.telefono;
                document.getElementById('direccion').value = cliente.direccion || '';
                
                document.querySelector('#modalTitle').textContent = 'Editar Cliente';
                clientesModal.show();
            }
        }

        // Toggle estado cliente
        async function toggleEstadoCliente(id) {
            const cliente = clientesData.find(c => c.id === id);
            if (!cliente) return;

            const nuevoEstado = !cliente.activo;
            const result = await Swal.fire({
                title: `¿${nuevoEstado ? 'Activar' : 'Desactivar'} cliente?`,
                text: `¿Estás seguro de que deseas ${nuevoEstado ? 'activar' : 'desactivar'} a ${cliente.nombre}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: nuevoEstado ? '#28a745' : '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: `Sí, ${nuevoEstado ? 'activar' : 'desactivar'}`
            });

            if (result.isConfirmed) {
                try {
                    const response = await fetch('toggle_estado_cliente.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ 
                            id,
                            estado: nuevoEstado
                        })
                    });

                    const resultado = await response.json();
                    
                    if (!resultado.success) {
                        throw new Error(resultado.message);
                    }

                    await Swal.fire({
                        icon: 'success',
                        title: `Cliente ${nuevoEstado ? 'activado' : 'desactivado'}`,
                        text: `El cliente ha sido ${nuevoEstado ? 'activado' : 'desactivado'} correctamente`
                    });

                    // Recargar la lista de clientes
                    await cargarClientes();

                } catch (error) {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: `Error al ${nuevoEstado ? 'activar' : 'desactivar'} el cliente`
                    });
                }
            }
        }

        // Utilidades
        function formatearFecha(fecha) {
            return new Date(fecha).toLocaleString('es-ES', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function limpiarFormularioCliente() {
            document.getElementById('clienteId').value = '';
            document.getElementById('formCliente').reset();
        }

        function limpiarFormularioMascota() {
            document.getElementById('formMascota').reset();
        }

        function mostrarExito(mensaje) {
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: mensaje,
                timer: 2000,
                showConfirmButton: false
            });
        }

        function mostrarError(mensaje) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: mensaje
            });
        }
    </script>
</body>
</html> 