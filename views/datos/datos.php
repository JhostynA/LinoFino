    <?php require_once '../../contenido.php'; ?>

    <body>
    <div class="container-fluid mt-4">
        <!-- Título principal -->
        <div class="text-center mb-4">
            <h2 class="display-4">Historial de Pagos</h2>
        </div>

        <!-- Contenedor de formularios -->
        <div class="row g-4">
            <!-- Columna: Formulario de búsqueda por nombre -->
            <div class="col-lg-6">
                <div class="card shadow h-100">
                    <div class="card-body">
                        <h5 class="mb-3">Búsqueda por Trabajador</h5>
                        <div class="form-group position-relative mb-3">
                            <input
                                type="text"
                                id="searchBox"
                                class="form-control"
                                placeholder="Ingrese nombres o apellidos del trabajador" />
                            <!-- Resultados de búsqueda en vivo -->
                            <ul id="searchResults" class="list-group mt-2 w-100"></ul>
                        </div>
                        <button id="searchButton" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i> Buscar Pagos
                        </button>
                    </div>
                </div>
            </div>

            <!-- Columna: Formulario de búsqueda por rango de fechas -->
            <div class="col-lg-6">
                <div class="card shadow h-100">
                    <div class="card-body">
                        <h5 class="mb-3">Filtrar por Fechas</h5>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <input type="date" id="fechaInicio" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <input type="date" id="fechaFin" class="form-control">
                            </div>
                        </div>
                        <button id="filterButton" class="btn btn-success w-100">
                            <i class="fas fa-calendar-alt"></i> Buscar por Fechas
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de resultados -->
        <div class="table-responsive mt-5">
            <table id="paymentsTable" class="table table-hover table-bordered align-middle shadow-sm w-100">
                <thead class="table-primary">
                    <tr>
                        <th>Trabajador</th>
                        <th>Orden de Producción</th>
                        <th>Secuencia</th>
                        <th>Operación</th>
                        <th>Modalidad</th>
                        <th>Fecha del Pago</th>
                        <th>Monto Pagado</th>
                    </tr>
                </thead>
                <tbody id="resultsTable">
                    <tr>
                        <td colspan="7" class="text-center text-muted">No hay resultados. Realiza una búsqueda.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

        <!-- Agregar dependencias de DataTables -->
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

        <script>
            $(document).ready(function() {
                // Establecer la fecha máxima permitida en los campos de fecha
                const today = new Date();
                today.setHours(0, 0, 0, 0); // Establecer la hora en 00:00 para evitar problemas con la hora
                const todayString = today.toISOString().split('T')[0]; // Obtener la fecha en formato YYYY-MM-DD

                $('#fechaInicio, #fechaFin').attr('max', todayString); // Asignar la fecha máxima a los campos de fecha

                // Deshabilitar el botón de buscar por fechas al cargar la página
                $('#filterButton').prop('disabled', true);
                $('#searchButton').prop('disabled', true);

                // Mostrar resultados a medida que el usuario escribe en el campo de búsqueda
                $('#searchBox').on('input', function() {
                    const searchTerm = $(this).val();
                    if (searchTerm.length > 1) {
                        $.ajax({
                            url: '../../controllers/datos/daxtra.controller.php',
                            method: 'GET',
                            data: {
                                searchTerm
                            },
                            success: function(response) {
                                const resultados = JSON.parse(response);
                                const resultsList = $('#searchResults');
                                resultsList.empty().show();

                                if (resultados.length === 0) {
                                    resultsList.append('<li class="list-group-item text-muted">No hay resultados</li>');
                                } else {
                                    resultados.forEach(resultado => {
                                        resultsList.append(`
                                <li class="list-group-item list-group-item-action" data-trabajador="${resultado.trabajador}">
                                    ${resultado.trabajador}
                                </li>
                            `);
                                    });
                                }
                            }
                        });
                    } else {
                        $('#searchResults').hide();
                    }
                });

                // Selección de trabajador de la lista
                $(document).on('click', '#searchResults li', function() {
                    const trabajador = $(this).data('trabajador');
                    $('#searchBox').val(trabajador);
                    $('#searchResults').hide();
                    enableSearchButton(); // Habilitar el botón de búsqueda
                });

                // Función para habilitar el botón de búsqueda
                function enableSearchButton() {
                    const trabajador = $('#searchBox').val().trim();
                    if (trabajador !== '') {
                        $('#searchButton').prop('disabled', false); // Habilitar el botón
                    } else {
                        $('#searchButton').prop('disabled', true); // Deshabilitar el botón
                    }
                }

                // Validar y realizar la búsqueda por trabajador
                $('#searchButton').on('click', function() {
                    const searchTerm = $('#searchBox').val().trim();

                    if (searchTerm === '') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Trabajador no seleccionado',
                            text: 'Por favor, ingrese o seleccione un trabajador para realizar la búsqueda.',
                        });
                        return;
                    }

                    $.ajax({
                        url: '../../controllers/datos/daxtra.controller.php',
                        method: 'GET',
                        data: {
                            searchTerm
                        },
                        success: function(response) {
                            const resultados = JSON.parse(response);
                            updateTable(resultados);

                            // Limpiar campo de búsqueda y ocultar resultados
                            $('#searchBox').val('');
                            $('#searchResults').hide();

                            // Deshabilitar el botón de búsqueda después de la búsqueda
                            $('#searchButton').prop('disabled', true);
                        }
                    });
                });

                // Función para actualizar la tabla con los resultados
                function updateTable(resultados) {
    console.log("Datos recibidos del servidor:", resultados); // Verifica los datos recibidos

    const tableBody = $('#resultsTable');
    tableBody.empty(); // Limpiar la tabla antes de actualizar

    if ($.fn.DataTable.isDataTable('#paymentsTable')) {
        $('#paymentsTable').DataTable().clear().destroy(); // Destruir instancia anterior
    }

    if (resultados.length === 0) {
        tableBody.append('<tr><td colspan="7" class="text-center text-muted">No se encontraron resultados.</td></tr>');
    } else {
        resultados.forEach(resultado => {
            const trabajador = resultado.trabajador || '-';
            const ordenProduccion = resultado.orden_produccion || '-';
            const secuencia = resultado.secuencia || '-';
            const operacion = resultado.operacion || '-';
            const modalidad = resultado.modalidad || '-';
            const fechaPago = resultado.fecha_pago || '-';
            const montoPagado = resultado.monto_pagado || '-';

            tableBody.append(`
                <tr>
                    <td>${trabajador}</td>
                    <td>${ordenProduccion}</td>
                    <td>${secuencia}</td>
                    <td>${operacion}</td>
                    <td>${modalidad}</td>
                    <td>${fechaPago}</td>
                    <td>${montoPagado}</td>
                </tr>
            `);
        });
    }

    // Re-activar DataTable con la configuración en español
    $('#paymentsTable').DataTable({
        language: {
            "decimal": "",
            "emptyTable": "No hay datos disponibles",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ entradas",
            "infoEmpty": "Mostrando 0 a 0 de 0 entradas",
            "infoFiltered": "(filtrado de _MAX_ entradas totales)",
            "lengthMenu": "Mostrar _MENU_ entradas",
            "loadingRecords": "Cargando...",
            "processing": "Procesando...",
            "search": "Buscar:",
            "zeroRecords": "No se encontraron resultados",
            "paginate": {
                "first": "Primero",
                "last": "Último",
                "next": "Siguiente",
                "previous": "Anterior"
            }
        }
    });
}


                // Verificar si ambos campos de fecha están completos para habilitar el botón de búsqueda por fechas
                $('#fechaInicio, #fechaFin').on('change', function() {
                    const fechaInicio = $('#fechaInicio').val();
                    const fechaFin = $('#fechaFin').val();

                    if (fechaInicio && fechaFin) {
                        $('#filterButton').prop('disabled', false); // Habilitar el botón si ambos campos están completos
                    } else {
                        $('#filterButton').prop('disabled', true); // Deshabilitar el botón si alguno de los campos está vacío
                    }
                });

                // Filtrar por fechas
                $('#filterButton').on('click', function() {
                    let fechaInicio = $('#fechaInicio').val();
                    let fechaFin = $('#fechaFin').val();

                    // Validar las fechas
                    if (!fechaInicio || !fechaFin) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Fechas incompletas',
                            text: 'Por favor, seleccione ambas fechas para filtrar.',
                        });
                        return;
                    }

                    // Validación: Si la fecha de inicio es mayor que la de fin
                    if (fechaInicio > fechaFin) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Rango inválido',
                            text: 'La fecha de inicio no puede ser mayor que la fecha de fin.',
                        });

                        // Limpiar los campos y deshabilitar el botón de búsqueda por fecha
                        $('#fechaInicio').val('');
                        $('#fechaFin').val('');
                        $('#filterButton').prop('disabled', true); // Deshabilitar el botón
                        return;
                    }

                    // Validación: Si alguna fecha es mayor que hoy
                    if (fechaInicio > todayString || fechaFin > todayString) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Fecha inválida',
                            text: 'Las fechas no pueden ser mayores a la fecha de hoy.',
                        });

                        // Limpiar los campos de fecha
                        $('#fechaInicio').val('');
                        $('#fechaFin').val('');
                        $('#filterButton').prop('disabled', true); // Deshabilitar el botón
                        return;
                    }

                    if (fechaInicio === fechaFin) {
                        fechaFin = fechaInicio; // Si las fechas son iguales, tomar solo la fecha de inicio
                    }

                    // Realizar la búsqueda
                    $.ajax({
                        url: '../../controllers/datos/daxfe.controller.php',
                        method: 'GET',
                        data: {
                            fechaInicio,
                            fechaFin
                        },
                        success: function(response) {
                            const resultados = JSON.parse(response);
                            updateTable(resultados);

                            // Limpiar los campos de fecha después de la búsqueda
                            $('#fechaInicio').val('');
                            $('#fechaFin').val('');

                            // Deshabilitar el botón de búsqueda después de realizar la búsqueda
                            $('#filterButton').prop('disabled', true);

                            // Mostrar un mensaje si no hay resultados
                            if (resultados.length === 0) {
                                Swal.fire({
                                    icon: 'info',
                                    title: 'Sin resultados',
                                    text: 'No se encontraron pagos en el rango de fechas seleccionado.',
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Hubo un problema al filtrar por fechas. Por favor, inténtelo más tarde.',
                            });
                        }
                    });
                });
            });
        </script>
        <?php require_once '../../footer.php'; ?>
    </body>
    </html>