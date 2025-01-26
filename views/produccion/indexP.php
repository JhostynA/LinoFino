<?php

require '../../models/produccion/ActionModel.php';
require '../../controllers/produccion/ActionController.php';

$actionModel = new ActionModel();
$actionController = new ActionController($actionModel);

$cliente_id = isset($_GET['cliente_id']) ? (int) $_GET['cliente_id'] : null;

if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'createOrdenProduccion':
            $actionController->createOrdenProduccion();
            break;
        case 'view':
            if (isset($_GET['id'])) {
                $actionController->viewAction($_GET['id']);
            }
            break;
        case 'viewSecuencia':
            if (isset($_GET['iddetop'])) {
                $actionController->viewSecuencia($_GET['iddetop']);
            }
            break;
        case 'viewPDF':
            if (isset($_GET['id'])) {
                $actionController->viewActionPDF($_GET['id']);
            }
            break;
        case 'createSequence':
            $actionController->createSequence();
            break;
        case 'createClientAction':
            $actionController->createClientAction();
            break;
        case 'updateClientAction':
            $actionController->updateClientAction();
            break;
        case 'createProduccion':
            $actionController->createProduccion();
            break;
        case 'updateOperations':
            $actionController->addOperationToDetalle();
            break;
        case 'updateOrdenProduccion':
            $actionController->updateOrdenProduccion();
            break;
        case 'updateDetalleProduccion':
            $actionController->updateDetalleProduccion();
            break;
        default:
            echo "Acción no válida.";
            break;
    }
} else {
    $actionController->showActions($cliente_id);
}
