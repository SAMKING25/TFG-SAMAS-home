CREATE SCHEMA prueba_samas_db;
USE prueba_samas_db;

CREATE TABLE categorias (
	categoria VARCHAR(30) PRIMARY KEY
);

CREATE TABLE usuarios (
    id_usuario INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    email VARCHAR(50),
    nombre VARCHAR(50),
    contrasena VARCHAR(120),
    suscripcion VARCHAR(20),
    proveedor BOOLEAN
);

CREATE TABLE productos (
	id_producto INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50),
    precio NUMERIC(6,2),
    categoria VARCHAR(30),
    stock INT DEFAULT 0,
    descripcion VARCHAR(255),
    largo INT,
    ancho INT,
    alto INT,
    id_proveedor INT,
    imagen VARCHAR(60),
    FOREIGN KEY (categoria) REFERENCES categorias(categoria),
    FOREIGN KEY (id_proveedor) REFERENCES usuarios(id_usuario)
);