<?php

require_once '../../models/pagos/pagosModels.php';


class PagosController {
    private $pagosModel;

    public function __construct() {
        $this->pagosModel = new PagosModels();
    }

    public function getClientesActivos() {
        return $this->pagosModel->getClientesActivos();
    }

    public function getOPByCliente($idcliente) {
        return $this->pagosModel->getOPByCliente($idcliente);
    }

    public function handleAjaxRequest() {
        if (isset($_POST['idcliente'])) {
            $idcliente = intval($_POST['idcliente']);
            $ordenes = $this->getOPByCliente($idcliente);
            echo json_encode($ordenes);
        } else {
            echo json_encode([]);
        }
    }

    public function getNSByOP($idop) {
        return $this->pagosModel->getNSByOP($idop);
    }

    public function handleAjaxRequestNS() {
        if (isset($_POST['idop'])) {
            $idop = intval($_POST['idop']);
            $ordenes = $this->getNSByOP($idop);
            echo json_encode($ordenes);
        } else {
            echo json_encode([]);
        }
    }

    public function gePrSByDOP($iddetop) {
        return $this->pagosModel->gePrSByDOP($iddetop);
    }


    public function handleAjaxRequestPr() {
        if (isset($_POST['iddetop'])) {
            $iddetop = intval($_POST['iddetop']);
            $ordenes = $this->gePrSByDOP($iddetop);
            echo json_encode($ordenes);
        } else {
            echo json_encode([]);
        }
    }

    public function getProduccionByPersona($idpersona){
        return $this->pagosModel->getProduccionByPersona($idpersona);
    }

    public function registrarPago($idModalidad, $idPersona, $fecha, $totalPago, $idProduccion) {
        return $this->pagosModel->registrarPago($idModalidad, $idPersona, $fecha, $totalPago, $idProduccion);
    }

    

}