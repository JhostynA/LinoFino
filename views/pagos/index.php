<?php require_once '../../contenido.php'; 

require_once '../../controllers/pagos/pagosControllers.php';

$pagosController = new PagosController();
$clientes = $pagosController->getClientesActivos();

?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 mb-0">Pagos</h1>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form>
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="selectCliente" class="form-label">Cliente</label>
                        <select class="form-select" id="selectCliente" name="selectCliente">
                            <option selected disabled>Seleccione un cliente</option>
                            <?php foreach ($clientes as $cliente): ?>
                                <option value="<?= htmlspecialchars($cliente['idcliente']) ?>">
                                    <?= htmlspecialchars($cliente['nombrecomercial']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="selectOP" class="form-label">Orden de Producción (OP)</label>
                        <select class="form-select" id="selectOP" name="selectOP">
                            <option selected>Seleccione una OP</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="selectSecuencia" class="form-label">Secuencia</label>
                        <select class="form-select" id="selectSecuencia" name="selectSecuencia">
                            <option selected>Seleccione una secuencia</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="selectTrabajadores" class="form-label">Trabajadores</label>
                        <select class="form-select" id="selectTrabajadores" name="selectTrabajadores">
                            <option selected>Seleccione un trabajador</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-hover table-bordered align-middle">
            <thead class="table-light text-center">
                <tr>
                    <th scope="col">Fecha</th>
                    <th scope="col">Operación</th>
                    <th scope="col">Precio de Operación</th>
                    <th scope="col">Cantidad Realizada</th>
                    <th scope="col">Total Paga</th>
                    <th scope="col">Acción</th>
                </tr>
            </thead>
            <tbody>
                <!-- Contenido dinámico -->
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Pagar -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">Detalles de Pago</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="paymentForm">
                    <input type="hidden" id="idPersona" name="idpersona">
                    <input type="hidden" id="idProduccion" name="idproduccion">
                    <div class="mb-3">
                        <label for="paymentMethod" class="form-label">Método de Pago</label>
                        <select class="form-select" id="paymentMethod" name="paymentMethod" required>
                            <option value="1">YAPE</option>
                            <option value="2">PLIN</option>
                            <option value="3">EFECTIVO</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="amountPaid" class="form-label">Monto Pagado</label>
                        <input type="number" class="form-control" id="amountPaid" name="amountPaid" placeholder="Monto Pagado" required readonly>
                    </div>
                    <div class="mb-3">
                        <label for="paymentDate" class="form-label">Fecha de Pago</label>
                        <input type="date" class="form-control" id="paymentDate" name="paymentDate" required readonly>
                    </div>
                    <button type="submit" class="btn btn-primary">Realizar Pago</button>
                </form>
            </div>
        </div>
    </div>
</div>





<script>
    document.querySelector('table tbody').addEventListener('click', function (e) {
        if (e.target && e.target.matches('button.btn.btn-primary')) {
            const row = e.target.closest('tr');
            const fecha = row.querySelector('td:nth-child(1)').textContent;
            const operacion = row.querySelector('td:nth-child(2)').textContent;
            const precio = row.querySelector('td:nth-child(3)').textContent;
            const cantidad = row.querySelector('td:nth-child(4)').textContent;
            const totalPago = row.querySelector('td:nth-child(5)').textContent;
            const idPersona = row.getAttribute('data-idpersona'); 
            const idProduccion = row.getAttribute('data-idproduccion');
            document.getElementById('paymentModalLabel').textContent = `Pago de ${operacion}`;
            document.getElementById('amountPaid').value = totalPago;
            document.getElementById('paymentMethod').value = "YAPE";  
            document.getElementById('paymentDate').value = new Date().toISOString().split('T')[0]; 
            document.getElementById('idPersona').value = idPersona; 
            document.getElementById('idProduccion').value = idProduccion;

            $('#paymentModal').modal('show');
        }
    });

    document.getElementById('paymentForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const paymentData = new FormData(this);

        const dataToLog = {};
        paymentData.forEach((value, key) => {
            dataToLog[key] = value;
        });

        console.log('Datos enviados:', dataToLog);

        fetch('pagarProduccion.php', {
            method: 'POST',
            body: paymentData,
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Error en la solicitud: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Respuesta del servidor:', data);

                if (data.success) {
                    document.getElementById('paymentForm').reset(); 
                    $('#paymentModal').modal('hide'); 
                    alert('Pago registrado con éxito'); 
                } else {
                    alert('Error al registrar el pago: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error al enviar los datos:', error);
                alert('Hubo un error al procesar la solicitud.');
            });
    });




</script>

<script>

function clearOptions(selectElement, defaultMessage) {
    selectElement.innerHTML = `<option selected>${defaultMessage}</option>`;
}

function fetchOptions(url, bodyData, targetSelect, defaultOptionMessage, valueKey, textKey) {
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams(bodyData),
    })
    .then(response => response.json())
    .then(data => {
        if (data.length > 0) {
            data.forEach(item => {
                const option = document.createElement('option');
                option.value = item[valueKey];
                option.textContent = item[textKey];
                targetSelect.appendChild(option);
            });
        } else {
            clearOptions(targetSelect, 'No hay datos disponibles');
        }
    })
    .catch(error => console.error('Error:', error));
}






