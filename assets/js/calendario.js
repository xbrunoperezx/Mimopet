// Referencias DOM
document.addEventListener('DOMContentLoaded', function() {
    // Obtener referencias a los elementos
    const selectMes = document.getElementById('selectMes');
    const selectAnio = document.getElementById('selectAnio');
    const btnPrev = document.getElementById('btnPrev');
    const btnHoy = document.getElementById('btnHoy');
    const btnNext = document.getElementById('btnNext');
    const diasMes = document.getElementById('diasMes');
    const formularioReserva = document.getElementById('formularioReserva');
    const fechaSeleccionadaTexto = document.getElementById('fechaSeleccionadaTexto');
    const tipoServicio = document.getElementById('tipoServicio');
    const formReserva = document.getElementById('reservarCitaForm');
    const horaDesde = document.getElementById('horaDesde');
    const horaHasta = document.getElementById('horaHasta');
    const precioTotalSpan = document.getElementById('precioTotal');
    
    // Verificar que todos los elementos necesarios existen
    if (!selectMes || !selectAnio || !btnPrev || !btnHoy || !btnNext || !diasMes) {
        console.error('Faltan elementos necesarios para el calendario');
        return;
    }
    
    // Fecha actual
    const fechaActual = new Date();
    let mesActual = fechaActual.getMonth();
    let anioActual = fechaActual.getFullYear();
    let diaSeleccionado = null;
    
    // Precio por hora según el servicio
    const PRECIOS_POR_HORA = {
        'Peluquería': 25,
        'Cuidado a Domicilio': 5,
        'Guardería': 8
    };

    // Obtener el servicio de la URL
    const urlParams = new URLSearchParams(window.location.search);
    const servicioSeleccionado = urlParams.get('servicio');
    
    // Si hay un servicio en la URL, seleccionarlo en el formulario
    if (servicioSeleccionado && tipoServicio) {
        tipoServicio.value = servicioSeleccionado;
    }

//---------------------------------------------FUNCIONES---------------------------------------------
    
    // Generar opciones de año
    function generarAnios() {
        const anioBase = anioActual;
        selectAnio.innerHTML = '';
        for (let i = anioBase - 5; i <= anioBase + 5; i++) {
            const option = document.createElement('option');
            option.value = i;
            option.textContent = i;
            selectAnio.appendChild(option);
        }
        selectAnio.value = anioActual;
    }
    
    // Inicializar mes y año actuales en los selectores
    function inicializarFecha() {
        selectMes.value = mesActual;
        selectAnio.value = anioActual;
    }
    
    // Verificar si es fin de semana
    function esFinDeSemana(fecha) {
        const diaSemana = fecha.getDay();
        return diaSemana === 0 || diaSemana === 6;
    }
    
    // Mostrar formulario de reserva
    function mostrarFormularioReserva(fecha) {
        const opciones = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        fechaSeleccionadaTexto.textContent = fecha.toLocaleDateString('es-ES', opciones);
        
        // Formatear la fecha manteniendo la zona horaria local
        const fechaISO = fecha.toLocaleDateString('sv-SE'); // Formato YYYY-MM-DD
        document.getElementById('fechaReserva').value = fechaISO;
        
        generarOpcionesHora();
        formularioReserva.classList.remove('d-none');
        calcularPrecioTotal();
        formularioReserva.scrollIntoView({ behavior: 'smooth' });
    }
    
    // Generar opciones de hora
    function generarOpcionesHora() {
        horaDesde.innerHTML = '';
        horaHasta.innerHTML = '';
        
        for (let hora = 8; hora <= 18; hora++) {
            for (let minuto = 0; minuto < 60; minuto += 30) {
                if (hora === 18 && minuto > 0) continue;
                
                const horaFormateada = `${hora.toString().padStart(2, '0')}:${minuto.toString().padStart(2, '0')}`;
                
                const optionDesde = document.createElement('option');
                optionDesde.value = horaFormateada;
                optionDesde.textContent = horaFormateada;
                horaDesde.appendChild(optionDesde);
                
                const optionHasta = document.createElement('option');
                optionHasta.value = horaFormateada;
                optionHasta.textContent = horaFormateada;
                horaHasta.appendChild(optionHasta);
            }
        }
        
        horaDesde.value = '08:00';
        horaHasta.value = '09:00';
    }
    
    // Calcular precio total
    function calcularPrecioTotal() {
        if (!horaDesde || !horaHasta || !tipoServicio || !precioTotalSpan) {
            console.error('Faltan elementos necesarios para calcular el precio');
            return;
        }

        const horaDesdeValue = horaDesde.value;
        const horaHastaValue = horaHasta.value;
        const tipoServicioValue = tipoServicio.value;

        if (!horaDesdeValue || !horaHastaValue || !tipoServicioValue) {
            precioTotalSpan.textContent = '0.00€';
            return;
        }

        const [hDesde, mDesde] = horaDesdeValue.split(':').map(Number);
        const [hHasta, mHasta] = horaHastaValue.split(':').map(Number);
        
        const minutosDesde = hDesde * 60 + mDesde;
        const minutosHasta = hHasta * 60 + mHasta;
        
        let horas = (minutosHasta - minutosDesde) / 60;
        if (horas <= 0) {
            precioTotalSpan.textContent = '0.00€';
            return;
        }

        const precioBase = PRECIOS_POR_HORA[tipoServicioValue] || 0;
        const total = horas * precioBase;
        
        precioTotalSpan.textContent = `${total.toFixed(2)}€`;
    }
    
    // Actualizar calendario
    function actualizarCalendario() {
        diasMes.innerHTML = '';
        
        const primerDia = new Date(anioActual, mesActual, 1);
        const ultimoDia = new Date(anioActual, mesActual + 1, 0);
        const hoy = new Date();
        
        let inicioDia = primerDia.getDay() - 1;
        if (inicioDia < 0) inicioDia = 6;
        
        const ultimoDiaMesAnterior = new Date(anioActual, mesActual, 0).getDate();
        for (let i = inicioDia - 1; i >= 0; i--) {
            const dia = document.createElement('div');
            dia.classList.add('dia', 'otro-mes', 'mb-2');
            dia.textContent = ultimoDiaMesAnterior - i;
            diasMes.appendChild(dia);
        }
        
        for (let i = 1; i <= ultimoDia.getDate(); i++) {
            const dia = document.createElement('div');
            dia.classList.add('dia', 'mb-2');
            dia.textContent = i;
            
            const fechaDia = new Date(anioActual, mesActual, i);
            
            if (
                i === hoy.getDate() && 
                mesActual === hoy.getMonth() && 
                anioActual === hoy.getFullYear()
            ) {
                dia.classList.add('actual');
            }
            
            if (esFinDeSemana(fechaDia) || fechaDia < new Date(hoy.setHours(0,0,0,0))) {
                dia.classList.add('no-disponible');
                dia.title = esFinDeSemana(fechaDia) ? 
                    "No disponible (fin de semana)" : 
                    "No disponible (fecha pasada)";
            } else {
                dia.addEventListener('click', function() {
                    const diasSeleccionados = document.querySelectorAll('.dia.seleccionado');
                    diasSeleccionados.forEach(d => d.classList.remove('seleccionado'));
                    
                    this.classList.add('seleccionado');
                    diaSeleccionado = new Date(anioActual, mesActual, i);
                    mostrarFormularioReserva(diaSeleccionado);
                });
                
                dia.classList.add('disponible');
                dia.title = "Disponible";
            }
            
            diasMes.appendChild(dia);
        }
        
        const totalDias = diasMes.childElementCount;
        let diasNecesariosParaCompletarFila = 7 - (totalDias % 7);
        if (diasNecesariosParaCompletarFila === 7) diasNecesariosParaCompletarFila = 0;
        
        for (let i = 1; i <= diasNecesariosParaCompletarFila; i++) {
            const dia = document.createElement('div');
            dia.classList.add('dia', 'otro-mes', 'mb-2');
            dia.textContent = i;
            diasMes.appendChild(dia);
        }
        
        formularioReserva.classList.add('d-none');
    }

//---------------------------------------------EVENT LISTENERS---------------------------------------------
    
    // Verificar disponibilidad al cambiar horarios
    ['horaDesde', 'horaHasta'].forEach(id => {
        document.getElementById(id).addEventListener('change', verificarDisponibilidad);
    });

    async function verificarDisponibilidad() {
        const horaDesde = document.getElementById('horaDesde').value;
        const horaHasta = document.getElementById('horaHasta').value;
        const fecha = document.getElementById('fechaReserva').value;
        
        // Solo verificar si tenemos todos los datos necesarios
        if (!horaDesde || !horaHasta || !fecha) {
            console.log('Faltan datos para verificar disponibilidad');
            return;
        }

        // No verificar si la hora final es menor o igual a la inicial
        if (horaHasta <= horaDesde) {
            await Swal.fire({
                icon: 'warning',
                title: 'Horario Inválido',
                text: 'La hora de fin debe ser posterior a la hora de inicio',
                confirmButtonText: 'Aceptar'
            });
            document.getElementById('horaHasta').value = '';
            document.getElementById('precioTotal').textContent = '0.00€';
            return;
        }

        try {
            console.log('Verificando disponibilidad para:', {
                fecha: fecha,
                hora_inicio: horaDesde,
                hora_fin: horaHasta
            });

            const params = new URLSearchParams({
                fecha: fecha,
                hora_inicio: horaDesde,
                hora_fin: horaHasta
            });

            const url = `../../backend/verificar_disponibilidad.php?${params.toString()}`;
            console.log('URL de verificación:', url);

            const response = await fetch(url);
            
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }

            const data = await response.json();
            console.log('Respuesta del servidor:', data);

            if (!data.success) {
                throw new Error(data.message);
            }

            if (!data.disponible) {
                await Swal.fire({
                    icon: 'warning',
                    title: 'Horario No Disponible',
                    text: 'Lo sentimos, este horario ya está reservado. Por favor, selecciona otro horario.',
                    confirmButtonText: 'Aceptar'
                });
                document.getElementById('horaHasta').value = '';
                document.getElementById('precioTotal').textContent = '0.00€';
            } else {
                calcularPrecioTotal();
            }
        } catch (error) {
            console.error('Error en verificación de disponibilidad:', error);
            await Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al verificar disponibilidad: ' + error.message,
                confirmButtonText: 'Aceptar'
            });
        }
    }

    // Event Listeners para el formulario
    if (formReserva) {
        formReserva.addEventListener('submit', async function(e) {
            e.preventDefault();
            console.log('Iniciando envío de formulario...');
            
            try {
                const formData = new FormData(this);
                
                // Imprimir datos del formulario para depuración
                for (let pair of formData.entries()) {
                    console.log(pair[0] + ': ' + pair[1]);
                }

                const submitButton = this.querySelector('button[type="submit"]');
                const originalButtonText = submitButton.innerHTML;
                submitButton.disabled = true;
                submitButton.innerHTML = 'Guardando...';

                const reservaResponse = await fetch('../../backend/procesar_reserva_frontend.php', {
                    method: 'POST',
                    body: formData
                });

                console.log('Respuesta del servidor:', reservaResponse);

                if (!reservaResponse.ok) {
                    throw new Error('Error en la respuesta del servidor: ' + reservaResponse.status);
                }

                const reservaData = await reservaResponse.json();
                console.log('Datos de respuesta:', reservaData);

                if (reservaData.success) {
                    await Swal.fire({
                        icon: 'success',
                        title: '¡Reserva Exitosa!',
                        text: 'Tu reserva ha sido creada correctamente. Pronto nos pondremos en contacto contigo.',
                        confirmButtonText: 'Aceptar'
                    });
                    
                    this.reset();
                    formularioReserva.classList.add('d-none');
                    const diasSeleccionados = document.querySelectorAll('.dia.seleccionado');
                    diasSeleccionados.forEach(d => d.classList.remove('seleccionado'));
                } else {
                    throw new Error(reservaData.message || 'Error desconocido en el servidor');
                }
            } catch (error) {
                console.error('Error en el proceso:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al procesar la reserva: ' + error.message,
                    confirmButtonText: 'Aceptar'
                });
            } finally {
                const submitButton = this.querySelector('button[type="submit"]');
                submitButton.disabled = false;
                submitButton.innerHTML = 'Confirmar Reserva';
            }
        });
    }

    // Event listeners para cambios en hora y servicio
    if (horaDesde) {
        horaDesde.addEventListener('change', function() {
            calcularPrecioTotal();
            verificarDisponibilidad();
        });
    }

    if (horaHasta) {
        horaHasta.addEventListener('change', function() {
            calcularPrecioTotal();
            verificarDisponibilidad();
        });
    }

    if (tipoServicio) {
        tipoServicio.addEventListener('change', calcularPrecioTotal);
    }

    // Event listeners para controles del calendario
    selectMes.addEventListener('change', () => {
        mesActual = parseInt(selectMes.value);
        actualizarCalendario();
    });

    selectAnio.addEventListener('change', () => {
        anioActual = parseInt(selectAnio.value);
        actualizarCalendario();
    });

    btnPrev.addEventListener('click', () => {
        mesActual--;
        if (mesActual < 0) {
            mesActual = 11;
            anioActual--;
        }
        inicializarFecha();
        actualizarCalendario();
    });

    btnNext.addEventListener('click', () => {
        mesActual++;
        if (mesActual > 11) {
            mesActual = 0;
            anioActual++;
        }
        inicializarFecha();
        actualizarCalendario();
    });

    btnHoy.addEventListener('click', () => {
        const fechaActual = new Date();
        mesActual = fechaActual.getMonth();
        anioActual = fechaActual.getFullYear();
        inicializarFecha();
        actualizarCalendario();
    });

    // Inicializar calendario
    generarAnios();
    inicializarFecha();
    actualizarCalendario();
}); 