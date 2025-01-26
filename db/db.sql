CREATE DATABASE linoFino;
USE linoFino;

CREATE TABLE operaciones
(
	idoperacion		INT PRIMARY KEY AUTO_INCREMENT,
    operacion		VARCHAR(50),
    precio			DECIMAL(6,3),
    CONSTRAINT uk_operacion_ope UNIQUE (operacion)
);

INSERT INTO operaciones (operacion,precio) VALUES
	('Cerrado de cuello',0.03),
    ('Bordeado de Cuello',0.07),
    ('Basta manga corta',0.12),
    ('Union hombro',0.07),
    ('Pegado de cuello',0.12),
    ('Recubierto de cuello',0.08),
    ('Pegado de tapete',0.12),
    ('Pegado de manga corta',0.12),
    ('Cerrado de costado',0.13),
    ('Atraque de puño',0.09),
    ('Basta de faldon',0.14),
    ('Parche',0.1),
    ('Limpieza',0.1),
    ('Inspeccion',0.12),
    ('Etiqueta',0.03),
    ('Costos fijos',0.336);

CREATE TABLE personas (
    idpersona 		INT PRIMARY KEY AUTO_INCREMENT,
    apellidos 		VARCHAR(50) 	NOT NULL,
    nombres 		VARCHAR(50) 	NOT NULL,
    telefono        CHAR(9)         NOT NULL,
    tipodoc         CHAR(3)         NOT NULL,
    numdoc          CHAR(15)        NOT NULL,
    fecharegistro 	DATETIME 		NOT NULL DEFAULT NOW(),
    fechabaja 		DATETIME 		NULL,
    CONSTRAINT chk_tipodoc_per CHECK (tipodoc in ('DNI', 'PST', 'CEX'))
);

INSERT INTO personas (apellidos, nombres, telefono, tipodoc, numdoc)
VALUES 
('Aburto Acevedo', 'Jhostyn Alberto', '987654321', 'DNI', '12345678'),
('Quispe Huamán', 'Juan Carlos', '987654322', 'PST', '23456789'),
('Gonzales Cahuana', 'María Elena', '987654323', 'CEX', '34567890'),
('Soto Yupanqui', 'José Antonio', '987654324', 'DNI', '45678901'),
('Rojas Chávez', 'Ana Lucía', '987654325', 'PST', '56789012'),
('Flores Pachacutec', 'Luis Alberto', '987654326', 'CEX', '67890123');

CREATE TABLE colaboradores (
    idcolaboradores INT PRIMARY KEY AUTO_INCREMENT,
    idpersona 		INT,
    nomusuario 		VARCHAR(50) NOT NULL,
    passusuario 	VARCHAR(60) NOT NULL,
    fecharegistro 	DATETIME 	NOT NULL DEFAULT NOW(),
    fechabaja 		DATETIME 	NULL,
    CONSTRAINT uk_nomusuario_per UNIQUE (nomusuario),
    FOREIGN KEY (idpersona) REFERENCES personas(idpersona)
);

CREATE TABLE clientes
(
	idcliente		INT AUTO_INCREMENT PRIMARY KEY,
    razonsocial		VARCHAR(150) 	NOT NULL,
	nombrecomercial	VARCHAR(50) 	NOT NULL,
    telefono 		VARCHAR(20) 	NULL,
    email 			VARCHAR(50) 	NULL, 
    direccion		VARCHAR(200) 	NULL,
    contacto		VARCHAR(100) 	NULL,
    fecha_creacion  DATE DEFAULT (CURRENT_TIMESTAMP),
    inactive_at 	DATETIME 		NULL
);
CREATE TABLE ordenesproduccion (
    idop 		INT AUTO_INCREMENT PRIMARY KEY,
    idcliente 	INT,
    op 			VARCHAR(50) NOT NULL,
    estilo 		VARCHAR(80) NOT NULL,
    division 	VARCHAR(20) NOT NULL,
    color 		VARCHAR(20) NOT NULL,
    fechainicio DATE	    NOT NULL,
    fechafin 	DATE		NOT NULL,
    created_at  DATETIME	NOT NULL DEFAULT NOW(),
    FOREIGN KEY (idcliente) REFERENCES clientes(idcliente)
);


