<?php require_once '../../contenido.php'; 
require_once '../../models/produccion/ActionModel.php';
$secuenciasModel = new ActionModel();

$tallas = $secuenciasModel->getTallas();


$iddetop = isset($_GET['iddetop']) ? $_GET['iddetop'] : null;

$operaciones = $secuenciasModel->getOperaciones();


$operacionesSeleccionadas = $secuenciasModel->getOperacionesSeleccionadas($iddetop); 

?>



<div class="container-fluid mt-5">
    <div class="d-flex justify-content-between mb-3 align-items-center">
        <h1 class="mb-4 text-center w-100">PRODUCCIÓN</h1>
    </div>

    <div class="mb-3">
        <input 
            type="text" 
            id="searchOP" 
            class="form-control" 
            placeholder="Buscar por OP..." 
            onkeyup="filterTable()">
    </div>

    <div class="table-responsive">
        <table id="actionsTable" class="table table-bordered shadow-lg w-100">
            <thead class="thead-dark">
                <tr>
                    <th class="text-center align-middle" style="width: 80px;">OP</th>
                    <th class="text-center align-middle" style="width: 80px;">D-OP</th>
                    <th class="text-center align-middle" style="width: 120px;">Estilo</th>
                    <th class="text-center align-middle" style="width: 120px;">División</th>
                    <th class="text-center align-middle" style="width: 120px;">Color</th>
                    <th class="text-center align-middle" style="width: 120px;">Fecha Inicio</th>
                    <th class="text-center align-middle" style="width: 120px;">Fecha Entrega</th>
                    <th class="text-center align-middle" style="width: 120px;">PDF</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($actions) && !empty($actions)): ?>
                    <?php foreach ($actions as $action): ?>
                        <tr class="table-hover action-row" data-op="<?= htmlspecialchars($action['idop']) ?>">
                            <td class="text-center align-middle"><?= htmlspecialchars($action['op']) ?></td>
                            <td class="text-center align-middle">
                                <button class="btn btn-link" onclick="toggleDetails(this)">▶</button>
                            </td>
                            <td class="text-center align-middle"><?= htmlspecialchars($action['estilo']) ?></td>
                            <td class="text-center align-middle"><?= htmlspecialchars($action['division']) ?></td>
                            <td class="text-center align-middle"><?= htmlspecialchars($action['color']) ?></td>
                            <td class="text-center align-middle"><?= htmlspecialchars($action['fechainicio']) ?></td>
                            <td class="text-center align-middle"><?= htmlspecialchars($action['fechafin']) ?></td>
                            <td class="text-center align-middle">
                                <a href="<?= $host ?>/views/produccion/indexP.php?action=viewPDF&id=<?= $action['idop'] ?>" class="btn btn-outline-danger">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                            </td>
                        </tr>
                        <tr class="details" style="display: none; background-color: #f9f9f9;">
                            <td colspan="8">
                                <div class="d-flex align-items-center mb-3">
                                    <button class="btn btn-primary btn-sm mr-3 open-modal-btn" 
                                        data-toggle="modal" 
                                        data-target="#createSequenceModal" 
                                        data-op="<?= htmlspecialchars($action['idop']) ?>">
                                        Nuevo Detalle Producción
                                    </button>
                                </div>

                                <table class="table table-sm rounded shadow-sm">
                                    <thead class="thead-light" style="background-color: #007bff; color: #fff;">
                                        <tr>
                                            <th class="text-center align-middle" style="width: 100px;">N. Secuencia</th>
                                            <th class="text-center align-middle">Talla</th>
                                            <th class="text-center align-middle">Cantidad</th>
                                            <th class="text-center align-middle">F Inicio</th>
                                            <th class="text-center align-middle">F Final</th>
                                            <th class="text-center align-middle">Operaciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $detalleOP = $secuenciasModel->getDetalleByOP($action['idop']);
                                        foreach ($detalleOP as $detalleop): ?>
                                            <tr>
                                                <td class="text-center align-middle">
                                                    <a href="<?= $host ?>/views/produccion/indexP.php?action=viewSecuencia&iddetop=<?= $detalleop['iddetop'] ?>" class="text-primary">
                                                        <button class="btn btn-outline-primary"><?= htmlspecialchars($detalleop['numSecuencia']) ?></button>
                                                    </a>
                                                </td>
                                                <td class="text-center align-middle"><?= htmlspecialchars($detalleop['iddetop']) ?></td>
                                                <td class="text-center align-middle"><?= htmlspecialchars($detalleop['cantidad']) ?></td>
                                                <td class="text-center align-middle"><?= htmlspecialchars($detalleop['sinicio']) ?></td>
                                                <td class="text-center align-middle"><?= htmlspecialchars($detalleop['sfin']) ?></td>
                                                <td class="text-center align-middle">
                                                    <button class="btn btn-sm btn-info open-operations-modal" 
                                                        data-toggle="modal" 
                                                        data-target="#operationsModal" 
                                                        data-iddetop="<?= $detalleop['iddetop'] ?>"
                                                        data-cantidad="<?= $detalleop['cantidad'] ?>"> 
                                                        Operaciones
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <?php if (empty($detalleOP)): ?>
                                            <tr>
                                                <td colspan="6" class="text-center text-muted">No hay detalles disponibles para esta OP.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted">No hay producciones disponibles.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    function filterTable() {
        const input = document.getElementById('searchOP').value.toUpperCase();
        const table = document.getElementById('actionsTable');
        const rows = table.getElementsByTagName('tr');

        for (let i = 1; i < rows.length; i++) { 
            const opCell = rows[i].querySelector('td:first-child'); 
            if (opCell) {
                const opText = opCell.textContent || opCell.innerText;
                rows[i].style.display = opText.toUpperCase().indexOf(input) > -1 ? '' : 'none';
            }
        }
    }
