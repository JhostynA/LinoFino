<?php

define('BASE_PATH', dirname(__DIR__)); 
require_once(BASE_PATH . '/Login.php');


class ActionModel {
    private $db;

    public function __construct() {
        $conexion = new Conexion();
        $this->db = $conexion->getConexion();
    }

    public function createClient($razonsocial, $nombrecomercial, $telefono, $email, $direccion, $contacto) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM clientes WHERE razonsocial = :razonsocial");
        $stmt->bindParam(':razonsocial', $razonsocial);
        $stmt->execute();
        $count = $stmt->fetchColumn();
    
        if ($count > 0) {
            return false;
        }
    
        $stmt = $this->db->prepare("
            INSERT INTO clientes (razonsocial, nombrecomercial, telefono, email, direccion, contacto) 
            VALUES (:razonsocial, :nombrecomercial, :telefono, :email, :direccion, :contacto)
        ");
        $stmt->bindParam(':razonsocial', $razonsocial);
        $stmt->bindParam(':nombrecomercial', $nombrecomercial);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':direccion', $direccion);
        $stmt->bindParam(':contacto', $contacto);
    
        return $stmt->execute();
    }
    
    
    public function updateClient($idcliente, $razonsocial, $nombrecomercial, $telefono, $email, $direccion, $contacto, $inactive_at) {
        $sql = "UPDATE clientes 
                SET razonsocial = :razonsocial,
                    nombrecomercial = :nombrecomercial,
                    telefono = :telefono,
                    email = :email,
                    direccion = :direccion,
                    contacto = :contacto,
                    inactive_at = :inactive_at
                WHERE idcliente = :idcliente";
        
        $stmt = $this->db->prepare($sql);
        
        $stmt->bindParam(':idcliente', $idcliente, PDO::PARAM_INT);
        $stmt->bindParam(':razonsocial', $razonsocial, PDO::PARAM_STR);
        $stmt->bindParam(':nombrecomercial', $nombrecomercial, PDO::PARAM_STR);
        $stmt->bindParam(':telefono', $telefono, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':direccion', $direccion, PDO::PARAM_STR);
        $stmt->bindParam(':contacto', $contacto, PDO::PARAM_STR);
        $stmt->bindParam(':inactive_at', $inactive_at, PDO::PARAM_STR); 
    
        return $stmt->execute();
    }
    
    public function getClientesActivos() {
        $stmt = $this->db->prepare("SELECT idcliente, nombrecomercial FROM clientes WHERE inactive_at IS NULL");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getClientes(){
        $stmt = $this->db->query("SELECT * FROM clientes ORDER BY inactive_at IS NULL DESC, fecha_creacion DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createOrdenProduccion($idcliente, $op, $estilo, $division, $color, $fechainicio, $fechafin) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM ordenesproduccion WHERE op = :op");
        $stmt->bindParam(':op', $op);
        $stmt->execute();
        $count = $stmt->fetchColumn();
    
        if ($count > 0) {
            return false;
        }
    
        $stmt = $this->db->prepare("
            INSERT INTO ordenesproduccion (idcliente, op, estilo, division, color, fechainicio, fechafin) 
            VALUES (:idcliente, :op, :estilo, :division, :color, :fechainicio, :fechafin)
        ");
    
        $stmt->bindParam(':idcliente', $idcliente);
        $stmt->bindParam(':op', $op);
        $stmt->bindParam(':estilo', $estilo);
        $stmt->bindParam(':division', $division);
        $stmt->bindParam(':color', $color);
        $stmt->bindParam(':fechainicio', $fechainicio);
        $stmt->bindParam(':fechafin', $fechafin);
    
        return $stmt->execute();
    }
    
    public function getTallas() {
        $query = "SELECT idtalla, talla FROM tallas";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getPersonasActivas() {
        $query = "SELECT idpersona, apellidos, nombres FROM personas WHERE fechabaja IS NULL";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOperaciones() {
        $query = "SELECT idoperacion, operacion FROM operaciones";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOperacionesSeleccionadas($iddetop) {
        $query = "
            SELECT idoperacion 
            FROM detalleop_operaciones 
            WHERE iddetop = :iddetop
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':iddetop', $iddetop, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN); // Devuelve un array con los `idoperacion`
    }

    public function getActions() {
        $stmt = $this->db->query("SELECT * FROM ordenesproduccion ORDER BY created_at DESC;");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getActionsByClient($cliente_id) {
        $stmt = $this->db->prepare("SELECT * FROM ordenesproduccion WHERE idcliente = ? ORDER BY created_at DESC");
        $stmt->execute([$cliente_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getActionById($id) {
        $stmt = $this->db->prepare("SELECT * FROM actions WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getDetalleByOP($idop) {
        $query = "
            SELECT 
                detalleop.iddetop,
                detalleop.numSecuencia,
                detalleop.cantidad,
                detalleop.sinicio,
                detalleop.sfin,
                tallas.talla
            FROM 
                detalleop
            INNER JOIN 
                tallas
            ON 
                detalleop.idtalla = tallas.idtalla
            WHERE 
                detalleop.idop = :idop
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':idop', $idop);
        $stmt->execute();
    
        $sequences = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!$sequences) {
            error_log("No se encontraron detalles para la OP con ID: $idop");
        }
        return $sequences;
    }

    public function getProduccionByDOP($iddetop) {
        $query = "
            SELECT 
                produccion.idproduccion,
                produccion.cantidadproducida,
                produccion.fecha,
                produccion.pagado,
                produccion.fechapagopersona,
                personas.nombres AS nombrePersona,
                personas.apellidos AS apellidos,
                operaciones.operacion AS tipoOperacion
            FROM 
                produccion
            INNER JOIN 
                detalleop_operaciones
            ON 
                produccion.iddetop_operacion = detalleop_operaciones.id
            INNER JOIN 
                detalleop
            ON 
                detalleop_operaciones.iddetop = detalleop.iddetop
            INNER JOIN 
                personas
            ON 
                produccion.idpersona = personas.idpersona
            INNER JOIN 
                operaciones
            ON 
                detalleop_operaciones.idoperacion = operaciones.idoperacion
            WHERE 
                detalleop.iddetop = :iddetop
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':iddetop', $iddetop, PDO::PARAM_INT);
        $stmt->execute();
    
        $producciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!$producciones) {
            error_log("No se encontraron producciones para la OP con ID: $iddetop");
        }
        return $producciones;
    }
    
    public function getSecuenciaById($id) {
        $stmt = $this->db->prepare("SELECT * FROM detalleop WHERE iddetop = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function insertDetalleOp($idop, $idtalla, $numSecuencia, $cantidad, $fechaInicio, $fechaFinal) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM detalleop WHERE idop = :idop AND numSecuencia = :numSecuencia AND idtalla = :idtalla");
        $stmt->bindParam(':idop', $idop);
        $stmt->bindParam(':numSecuencia', $numSecuencia);
        $stmt->bindParam(':idtalla', $idtalla);
        $stmt->execute();
        $count = $stmt->fetchColumn();
    
        if ($count > 0) {
            return false; 
        }
    
        $stmt = $this->db->prepare("
            INSERT INTO detalleop (idop, idtalla, numSecuencia, cantidad, sinicio, sfin) 
            VALUES (:idop, :idtalla, :numSecuencia, :cantidad, :sinicio, :sfin)
        ");
        $stmt->bindParam(':idop', $idop);
        $stmt->bindParam(':idtalla', $idtalla);
        $stmt->bindParam(':numSecuencia', $numSecuencia);
        $stmt->bindParam(':cantidad', $cantidad);
        $stmt->bindParam(':sinicio', $fechaInicio);
        $stmt->bindParam(':sfin', $fechaFinal);
    
        if ($stmt->execute()) {
            return $this->db->lastInsertId(); 
        }
    
        return false; 
    }

    public function createProduccion($iddetop_operacion, $idpersona, $cantidadproducida, $fecha) {
        try {
            // 1. Verificar la cantidad disponible en la operación seleccionada
            $query = "SELECT cantidaO FROM detalleop_operaciones WHERE id = :iddetop_operacion";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':iddetop_operacion', $iddetop_operacion, PDO::PARAM_INT);
            $stmt->execute();
            $operacion = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if (!$operacion) {
                throw new Exception("Operación no encontrada.");
            }
    
            $cantidadDisponible = $operacion['cantidaO'];
    
            // 2. Validar que la cantidad producida no exceda la cantidad disponible
            if ($cantidadproducida > $cantidadDisponible) {
                throw new Exception("La cantidad producida excede la cantidad disponible.");
            }
    
            // 3. Insertar la nueva producción
            $stmt = $this->db->prepare("
                INSERT INTO produccion (iddetop_operacion, idpersona, cantidadproducida, fecha) 
                VALUES (:iddetop_operacion, :idpersona, :cantidadproducida, :fecha)
            ");
            $stmt->bindParam(':iddetop_operacion', $iddetop_operacion, PDO::PARAM_INT);
            $stmt->bindParam(':idpersona', $idpersona, PDO::PARAM_INT);
            $stmt->bindParam(':cantidadproducida', $cantidadproducida, PDO::PARAM_INT);
            $stmt->bindParam(':fecha', $fecha);
    
            if (!$stmt->execute()) {
                throw new Exception("Error al insertar la producción.");
            }
    
            // 4. Actualizar la cantidad disponible en la operación seleccionada
            $nuevaCantidad = $cantidadDisponible - $cantidadproducida;
            $stmt = $this->db->prepare("
                UPDATE detalleop_operaciones 
                SET cantidaO = :nuevaCantidad 
                WHERE id = :iddetop_operacion
            ");
            $stmt->bindParam(':nuevaCantidad', $nuevaCantidad, PDO::PARAM_INT);
            $stmt->bindParam(':iddetop_operacion', $iddetop_operacion, PDO::PARAM_INT);
    
            if (!$stmt->execute()) {
                throw new Exception("Error al actualizar la cantidad disponible.");
            }
    
            // Retornar el ID de la nueva producción insertada
            return $this->db->lastInsertId();
        } catch (Exception $e) {
            // Manejo de errores (puedes implementar logging si lo necesitas)
            error_log("Error en createProduccion: " . $e->getMessage());
            return false;
        }
    }

    public function getLastInsertedSequenceId() {
        return $this->db->lastInsertId();
    }

    public function getActionByIdxPDF($id) {
        $stmt = $this->db->prepare("SELECT * FROM ordenesproduccion WHERE idop = :idop");
        $stmt->bindParam(':idop', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getPDFByActionId($actionId) {
        $query = "SELECT * FROM detalleop WHERE iddetop = :iddetop";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':iddetop', $iddetop);
        $stmt->execute();
    
        $sequences = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!$sequences) {
            error_log("No se encontraron secuencias para la OP con ID: $iddetop");
        }
        return $sequences;
    }

    public function actualizarInventario($actionId) {
        $stmt = $this->db->prepare("CALL actualizar_inventario(:actionId)");
        $stmt->bindParam(':actionId', $actionId, PDO::PARAM_INT);
        return $stmt->execute();
    }    
    
    public function addOperationToDetalle($iddetop, $idoperacion, $cantidaO) {
        $sql = "INSERT INTO detalleop_operaciones (iddetop, idoperacion, cantidaO) 
                VALUES (:iddetop, :idoperacion, :cantidaO)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':iddetop', $iddetop, PDO::PARAM_INT);
        $stmt->bindParam(':idoperacion', $idoperacion, PDO::PARAM_INT);
        $stmt->bindParam(':cantidaO', $cantidaO, PDO::PARAM_INT);
        $stmt->execute();
    }
    
    public function getOperacionesByDetalleOp($iddetop){
        $query = "
            SELECT doo.id AS iddetop_operacion, op.idoperacion, op.operacion, doo.cantidaO
            FROM detalleop_operaciones doo
            INNER JOIN operaciones op ON doo.idoperacion = op.idoperacion
            WHERE doo.iddetop = :iddetop
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':iddetop', $iddetop, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateOrdenProduccion($idop, $op, $estilo, $division, $color, $fechainicio, $fechafin) {
        $sql = "UPDATE ordenesproduccion 
                SET op = ?, estilo = ?, division = ?, color = ?, fechainicio = ?, fechafin = ? 
                WHERE idop = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$op, $estilo, $division, $color, $fechainicio, $fechafin, $idop]);
    }

    public function updateDetalle($iddetop, $numSecuencia, $idtalla, $cantidad, $sinicio, $sfin) {
        $query = "UPDATE detalleop SET numSecuencia = ?, idtalla = ?, cantidad = ?, sinicio = ?, sfin = ? WHERE iddetop = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$numSecuencia, $idtalla, $cantidad, $sinicio, $sfin, $iddetop]);
    }

    public function getDetalleConPrendasRealizadas($idop) {
        $query = "
            SELECT d.*, 
                   t.talla,
                   COALESCE(MIN(p.cantidadproducida), 0) AS prendas_realizadas
            FROM detalleop d
            LEFT JOIN tallas t ON d.idtalla = t.idtalla
            LEFT JOIN detalleop_operaciones doo ON d.iddetop = doo.iddetop
            LEFT JOIN produccion p ON doo.id = p.iddetop_operacion
            WHERE d.idop = ?
            GROUP BY d.iddetop
        ";
    
        $stmt = $this->db->prepare($query);
        $stmt->execute([$idop]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
      
    
}

