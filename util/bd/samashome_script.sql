CREATE SCHEMA u929679314_samashome_db;

USE u929679314_samashome_db;

CREATE TABLE categorias (
	categoria VARCHAR(50) PRIMARY KEY,
    img_categoria VARCHAR(60)
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

CREATE TABLE ofertas(
    id_oferta INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(255),
    porcentaje INT
);

CREATE TABLE productos (
	id_producto INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
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
    id_oferta INT,
    FOREIGN KEY (categoria) REFERENCES categorias(categoria),
    FOREIGN KEY (id_proveedor) REFERENCES proveedores(id_proveedor),
    FOREIGN KEY (id_oferta) REFERENCES ofertas(id_oferta)
);

CREATE TABLE carrito (
    id_carrito INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    id_producto INT,
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto)
);

INSERT INTO categorias (categoria,img_categoria) VALUES ('Sofás',"sofas.jpg");
INSERT INTO categorias (categoria,img_categoria) VALUES ('Sillas',"sillas.jpg");
INSERT INTO categorias (categoria,img_categoria) VALUES ('Mesas',"mesas.jpg");
INSERT INTO categorias (categoria,img_categoria) VALUES ('Colchones',"colchones.jpg");
INSERT INTO categorias (categoria,img_categoria) VALUES ('Armarios',"armarios.jpg");

SELECT * FROM categorias;
SELECT * FROM usuarios;
SELECT * FROM proveedores;
SELECT * FROM productos;

DROP TABLE categorias;
DROP TABLE usuarios;
DROP TABLE proveedores;
DROP TABLE productos;

DELETE FROM usuarios WHERE id_usuario = 1;
DELETE FROM proveedores WHERE id_proveedor = 1;
DELETE FROM productos WHERE id_proveedor = 1;
DELETE FROM categorias WHERE categoria = "Sillas";