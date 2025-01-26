<?php

class ActionController {
    private $actionModel;

    public function __construct($actionModel) {
        $this->actionModel = $actionModel;
    }
    
    public function createClientAction() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['razonsocial'], $_POST['nombrecomercial'])) {
            $razonsocial = $_POST['razonsocial'];
            $nombrecomercial = $_POST['nombrecomercial'];
            $telefono = isset($_POST['telefono']) ? $_POST['telefono'] : null;
            $email = isset($_POST['email']) ? $_POST['email'] : null;
            $direccion = isset($_POST['direccion']) ? $_POST['direccion'] : null;
            $contacto = isset($_POST['contacto']) ? $_POST['contacto'] : null;
    
            if ($this->actionModel->createClient($razonsocial, $nombrecomercial, $telefono, $email, $direccion, $contacto)){
                header('Location: ../../views/produccion/registrarClientes.php');
                exit();
            } else {
                header('Location: ../../views/produccion/registrarClientes.php?error=ClienteYaExiste');
                exit();
            }
        }
    }

    public function updateClientAction() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idcliente'])) {
            $idcliente = $_POST['idcliente'];
            $razonsocial = $_POST['razonsocial'];
            $nombrecomercial = $_POST['nombrecomercial'];
            $telefono = $_POST['telefono'] ?? null; 
            $email = $_POST['email'] ?? null;
            $direccion = $_POST['direccion'] ?? null;
            $contacto = $_POST['contacto'] ?? null;
            $estado = $_POST['estado'];
    
            $inactive_at = ($estado === 'inactivo') ? date('Y-m-d H:i:s') : null;
    
            if ($this->actionModel->updateClient($idcliente, $razonsocial, $nombrecomercial, $telefono, $email, $direccion, $contacto, $inactive_at)) {
                header('Location: ../../views/produccion/registrarClientes.php');
                exit();
            } else {
                header('Location: ../../views/produccion/registrarClientes.php?error=ErrorActualizacion');
                exit();
            }
        }
    }
    

    public function createOrdenProduccion() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['op'])) {
            $idcliente = $_POST['idcliente'];
            $op = $_POST['op'];
            $estilo = $_POST['estilo'];
            $division = $_POST['division'];
            $color = $_POST['color'];
            $fechainicio = $_POST['fechainicio'];
            $fechafin = $_POST['fechafin'];
    
            if ($this->actionModel->createOrdenProduccion($idcliente, $op, $estilo, $division, $color, $fechainicio, $fechafin)) {
                header('Location: ../../views/produccion/registrarProduccion.php');
                exit();
            } else {
                header('Location: ../../views/produccion/registrarProduccion.php?error=NombreYaExiste');
                exit();
            }
        }
    }
    
    
    public function showActions($cliente_id = null) {
        if ($cliente_id) {
            $actions = $this->actionModel->getActionsByClient($cliente_id);
        } else {
            $actions = $this->actionModel->getActions();
        }
        require '../../views/produccion/actions.php';
    }
    

    public function viewAction($id) {
        $action = $this->actionModel->getActionById($id);
        $secuencias = $this->actionModel->getSecuenciasByActionId($id);
        require '../../views/produccion/view.Action.php';
    }
    
    public function viewSecuencia($id) {
        $secuencia = $this->actionModel->getSecuenciaById($id);
        require '../../views/produccion/viewSecuencia.php'; 
    }    

    public function viewActionPDF($id) {
        $actionP = $this->actionModel->getActionByIdxPDF($id);
    
        $pdfs = $this->actionModel->getPDFByActionId($id);
    
        require '../../views/produccion/archivosPDF.php';
    }
    
    public function createSequence() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idop = $_POST['idop'];
            $idcliente = $_POST['idcliente']; // Recibimos el idcliente
            $fechaInicio = $_POST['sinicio'];
            $fechaFinal = $_POST['sfin'];
            $numSecuencia = isset($_POST['numSecuencia']) ? (int)$_POST['numSecuencia'] : 1;
            $idtalla = isset($_POST['idtalla']) ? (int)$_POST['idtalla'] : null;
            $cantidad = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 0;
    
            $detalleInsertado = $this->actionModel->insertDetalleOp($idop, $idtalla, $numSecuencia, $cantidad, $fechaInicio, $fechaFinal);
    
            if (!$detalleInsertado) {
                header("Location: ../../views/produccion/indexP.php?cliente_id=" . urlencode($idcliente) . "&error=DetalleNoInsertado");
                exit();
            }
    
            header("Location: ../../views/produccion/indexP.php?cliente_id=" . urlencode($idcliente));
            exit();
        }
    }
    
   public function createProduccion() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $iddetop = isset($_POST['iddetop']) ? (int)$_POST['iddetop'] : null;
        // Obtener y validar los datos del formulario
        $iddetop_operacion = isset($_POST['iddetop_operacion']) ? (int)$_POST['iddetop_operacion'] : null;
        $idpersona = isset($_POST['idpersona']) ? (int)$_POST['idpersona'] : null;
        $cantidadproducida = isset($_POST['cantidadproducida']) ? (int)$_POST['cantidadproducida'] : 0;
        $fecha = isset($_POST['fecha']) ? $_POST['fecha'] : date('Y-m-d H:i:s'); 

        // Validar que los datos esenciales estén presentes
        if ($iddetop_operacion === null || $idpersona === null || $cantidadproducida <= 0) {
             // Redirigir al usuario a la vista correspondiente
        header("Location: ../../views/produccion/indexP.php?action=viewSecuencia&iddetop=" . urlencode($iddetop));
        exit();
        }

        // Llamar al modelo para registrar la producción
        $produccionInsertada = $this->actionModel->createProduccion($iddetop_operacion, $idpersona, $cantidadproducida, $fecha);

        if (!$produccionInsertada) {
            header("Location: ../../views/produccion/indexP.php?action=viewSecuencia&iddetop=" . urlencode($iddetop));
            exit();
        }

        // Redirigir al usuario a la vista correspondiente
        header("Location: ../../views/produccion/indexP.php?action=viewSecuencia&iddetop=" . urlencode($iddetop));
        exit();
    }
    }

    
    public function addOperationToDetalle() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $iddetop = $_POST['iddetop'];
            $idcliente = $_POST['idcliente'];
            $operaciones = $_POST['operaciones']; // Operaciones seleccionadas como un array
            $cantidaO = $_POST['cantidaO']; 
    
            // Verifica si se seleccionaron operaciones
            if (!empty($operaciones)) {
                foreach ($operaciones as $idoperacion) {
                    $operacionInsertado = $this->actionModel->addOperationToDetalle($iddetop, $idoperacion, $cantidaO);
                }
    
                if ($operacionInsertado) {
                    header("Location: ../../views/produccion/indexP.php?cliente_id=" . urlencode($idcliente));
                    exit();
                }
            }
    
            // Si no se insertó o no se seleccionaron operaciones
            header("Location: ../../views/produccion/indexP.php?cliente_id=" . urlencode($idcliente));
            exit();
        }
    }
    
    public function updateOrdenProduccion() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idop = $_POST['idop'] ?? null;
            $op = $_POST['op'] ?? null;
            $estilo = $_POST['estilo'] ?? null;
            $division = $_POST['division'] ?? null;
            $color = $_POST['color'] ?? null;
            $fechainicio = $_POST['fechainicio'] ?? null;
            $fechafin = $_POST['fechafin'] ?? null;

            if ($idop && $op && $estilo && $division && $color && $fechainicio && $fechafin) {
                $result = $this->actionModel->updateOrdenProduccion($idop, $op, $estilo, $division, $color, $fechainicio, $fechafin);

                header('Content-Type: application/json');
                echo json_encode(['success' => $result]);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
            }
        }
    }

    public function updateDetalleProduccion() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $iddetop = $_POST['iddetop'] ?? null;
            $numSecuencia = $_POST['numSecuencia'] ?? null;
            $idtalla = $_POST['talla'] ?? null; // Aquí se espera que 'talla' ya sea el ID
            $cantidad = $_POST['cantidad'] ?? null;
            $sinicio = $_POST['sinicio'] ?? null;
            $sfin = $_POST['sfin'] ?? null;
    
            if ($iddetop && $numSecuencia && $idtalla && $cantidad && $sinicio && $sfin) {
                $result = $this->actionModel->updateDetalle($iddetop, $numSecuencia, $idtalla, $cantidad, $sinicio, $sfin);
    
                header('Content-Type: application/json');
                echo json_encode(['success' => $result]);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
            }
        }
    }
    
}