CREATE TABLE tallas (
    idtalla INT AUTO_INCREMENT PRIMARY KEY,
    talla 	CHAR(10),
    CONSTRAINT uk_talla UNIQUE(talla)
);
    
    
   INSERT INTO tallas(talla)
	VALUE
        ('2T'),
        ('3T'),
        ('4T'),
        ('5T'),
		('S'),
        ('M'),
        ('L'),
        ('XL'),
        ('XXL'),
        ('XXXL');

CREATE TABLE detalleop (
    iddetop 		INT AUTO_INCREMENT PRIMARY KEY,
    idop 			INT, 
    idtalla 		INT,
    numSecuencia	INT NOT NULL,
    cantidad 		INT NOT NULL,
    sinicio 		DATE NOT NULL,
    sfin			DATE NOT NULL,
    FOREIGN KEY (idtalla) REFERENCES tallas(idtalla),
    FOREIGN KEY (idop) REFERENCES ordenesproduccion(idop)
);

CREATE TABLE detalleop_operaciones (
    id 				INT AUTO_INCREMENT PRIMARY KEY,
    iddetop 		INT,
    idoperacion 	INT,
    cantidaO		INT,
    FOREIGN KEY (iddetop) REFERENCES detalleop(iddetop),
    FOREIGN KEY (idoperacion) REFERENCES operaciones(idoperacion)
);

CREATE TABLE produccion (
    idproduccion 		INT AUTO_INCREMENT PRIMARY KEY,
    iddetop_operacion   INT,
    idpersona			INT,
    cantidadproducida 	INT			NOT NULL,
    fecha 				DATE	 	DEFAULT NOW(),
    pagado 				BOOLEAN 	DEFAULT FALSE,
    fechapagopersona	DATE		NULL,
    FOREIGN KEY (iddetop_operacion) REFERENCES detalleop_operaciones(id),
    FOREIGN KEY (idpersona) REFERENCES personas(idpersona)
);

CREATE TABLE pdf_files (
    id INT AUTO_INCREMENT PRIMARY KEY,
    idop INT NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    FOREIGN KEY (idop) REFERENCES ordenesproduccion(idop)
);


DELIMITER $$
CREATE PROCEDURE spu_colaboradores_login(IN _nomusuario VARCHAR(50))
BEGIN
	SELECT
		COL.idcolaboradores,
        PER.apellidos, PER.nombres,
        COL.nomusuario, COL.passusuario
		FROM colaboradores COL
        INNER JOIN personas PER ON PER.idpersona = COL.idcolaboradores
        WHERE COL.nomusuario = _nomusuario AND COL.fechabaja IS NULL;
END $$
DELIMITER $$

CREATE PROCEDURE spu_colaboradores_registrar
(
	IN _idpersona		INT,
    IN _nomusuario  	VARCHAR(50),
    IN _passusuario 	VARCHAR(60)
)
BEGIN
	INSERT INTO colaboradores (idpersona, nomusuario, passusuario) VALUES
		(_idpersona, _nomusuario, _passusuario);
	SELECT @@last_insert_id 'idcolaboradores';
END $$
DELIMITER $$

CALL spu_colaboradores_registrar(1,'JhostynA','$2y$10$shfcJOApvH8mxR/vm4PupOQ9b5v9vGBXMQnfwDKeJhbOuvWurw/qi');

DELIMITER $$
CREATE PROCEDURE VerificarOperacion
(
	IN _operacion	VARCHAR(50), 	
    IN _precio		DECIMAL(6,3)
    )
BEGIN
  SELECT COUNT(*) AS existe
  FROM operaciones
  WHERE operacion = _operacion AND precio = _precio;
END $$

DELIMITER $$
CREATE PROCEDURE VerificarPersona
(
	IN _apellidos		VARCHAR(50), 	
    IN _nombres			VARCHAR(50),
    IN _telefono        CHAR(9),
    IN _tipodoc         CHAR(3),
    IN _numdoc          CHAR(15)
    )
BEGIN
  SELECT COUNT(*) AS existe
  FROM personas
  WHERE  apellidos = _apellidos AND nombres = _nombres AND telefono = _telefono AND tipodoc = _tipodoc AND numdoc = _numdoc;
