<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// tu código existente abajo...
require_once '../../controllers/pagos/pagosControllers.php';

// Asegurarte de que el tipo de respuesta sea JSON
header('Content-Type: application/json');

// Validar el método de la solicitud
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar los datos recibidos
   if (!isset($_POST['paymentMethod'], $_POST['idpersona'], $_POST['paymentDate'], $_POST['amountPaid'], $_POST['idproduccion'])) {
        echo json_encode(['success' => false, 'message' => 'Datos incompletos.']);
        exit;
    }

    $idModalidad = $_POST['paymentMethod'];
    $idPersona = $_POST['idpersona'];
    $fecha = $_POST['paymentDate'];
    $totalPago = $_POST['amountPaid'];
    $idProduccion = $_POST['idproduccion'];
    try {
        // Crear instancia del controlador y registrar el pago
        $pagosController = new PagosController();
        $response = $pagosController->registrarPago($idModalidad, $idPersona, $fecha, $totalPago, $idProduccion);

        // Asegúrate de que $response es un array válido
        echo json_encode($response);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error del servidor: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
}