</script>


<div class="modal fade" id="createSequenceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nuevo Detalle de Producción</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formCreateSequence" method="POST" action="<?= $host ?>/views/produccion/indexP.php?action=createSequence">
                    <input type="hidden" name="idop" id="opIdInput" value="">
                    <input type="hidden" name="idcliente" id="clienteIdInput" value="<?= htmlspecialchars($action['idcliente']) ?>">

                    <div class="form-group">
                        <label for="numSecuencia">Número de Secuencia:</label>
                        <input type="number" class="form-control" name="numSecuencia" required>
                    </div>

                    <div class="form-group">
                        <label for="idtalla">Talla:</label>
                        <select class="form-control" name="idtalla" id="idtalla" required>
                            <option value="" selected>Seleccione una talla</option>
                            <?php foreach ($tallas as $talla): ?>
                                <option value="<?= htmlspecialchars($talla['idtalla']) ?>">
                                    <?= htmlspecialchars($talla['talla']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="cantidad">Cantidad:</label>
                        <input type="number" class="form-control" name="cantidad" min="1" required>
                    </div>

                    <div class="form-group">
                        <label for="sinicio">Fecha de Inicio:</label>
                        <input type="date" class="form-control" name="sinicio" id="sinicio" required>
                    </div>

                    <div class="form-group">
                        <label for="sfin">Fecha Final:</label>
                        <input type="date" class="form-control" name="sfin" id="sfin" required>
                    </div>

                    <button type="button" class="btn btn-primary" id="submitBtn">Guardar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="operationsModal" tabindex="-1" role="dialog" aria-labelledby="operationsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="operationsModalLabel">Gestionar Operaciones</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="operationsForm" method="POST" action="<?= $host ?>/views/produccion/indexP.php?action=updateOperations">
                    <input type="hidden" name="idcliente" id="clienteIdInput" value="<?= htmlspecialchars($action['idcliente']) ?>">
                    <input type="hidden" name="iddetop" id="iddetopInput" value="">
                    <input type="hidden" name="cantidaO" id="cantidaOInput" value="">

                    <div id="operationsGroup" class="mb-4">
                        <label class="form-label fw-bold">Selecciona Operaciones:</label>
                        <div class="row">
                            <?php if (!empty($operaciones) && is_array($operaciones)): ?>
                                <?php foreach ($operaciones as $operacion): ?>
                                    <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input 
                                            class="form-check-input" 
                                            type="checkbox" 
                                            name="operaciones[]" 
                                            value="<?= htmlspecialchars($operacion['idoperacion']) ?>" 
                                            id="operacion_<?= $operacion['idoperacion'] ?>"
                                            <?php if (in_array($operacion['idoperacion'], $operacionesSeleccionadas)): ?>
                                                checked disabled
                                            <?php endif; ?>
                                        >
                                        <label class="form-check-label" for="operacion_<?= $operacion['idoperacion'] ?>">
                                            <?= htmlspecialchars($operacion['operacion']) ?>
                                            <?php if (in_array($operacion['idoperacion'], $operacionesSeleccionadas)): ?>
                                                <span class="text-muted">(ya seleccionada)</span>
                                            <?php endif; ?>
                                        </label>
                                    </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-danger">No hay operaciones disponibles.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-3">
                        <button type="submit" class="btn btn-primary">Guardar Operaciones</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.getElementById('operationsForm').addEventListener('submit', (e) => {
        const checkboxes = document.querySelectorAll('#operationsGroup .form-check-input:checked');
        if (checkboxes.length === 0) {
            e.preventDefault();
            alert('Debes seleccionar al menos una operación.');
        }
    });
</script>

<script>
    document.querySelectorAll('.open-operations-modal').forEach(button => {
    button.addEventListener('click', async function () {
        const iddetop = this.getAttribute('data-iddetop');
        document.getElementById('iddetopInput').value = iddetop;

        // Llama al servidor para obtener las operaciones seleccionadas
        const response = await fetch('fetch_operaciones_seleccionadas.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ iddetop }),
        });

        if (response.ok) {
            const data = await response.json();
            const checkboxes = document.querySelectorAll('#operationsGroup .form-check-input');
            
            // Resetea los checkboxes
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
                checkbox.disabled = false;
            });

            // Marca y desactiva las operaciones seleccionadas
            data.operacionesSeleccionadas.forEach(id => {
                const checkbox = document.querySelector(`#operationsGroup .form-check-input[value="${id}"]`);
                if (checkbox) {
                    checkbox.checked = true;
                    checkbox.disabled = true;
                }
            });
        } else {
            console.error('Error al obtener operaciones seleccionadas');
        }
    });
});