END $$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE buscarPersonas(IN searchTerm VARCHAR(255))
BEGIN
    SELECT idpersona, nombres, apellidos
    FROM personas
    WHERE nombres LIKE CONCAT('%', searchTerm, '%')
       OR apellidos LIKE CONCAT('%', searchTerm, '%');
END $$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE buscarOperaciones(IN searchTerm VARCHAR(255))
BEGIN
    SELECT idoperacion, operacion, precio
    FROM operaciones
    WHERE operacion LIKE CONCAT('%', searchTerm, '%');
END $$
DELIMITER ;

CREATE TABLE modalidades (
    idmodalidad 	INT AUTO_INCREMENT PRIMARY KEY,
    modalidad 		VARCHAR(50) NOT NULL,
    CONSTRAINT chk_modalidad_mod CHECK (modalidad in ('YAPE', 'PLIN', 'EFECTIVO')),
    CONSTRAINT uk_modalidad UNIQUE(modalidad)
);

INSERT INTO modalidades (modalidad)
    VALUES 
        ('YAPE'), 
        ('PLIN'), 
        ('EFECTIVO');

CREATE TABLE pagos (
    idpago 			INT AUTO_INCREMENT PRIMARY KEY,
    idmodalidad 	INT,
    idpersona 		INT,
    fecha 			DATE NOT NULL,
    totalpago 		DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (idmodalidad) REFERENCES modalidades(idmodalidad),
    FOREIGN KEY (idpersona) REFERENCES personas(idpersona)
);


DELIMITER $$
CREATE PROCEDURE listarPagosPorBusqueda(
    IN searchTerm VARCHAR(100)
)
BEGIN
    SELECT 
        CONCAT(p.apellidos, ' ', p.nombres) AS trabajador,
        op.op AS orden_produccion,
        dop.numSecuencia AS secuencia,
        o.operacion AS operacion,
        pg.fecha AS fecha_pago,
        m.modalidad AS modalidad,
        IFNULL(pg.totalpago, 0) AS monto_pagado     
    FROM personas p
    LEFT JOIN pagos pg ON p.idpersona = pg.idpersona
    LEFT JOIN produccion prod ON p.idpersona = prod.idpersona
    LEFT JOIN detalleop_operaciones dopo ON prod.iddetop_operacion = dopo.id
    LEFT JOIN detalleop dop ON dopo.iddetop = dop.iddetop
    LEFT JOIN ordenesproduccion op ON dop.idop = op.idop
    LEFT JOIN operaciones o ON dopo.idoperacion = o.idoperacion
    LEFT JOIN modalidades m ON pg.idmodalidad = m.idmodalidad
    WHERE 
        CONCAT(p.apellidos, ' ', p.nombres) LIKE CONCAT('%', searchTerm, '%');
END $$ 
DELIMITER ;



DELIMITER $$
CREATE PROCEDURE listarPagosPorFecha(
    IN fechaInicio DATE,
    IN fechaFin DATE
)
BEGIN
    SELECT 
        CONCAT(p.apellidos, ' ', p.nombres) AS trabajador,
        op.op AS orden_produccion,
        dop.numSecuencia AS secuencia,
        o.operacion AS operacion,
        pg.fecha AS fecha_pago,
        m.modalidad AS modalidad,
        IFNULL(pg.totalpago, 0) AS monto_pagado
    FROM pagos pg
    INNER JOIN personas p ON pg.idpersona = p.idpersona
    LEFT JOIN produccion prod ON p.idpersona = prod.idpersona
    LEFT JOIN detalleop_operaciones dopo ON prod.iddetop_operacion = dopo.id
    LEFT JOIN detalleop dop ON dopo.iddetop = dop.iddetop
    LEFT JOIN ordenesproduccion op ON dop.idop = op.idop
    LEFT JOIN operaciones o ON dopo.idoperacion = o.idoperacion
    LEFT JOIN modalidades m ON pg.idmodalidad = m.idmodalidad
    WHERE pg.fecha BETWEEN fechaInicio AND fechaFin
    ORDER BY pg.fecha ASC;
END $$
DELIMITER ;

