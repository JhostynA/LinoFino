<?php

require_once __DIR__ . '/../../models/Conexion.php'; 

class OrdenesProduccion extends Conexion {

    private $pdo;

    public function __CONSTRUCT() {
        $this->pdo = parent::getConexion();
    }

    public function listarOrdenesPorProximidad(): array {
        try {
            $query = $this->pdo->prepare("CALL ListarOrdenesPorProximidad()");
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
}