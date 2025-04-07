CREATE SCHEMA prueba_samas;

USE prueba_samas;

DROP TABLE categorias;
DROP TABLE usuarios;
DROP TABLE proveedores;
DROP TABLE productos;

SELECT * FROM categorias;
SELECT * FROM usuarios;
SELECT * FROM proveedores;
SELECT * FROM productos;

INSERT INTO categorias (categoria) VALUES ('Electrónica');

INSERT INTO usuarios (email_usuario, nombre_usuario, contrasena_usuario, suscripcion, foto_usuario) VALUES
    ('mva0025', 'Manuva', 'SAMAShome1234', 'Premium', 'usuario-estandar.png');

INSERT INTO proveedores (email_proveedor, nombre_proveedor, contrasena_proveedor, foto_proveedor) VALUES
	('mueblesbuffalo@gmail.com', 'Muebles Buffalo', 'SAMAShome1234', 'usuario-estandar.png');
    
INSERT INTO productos (nombre, precio, categoria, stock, descripcion, largo, ancho, alto, id_proveedor, imagen) VALUES
('Smartphone', 49.99, 'Electrónica', 10, 'Teléfono inteligente de última generación', 15, 7, 1, 1, 'sillon.jpg');

DELETE FROM usuarios WHERE id_usuario = 1;

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
    email_usuario VARCHAR(50),
    nombre_usuario VARCHAR(50),
    contrasena_usuario VARCHAR(120),
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