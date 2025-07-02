
CREATE DATABASE dbviajes;
USE dbviajes;

CREATE TABLE empresa (
    idEmpresa bigint AUTO_INCREMENT,
    nombre varchar (150),
    direccion varchar (150),
    PRIMARY KEY (idEmpresa)
    )
ENGINE = InnoDB DEFAULT CHARSET = utf8 AUTO_INCREMENT = 1;

CREATE TABLE persona (
                         id BIGINT AUTO_INCREMENT PRIMARY KEY,
                         nombre VARCHAR(30) NOT NULL,
                         apellido VARCHAR(30) NOT NULL
)ENGINE = InnoDB DEFAULT CHARSET = utf8 AUTO_INCREMENT = 1;


CREATE TABLE responsable (
    numeroEmpleado bigint,
    numeroLicencia bigint NOT NULL UNIQUE,
    PRIMARY KEY (numeroEmpleado),
    FOREIGN KEY (numeroEmpleado) REFERENCES persona(id)
                         ON DELETE RESTRICT
                         ON UPDATE RESTRICT
    )
ENGINE = InnoDB DEFAULT CHARSET = utf8 AUTO_INCREMENT = 1;

CREATE TABLE viaje (
    idViaje bigint AUTO_INCREMENT,
	destino varchar (150),
    cantMaxPasajeros int,
	idEmpresa bigint,
    numeroEmpleado bigint,
    importe float,
    PRIMARY KEY (idViaje),
    FOREIGN KEY (idEmpresa) REFERENCES empresa (idEmpresa)
    ON UPDATE RESTRICT
    ON DELETE RESTRICT,
	FOREIGN KEY (numeroEmpleado) REFERENCES responsable (numeroEmpleado)
    ON UPDATE RESTRICT
    ON DELETE RESTRICT
    )
ENGINE = InnoDB DEFAULT CHARSET = utf8 AUTO_INCREMENT = 1;

CREATE TABLE pasajero (
    id BIGINT,
	telefono int,
    PRIMARY KEY (id),
    numeroDocumento int UNIQUE,

        FOREIGN KEY (id) REFERENCES persona (id)
                      ON UPDATE RESTRICT
    ON DELETE RESTRICT
    )
ENGINE = InnoDB DEFAULT CHARSET = utf8;

CREATE TABLE pasajero_viaje (
    numeroDocumento int,
    idViaje bigint,
    PRIMARY KEY (idViaje, numeroDocumento),
    FOREIGN KEY (idViaje) REFERENCES viaje (idViaje)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT,
    FOREIGN KEY (numeroDocumento) REFERENCES pasajero (numeroDocumento)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT
)ENGINE = InnoDB DEFAULT CHARSET = utf8;

