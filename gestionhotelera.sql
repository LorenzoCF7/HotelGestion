create database GranDescanso;
use GranDescanso;

-- Tabla de Habitaciones
CREATE TABLE Habitaciones (
    id_habitacion INT PRIMARY KEY AUTO_INCREMENT,
    numero INT UNIQUE NOT NULL,
    tipo ENUM('Sencilla','Doble','Suite') NOT NULL,
    precio_base DECIMAL(10,2) NOT NULL,
    estado_limpieza ENUM('Limpia','Sucia','En Limpieza') DEFAULT 'Sucia'
);

-- Tabla de Huéspedes
CREATE TABLE Huespedes (
    id_huesped INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    documento_identidad VARCHAR(20) NOT NULL
);

-- Tabla de Reservas
CREATE TABLE Reservas (
    id_reserva INT PRIMARY KEY AUTO_INCREMENT,
    id_huesped INT NOT NULL,
    id_habitacion INT NOT NULL,
    fecha_llegada DATE NOT NULL,
    fecha_salida DATE NOT NULL,
    precio_total DECIMAL(10,2) NOT NULL,
    estado ENUM('Pendiente','Confirmada','Cancelada') DEFAULT 'Pendiente',
    fecha_reserva DATE,
    FOREIGN KEY (id_huesped) REFERENCES Huespedes(id_huesped),
    FOREIGN KEY (id_habitacion) REFERENCES Habitaciones(id_habitacion),
    CONSTRAINT chk_fecha CHECK (fecha_salida > fecha_llegada)
);

-- Tabla de Tareas de Mantenimiento
CREATE TABLE Tareas_Mantenimiento (
    id_tarea INT PRIMARY KEY AUTO_INCREMENT,
    id_habitacion INT NOT NULL,
    descripcion VARCHAR(255) NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE,
    FOREIGN KEY (id_habitacion) REFERENCES Habitaciones(id_habitacion)
);

DELIMITER $$

CREATE TRIGGER verificar_reserva BEFORE INSERT ON Reservas
FOR EACH ROW
BEGIN
    DECLARE conflict INT;

    SELECT COUNT(*) INTO conflict
    FROM Reservas
    WHERE id_habitacion = NEW.id_habitacion
      AND estado = 'Confirmada'
      AND NOT (NEW.fecha_salida <= fecha_llegada OR NEW.fecha_llegada >= fecha_salida);

    IF conflict > 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La habitación ya está reservada para esas fechas.';
    END IF;
END$$

DELIMITER ;

DELIMITER $$

CREATE TRIGGER verificar_mantenimiento BEFORE INSERT ON Reservas
FOR EACH ROW
BEGIN
    DECLARE mantenimiento_activo INT;

    SELECT COUNT(*) INTO mantenimiento_activo
    FROM Tareas_Mantenimiento
    WHERE id_habitacion = NEW.id_habitacion
      AND fecha_inicio <= NEW.fecha_salida
      AND (fecha_fin IS NULL OR fecha_fin >= NEW.fecha_llegada);

    IF mantenimiento_activo > 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La habitación tiene mantenimiento activo en esas fechas.';
    END IF;
END$$

DELIMITER ;

ALTER TABLE Reservas
DROP CONSTRAINT chk_fecha;