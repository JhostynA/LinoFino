<?php
require_once '../../models/Conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['searchTerm'])) {
    $searchTerm = $_GET['searchTerm'];
    $action = $_GET['action'] ?? 'historial';
    $conexion = (new Conexion())->getConexion();

    if ($action === 'sugerencias') {
        // Solo nombres únicos para el buscador
        $stmt = $conexion->prepare("CALL buscarTrabajadores(:searchTerm)");
    } else {
        // Historial completo al hacer clic en Buscar
        $stmt = $conexion->prepare("CALL listarPagosPorBusqueda(:searchTerm)");
    }

    $stmt->bindValue(':searchTerm', $searchTerm, PDO::PARAM_STR);
    $stmt->execute();
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($resultados);
}
?>