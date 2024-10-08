<?php require_once '../../contenido.php'; ?>

<div class="container mt-5">
    <h1 class="mb-4" style="text-align: center;">PRODUCCIÓN</h1>


    <div class="d-flex justify-content-between mb-3">
        <div class="input-group search-container">
            <input type="text" id="searchInput" class="form-control" placeholder="Search..." aria-label="Search">
        </div>

        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createActionModal">
            Nueva Producción
        </button>
    </div>

    <table class="table table-hover" id="actionsTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Fecha Inicio</th>
                <th>Fecha Entrega</th>
                <th>Cantidad Prendas</th>
                <th>Progreso</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($actions as $action): ?>
                <tr>
                    <td><?= htmlspecialchars($action['id']) ?></td>
                    <td><a href="<?= $host ?>/views/produccion/indexP.php?action=view&id=<?= $action['id'] ?>" class="text-primary"><?= htmlspecialchars($action['nombre']) ?></a></td>
                    <td><?= htmlspecialchars($action['fecha_inicio']) ?></td>
                    <td><?= htmlspecialchars($action['fecha_entrega']) ?></td>
                    <td><?= htmlspecialchars($action['cantidad_prendas']) ?></td>
                    <td>
                        <div class="progress">
                            <div class="progress-bar 
                                <?php if ($action['porcentaje'] <= 40) echo 'bg-danger'; ?>
                                <?php if ($action['porcentaje'] > 40 && $action['porcentaje'] <= 80) echo 'bg-warning'; ?>
                                <?php if ($action['porcentaje'] > 80) echo 'bg-success'; ?>" 
                                role="progressbar" 
                                style="width: <?= $action['porcentaje'] ?>%;" 
                                aria-valuenow="<?= $action['porcentaje'] ?>" 
                                aria-valuemin="0" 
                                aria-valuemax="100">
                                <?= $action['porcentaje'] ?>%
                            </div>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>




    <!-- Modal para crear nueva producción -->
    <div class="modal fade" id="createActionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nueva Producción</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="<?= $host ?>/views/produccion/indexP.php?action=create">
                        <div class="form-group">
                            <label for="name">Nombre de la nueva producción:</label>
                            <input type="text" class="form-control" name="nombre" required>
                        </div>
                        <div class="form-group">
                            <label for="fecha_inicio">Fecha de Inicio:</label>
                            <input type="date" class="form-control" name="fecha_inicio" required>
                        </div>
                        <div class="form-group">
                            <label for="fecha_entrega">Fecha de Entrega:</label>
                            <input type="date" class="form-control" name="fecha_entrega" required>
                        </div>
                        <div class="form-group">
                            <label for="cantidad_prendas">Cantidad de Prendas:</label>
                            <input type="number" class="form-control" name="cantidad_prendas" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>



</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    document.getElementById('searchInput').addEventListener('keyup', function() {
        var input = document.getElementById('searchInput').value.toLowerCase();
        var rows = document.getElementById('actionsTable').getElementsByTagName('tr');

        for (var i = 1; i < rows.length; i++) {
            var nombre = rows[i].getElementsByTagName('td')[1];
            if (nombre) {
                var txtValue = nombre.textContent || nombre.innerText;
                rows[i].style.display = txtValue.toLowerCase().indexOf(input) > -1 ? "" : "none";
            }
        }
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        //Para la fecha
        const today = new Date().toISOString().split('T')[0];
        const fechaInicioInput = document.querySelector('input[name="fecha_inicio"]');
        const fechaEntregaInput = document.querySelector('input[name="fecha_entrega"]');

        //Para la cantidad de prendas
        const cantidadPrendasInput = document.querySelector('input[name="cantidad_prendas"]');
        const form = cantidadPrendasInput.closest('form');


        //Fecha
        fechaInicioInput.setAttribute('min', today);

        fechaInicioInput.addEventListener('change', function () {
            const selectedFechaInicio = new Date(this.value);
            const fechaMinimaEntrega = selectedFechaInicio.toISOString().split('T')[0];
            fechaEntregaInput.setAttribute('min', fechaMinimaEntrega);
            if (fechaEntregaInput.value < fechaMinimaEntrega) {
                fechaEntregaInput.value = ''; 
            }
        });

        //Cantidad de prendas
        form.addEventListener('submit', function(event) {
            const cantidadPrendas = parseInt(cantidadPrendasInput.value, 10);
            if (cantidadPrendas <= 0) {
                event.preventDefault();
                alert('La cantidad de prendas debe ser mayor a 0.');
            }
        });
    });
</script>


<?php require_once '../../footer.php'; ?>

</body>
</html>