document.getElementById('selectCliente').addEventListener('change', function () {
    const idCliente = this.value;
    const selectOP = document.getElementById('selectOP');
    clearOptions(selectOP, 'Seleccione una OP');
    if (idCliente) {
        fetchOptions('./clienteAjax.php', { idcliente: idCliente }, selectOP, 'Seleccione una OP', 'idop', 'op');
    }
});

document.getElementById('selectOP').addEventListener('change', function () {
    const idop = this.value;
    const selectSecuencia = document.getElementById('selectSecuencia');
    clearOptions(selectSecuencia, 'Seleccione una secuencia');
    if (idop) {
        fetchOptions('./numSecuencia.php', { idop: idop }, selectSecuencia, 'Seleccione una secuencia', 'iddetop', 'numSecuencia');
    }
});

document.getElementById('selectSecuencia').addEventListener('change', function () {
    const iddetop = this.value;
    const selectTrabajadores = document.getElementById('selectTrabajadores');
    clearOptions(selectTrabajadores, 'Seleccione un trabajador');
    if (iddetop) {
        fetchOptions('./personas.php', { iddetop: iddetop }, selectTrabajadores, 'Seleccione un trabajador', 'idpersona', 'nombre_completo');
    }
});

document.getElementById('selectTrabajadores').addEventListener('change', function () {
    const idpersona = this.value;
    const tableBody = document.querySelector('table tbody');
    tableBody.innerHTML = ''; 

    if (idpersona) {
        fetch('./produccionAjax.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: new URLSearchParams({ idpersona }),
})
    .then(response => response.json())
    .then(data => {
        console.log('Datos recibidos:', data); 
        if (data.length > 0) {
            data.forEach(item => {
    const row = document.createElement('tr');
    row.setAttribute('data-idpersona', item.idpersona); 
     row.setAttribute('data-idproduccion', item.idproduccion);
    row.innerHTML = `
        <td>${item.fecha}</td>
        <td>${item.operacion}</td>
        <td>${Number(item.precio).toFixed(2)}</td>
        <td>${item.cantidadproducida}</td>
        <td>${Number(item.total_pago).toFixed(2)}</td>
        <td>
            <button class="btn btn-primary btn-sm">Pagar</button>
        </td>
    `;
    console.log('Item recibido:', item);

    tableBody.appendChild(row);
});
        } else {
            const row = document.createElement('tr');
            row.innerHTML = `<td colspan="6" class="text-center">No hay registros para esta persona.</td>`;
            tableBody.appendChild(row);
        }
    })
    .catch(error => console.error('Error:', error));

    }
});

</script>

<?php require_once '../../footer.php'; ?>

</body>
</html>
