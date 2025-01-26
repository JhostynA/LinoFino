<?php
require_once '../../contenido.php';
require_once '../../models/produccion/ActionModel.php';
$clientes = (new ActionModel())->getClientesActivos();
?>

<div class="container my-5 p-5 shadow-sm rounded bg-light" style="max-width: 90%; min-height: 85vh;">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <h2 class="mb-0">Registrar Nueva Producción</h2>
        <a href="<?= $host ?>/views/produccion/registrarClientes.php" class="btn btn-secondary btn-lg">Administrar Clientes</a>
    </div>
    
    <form id="formCreateAction" method="POST" action="<?= $host ?>/views/produccion/indexP.php?action=createOrdenProduccion">
        
        <div class="row mb-4">
            <div class="col-md-4">
                <label for="idcliente" class="form-label">Cliente:</label>
                <select class="form-select" id="idcliente" name="idcliente" required>
                    <option value="">Seleccione un cliente</option>
                    <?php foreach ($clientes as $cliente): ?>
                        <option value="<?= $cliente['idcliente'] ?>"><?= htmlspecialchars($cliente['nombrecomercial']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label for="op" class="form-label">OP:</label>
                <input type="number" class="form-control" id="op" name="op" required>
            </div>
            <div class="col-md-4">
                <label for="division" class="form-label">División</label>
                    <select class="form-select" class="form-control" id="division" name="division" required>
                        <option value="">Seleccion un tipo de división</option>
                        <option value="Niño">Niño</option>
                        <option value="Niña">Niña</option>
                        <option value="Caballero">Caballero</option>
                        <option value="Dama">Dama</option>
                    </select>
            </div>
        </div>
        
        <div class="row mb-4">
            <div class="col-md-3">
                <label for="estilo" class="form-label">Estilo:</label>
                <input type="text" class="form-control" id="estilo" name="estilo" required>
            </div>
            <div class="col-md-3">
                <label for="color" class="form-label">Color:</label>
                <input type="text" class="form-control" id="color" name="color" required>
            </div>
            <div class="col-md-3">
                <label for="fechainicio" class="form-label">Fecha de Inicio:</label>
                <input type="date" class="form-control" id="fechainicio" name="fechainicio" required>
            </div>
            <div class="col-md-3">
                <label for="fechafin" class="form-label">Fecha de Entrega:</label>
                <input type="date" class="form-control" id="fechafin" name="fechafin" required>
            </div>
        </div>
        
        <div class="text-end mt-4">
            <button type="submit" class="btn btn-primary btn-lg" id="guardarBtn">Registrar Producción</button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const factual = new Date();
        const anho = factual.getFullYear();
        const mes = String(factual.getMonth() + 1).padStart(2, '0'); 
        const dia = String(factual.getDate()).padStart(2, '0'); 
        const FechaActual = `${anho}-${mes}-${dia}`;
        const fechaInicioInput = document.querySelector('input[name="fechainicio"]');
        const fechaEntregaInput = document.querySelector('input[name="fechafin"]');

        fechaInicioInput.setAttribute('min', FechaActual);

        fechaInicioInput.addEventListener('change', function () {
            const selectedFechaInicio = new Date(this.value); 

            if (!isNaN(selectedFechaInicio.getTime())) {
                const fechaMinimaEntrega = selectedFechaInicio.toISOString().split('T')[0];
                fechaEntregaInput.setAttribute('min', fechaMinimaEntrega);

                if (new Date(fechaEntregaInput.value) < selectedFechaInicio) {
                    fechaEntregaInput.value = '';
                }
            }
        });

        const opInput = document.querySelector('input[name="op"]');
        const form = opInput.closest('form');

        form.addEventListener('submit', function(event) {
            const op = parseInt(opInput.value, 10);
            if(op <= 0) {
                event.preventDefault();
                alert('La OP debe ser mayor a 0');
                return;
            }
        });

        document.querySelector("#guardarBtn").addEventListener("click", async (event) => {
            event.preventDefault(); 
            
            const confirmacion = await Swal.fire({
                title: '¿Está seguro de registrar esta producción?',
                text: 'Lino Fino',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, registrar',
                cancelButtonText: 'Cancelar',
            });

            if (confirmacion.isConfirmed) {
                Swal.fire({
                    title: 'Producción registrada',
                    text: 'La producción se ha registrado exitosamente',
                    icon: 'success',
                    confirmButtonText: 'Aceptar'
                }).then(() => {
                    form.submit();
                });
            } else {
                Swal.fire({
                    title: 'Registro cancelado',
                    text: 'El registro ha sido cancelado',
                    icon: 'info',
                    confirmButtonText: 'Aceptar'
                });
            }
        });
    });
</script>

<?php require_once '../../footer.php'; ?>