</script>

<script>
    document.querySelector("#submitBtn").addEventListener("click", async (event) => {
        event.preventDefault(); 

        const confirmacion = await Swal.fire({
            title: '¿Está seguro de guardar este detalle de producción?',
            text: 'Verifique que los datos sean correctos antes de proceder.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, guardar',
            cancelButtonText: 'Cancelar',
        });

        if (confirmacion.isConfirmed) {
            Swal.fire({
                title: 'Detalle de Producción Guardado',
                text: 'El detalle de producción se ha registrado exitosamente.',
                icon: 'success',
                confirmButtonText: 'Aceptar'
            }).then(() => {
                document.getElementById('formCreateSequence').submit();
            });
        } else {
            Swal.fire({
                title: 'Registro Cancelado',
                text: 'El registro ha sido cancelado.',
                icon: 'info',
                confirmButtonText: 'Aceptar'
            });
        }
    });
</script>

<script>
   $(document).on("click", ".open-operations-modal", function () {
        var cantidad = $(this).data('cantidad');
        var iddetop = $(this).data('iddetop');
        
        $("#operationsModal #iddetopInput").val(iddetop);
        $("#operationsModal #cantidaOInput").val(cantidad); 
    });

    document.querySelectorAll('.open-modal-btn').forEach(button => {
        button.addEventListener('click', function () {
            const opId = this.getAttribute('data-op');
            document.getElementById('opIdInput').value = opId;
        });
    });

    function toggleDetails(button) {
        const row = button.closest('tr').nextElementSibling;
        row.style.display = row.style.display === 'none' ? '' : 'none';
        button.textContent = row.style.display === 'none' ? '▶' : '▼';
    }

    document.querySelectorAll('.open-operations-modal').forEach(button => {
        button.addEventListener('click', function () {
            const iddetop = this.getAttribute('data-iddetop');
            document.getElementById('iddetopInput').value = iddetop;
        });
    });

</script>

<script>
    const fechainicioProduccion = "<?= htmlspecialchars($action['fechainicio']) ?>"; 
    const fechafinProduccion = "<?= htmlspecialchars($action['fechafin']) ?>";    

    const fechaInicioInput = document.querySelector('input[name="sinicio"]');
    const fechaFinInput = document.querySelector('input[name="sfin"]');

    fechaInicioInput.setAttribute('min', fechainicioProduccion);
    fechaInicioInput.setAttribute('max', fechafinProduccion);

    fechaInicioInput.addEventListener('change', function () {
        const selectedFechaInicio = new Date(this.value);

        if (!isNaN(selectedFechaInicio.getTime())) {
            const fechaMinimaEntrega = selectedFechaInicio.toISOString().split('T')[0];
            fechaFinInput.setAttribute('min', fechaMinimaEntrega);
            fechaFinInput.setAttribute('max', fechafinProduccion);

            const selectedFechaFin = new Date(fechaFinInput.value);
            if (selectedFechaFin < selectedFechaInicio || selectedFechaFin > new Date(fechafinProduccion)) {
                fechaFinInput.value = '';
            }
        }
    });
</script>

<?php require_once '../../footer.php'; ?>

</body>
</html>
