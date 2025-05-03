CREATE SCHEMA u929679314_samashome_db;
USE u929679314_samashome_db;
DROP SCHEMA u929679314_samashome_db;

CREATE TABLE categorias (
	nombre_categoria VARCHAR(50) PRIMARY KEY,
    img_categoria VARCHAR(60)
);

CREATE TABLE suscripciones (
	id_suscripcion INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(50),
    precio NUMERIC(4,2),
    max_usos_plano INT
);

CREATE TABLE usuarios (
	id_usuario INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    email_usuario VARCHAR(50) UNIQUE,
    nombre_usuario VARCHAR(50),
    contrasena_usuario VARCHAR(120),
    id_suscripcion INT,
    img_usuario VARCHAR(60),
    FOREIGN KEY (id_suscripcion) REFERENCES suscripciones(id_suscripcion)
);

CREATE TABLE proveedores (
	id_proveedor INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    email_proveedor VARCHAR(50) UNIQUE,
    nombre_proveedor VARCHAR(50),
    contrasena_proveedor VARCHAR(120),
    img_proveedor VARCHAR(60)
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
    descripcion TEXT,
    medidas JSON,
    fecha DATETIME DEFAULT current_timestamp,
    id_proveedor INT,
    img_producto VARCHAR(60),
    id_oferta INT,
    FOREIGN KEY (categoria) REFERENCES categorias(nombre_categoria),
    FOREIGN KEY (id_proveedor) REFERENCES proveedores(id_proveedor),
    FOREIGN KEY (id_oferta) REFERENCES ofertas(id_oferta)
);

CREATE TABLE carrito (
    id_carrito INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    id_producto INT,
    id_usuario INT,
    cantidad INT default 1,
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);

CREATE TABLE pedidos (
	id_pedido INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    id_usuario INT,
    fecha DATETIME DEFAULT current_timestamp,
    total NUMERIC (8,2),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);

CREATE TABLE detalle_pedidos (
    id_detalle INT PRIMARY KEY AUTO_INCREMENT,
    id_pedido INT,
    id_producto INT,
    cantidad INT,
    precio_unitario NUMERIC(6,2),
    FOREIGN KEY (id_pedido) REFERENCES pedidos(id_pedido),
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto)
);

INSERT INTO categorias (nombre_categoria,img_categoria) VALUES ('Sofás',"sofas.jpg");
INSERT INTO categorias (nombre_categoria,img_categoria) VALUES ('Sillas',"sillas.jpg");
INSERT INTO categorias (nombre_categoria,img_categoria) VALUES ('Mesas',"mesas.jpg");
INSERT INTO categorias (nombre_categoria,img_categoria) VALUES ('Colchones',"colchones.jpg");
INSERT INTO categorias (nombre_categoria,img_categoria) VALUES ('Armarios',"armarios.jpg");

SELECT * FROM detalle_pedidos;
SELECT * FROM pedidos;
SELECT * FROM carrito;
SELECT * FROM productos;
SELECT * FROM ofertas;
SELECT * FROM proveedores;
SELECT * FROM usuarios;
SELECT * FROM suscripciones;
SELECT * FROM categorias;

DROP TABLE detalle_pedidos;
DROP TABLE pedidos;
DROP TABLE carrito;
DROP TABLE productos;
DROP TABLE ofertas;
DROP TABLE proveedores;
DROP TABLE usuarios;
DROP TABLE suscripciones;
DROP TABLE categorias;

DELETE FROM usuarios WHERE id_usuario = 1;
DELETE FROM categorias WHERE categoria = "Sillas";