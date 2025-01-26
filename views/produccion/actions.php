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
                <th class="text-center align-middle" style="width: 100px;">Editar</th>
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
                        <td class="text-center align-middle">
                            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal" 
                                onclick="populateModal(<?= htmlspecialchars(json_encode($action)) ?>)">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                        </td>
                    </tr>
                    <tr class="details" style="display: none; background-color: #f9f9f9;">
                        <td colspan="9">
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
                                        <th class="text-center align-middle">Editar</th>
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
                                            <td class="text-center align-middle"><?= htmlspecialchars($detalleop['talla']) ?></td>
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
                                            <td class="text-center align-middle">
                                                <button class="btn btn-sm btn-outline-primary" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editDetailModal" 
                                                    onclick="populateDetailModal(<?= htmlspecialchars(json_encode($detalleop)) ?>)">
                                                    <i class="fas fa-edit"></i> Editar
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
                    <td colspan="9" class="text-center text-muted">No hay producciones disponibles.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    </div>
</div>




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
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
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


<!-- Modal Actualizar ordenesproduccion -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Editar Acción</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editForm">
                <input type="hidden" name="idop" id="idop">
                    <div class="mb-3">
                        <label for="editOp" class="form-label">OP</label>
                        <input type="text" class="form-control" id="editOp" name="op">
                    </div>
                    <div class="mb-3">
                        <label for="editEstilo" class="form-label">Estilo</label>
                        <input type="text" class="form-control" id="editEstilo" name="estilo">
                    </div>
                    <div class="mb-3">
                        <label for="editDivision" class="form-label">División</label>
                            <select class="form-select" class="form-control" id="editDivision" name="division" required>
                                <option value="">Seleccion un tipo de división</option>
                                <option value="Niño">Niño</option>
                                <option value="Niña">Niña</option>
                                <option value="Caballero">Caballero</option>
                                <option value="Dama">Dama</option>
                            </select>
                    </div>
                    <div class="mb-3">
                        <label for="editColor" class="form-label">Color</label>
                        <input type="text" class="form-control" id="editColor" name="color">
                    </div>
                    <div class="mb-3">
                        <label for="editFechaInicio" class="form-label">Fecha Inicio</label>
                        <input type="date" class="form-control" id="editFechaInicio" name="fechainicio">
                    </div>
                    <div class="mb-3">
                        <label for="editFechaFin" class="form-label">Fecha Entrega</label>
                        <input type="date" class="form-control" id="editFechaFin" name="fechafin">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="saveChanges()">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Actualizar detalleop -->
<div class="modal fade" id="editDetailModal" tabindex="-1" role="dialog" aria-labelledby="editDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editDetailModalLabel">Editar Detalle Producción</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editDetailForm">
                <div class="modal-body">
                    <input type="hidden" name="iddetop" id="editDetailId">
                    <div class="form-group">
                        <label for="editDetailNSecuencia">N. Secuencia</label>
                        <input type="number" class="form-control" id="editDetailNSecuencia" name="numSecuencia" required>
                    </div>
                    <div class="form-group">
                        <label for="editDetailTalla">Talla</label>
                        <select class="form-select" id="editDetailTalla" name="talla" required>
                            <option value="">Seleccionar talla</option>
                            <option value="1">2T</option>
                            <option value="2">3T</option>
                            <option value="3">4T</option>
                            <option value="4">5T</option>
                            <option value="5">S</option>
                            <option value="6">M</option>
                            <option value="7">L</option>
                            <option value="8">XL</option>
                            <option value="9">XXL</option>
                            <option value="10">XXXL</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="editDetailCantidad">Cantidad</label>
                        <input type="number" class="form-control" id="editDetailCantidad" name="cantidad" required>
                    </div>
                    <div class="form-group">
                        <label for="editDetailFechaInicio">Fecha Inicio</label>
                        <input type="date" class="form-control" id="editDetailFechaInicio" name="sinicio" required>
                    </div>
                    <div class="form-group">
                        <label for="editDetailFechaFinal">Fecha Final</label>
                        <input type="date" class="form-control" id="editDetailFechaFinal" name="sfin" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="saveDetailChanges()">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Para actualizar el ordenesproduccion -->
<script>
    function populateModal(action) {
        document.getElementById('idop').value = action.idop;
        document.getElementById('editOp').value = action.op;
        document.getElementById('editEstilo').value = action.estilo;
        document.getElementById('editDivision').value = action.division;
        document.getElementById('editColor').value = action.color;
        document.getElementById('editFechaInicio').value = action.fechainicio;
        document.getElementById('editFechaFin').value = action.fechafin;
    }

    function saveChanges() {
        const form = document.getElementById('editForm');
        const data = new FormData(form);

        fetch('<?= $host ?>/views/produccion/indexP.php?action=updateOrdenProduccion', {
            method: 'POST',
            body: data
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                location.reload();
            } else {
                alert('Error al guardar los cambios');
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
</script>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function populateDetailModal(detalleop) {
        document.getElementById('editDetailId').value = detalleop.iddetop;
        document.getElementById('editDetailNSecuencia').value = detalleop.numSecuencia;
        document.getElementById('editDetailTalla').value = detalleop.talla;
        document.getElementById('editDetailCantidad').value = detalleop.cantidad;
        document.getElementById('editDetailFechaInicio').value = detalleop.sinicio;
        document.getElementById('editDetailFechaFinal').value = detalleop.sfin;
    }

    function saveDetailChanges() {
        const form = document.getElementById('editDetailForm');
        const data = new FormData(form);

        fetch('<?= $host ?>/views/produccion/indexP.php?action=updateDetalleProduccion', {
            method: 'POST',
            body: data
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                location.reload();
            } else {
                alert(result.error || 'Error al guardar los cambios');
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
</script>

<!-- Validación para seleccionar una OP -->
<script>
    document.getElementById('operationsForm').addEventListener('submit', (e) => {
        const checkboxes = document.querySelectorAll('#operationsGroup .form-check-input:checked');
        if (checkboxes.length === 0) {
            e.preventDefault();
            alert('Debes seleccionar al menos una operación.');
        }
    });
</script>
<!-- Las OP seleccionadas -->
<script>
    document.querySelectorAll('.open-operations-modal').forEach(button => {
    button.addEventListener('click', async function () {
        const iddetop = this.getAttribute('data-iddetop');
        document.getElementById('iddetopInput').value = iddetop;

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
            
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
                checkbox.disabled = false;
            });

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
<!-- Alert -->
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
        const detailsRow = button.closest('tr').nextElementSibling;
        if (detailsRow.style.display === 'none' || detailsRow.style.display === '') {
            detailsRow.style.display = 'table-row';
            button.textContent = '▼'; // Cambia el ícono a "expandido"
        } else {
            detailsRow.style.display = 'none';
            button.textContent = '▶'; // Cambia el ícono a "colapsado"
        }
    }


    document.querySelectorAll('.open-operations-modal').forEach(button => {
        button.addEventListener('click', function () {
            const iddetop = this.getAttribute('data-iddetop');
            document.getElementById('iddetopInput').value = iddetop;
        });
    });

</script>

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
