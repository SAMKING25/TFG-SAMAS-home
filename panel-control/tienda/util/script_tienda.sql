CREATE SCHEMA prueba_samas;

USE prueba_samas;

DROP TABLE categorias;
DROP TABLE usuarios;
DROP TABLE proveedores;
DROP TABLE productos;

INSERT INTO categorias (categoria) VALUES ('Electrónica');

INSERT INTO proveedores (email_proveedor, nombre_proveedor, contrasena_proveedor, foto_proveedor) VALUES
('mueblesbuffalo@gmail.com', 'Muebles Buffalo', 'SAMAShome1234', 'usuario-estandar.png');
    
INSERT INTO productos (nombre, precio, categoria, stock, descripcion, largo, ancho, alto, id_proveedor, imagen) VALUES
('Smartphone', 49.99, 'Electrónica', 10, 'Teléfono inteligente de última generación', 15, 7, 1, 1, 'sillon.jpg');

INSERT INTO categorias (categoria) VALUES ('Sofás');
INSERT INTO categorias (categoria) VALUES ('Sillas');
INSERT INTO categorias (categoria) VALUES ('Mesas');
INSERT INTO categorias (categoria) VALUES ('Colchones');
INSERT INTO categorias (categoria) VALUES ('Armarios');

CREATE TABLE categorias (
categoria VARCHAR(50) PRIMARY KEY
);

CREATE TABLE usuarios (
id_usuario INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    email VARCHAR(50),
    nombre VARCHAR(50),
    contrasena VARCHAR(120),
    suscripcion VARCHAR(20),
    foto_usuario VARCHAR(60)
);

CREATE TABLE proveedores (
id_proveedor INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    email_proveedor VARCHAR(50),
    nombre_proveedor VARCHAR(50),
    contrasena_proveedor VARCHAR(120),
    foto_proveedor VARCHAR(60)
);

CREATE TABLE productos (
id_producto INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(50),
    precio NUMERIC(4,2),
    categoria VARCHAR(30),
    stock INT DEFAULT 0,
    descripcion VARCHAR(255),
    largo INT,
    ancho INT,
    alto INT,
    id_proveedor INT,
    imagen VARCHAR(60),
    FOREIGN KEY (categoria) REFERENCES categorias(categoria),
    FOREIGN KEY (id_proveedor) REFERENCES proveedores(id_proveedor)
);

CREATE TABLE productos_carrito (
id_carrito INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	id_producto INT,
    id_usuario INT,
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);