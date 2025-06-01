DROP SCHEMA u929679314_samashome_db;
CREATE SCHEMA u929679314_samashome_db;
USE u929679314_samashome_db;

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
    fecha_expiracion_suscripcion DATE,
    img_usuario VARCHAR(60),
    usos_plano INT DEFAULT 0,
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

ALTER TABLE carrito ADD UNIQUE (id_usuario, id_producto);

CREATE TABLE pedidos (
	id_pedido INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    id_usuario INT,
    fecha DATETIME DEFAULT current_timestamp,
    total NUMERIC (8,2),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);

ALTER TABLE pedidos ADD COLUMN datos_usuario JSON;

CREATE TABLE detalle_pedidos (
    id_detalle INT PRIMARY KEY AUTO_INCREMENT,
    id_pedido INT,
    id_producto INT,
    cantidad INT,
    precio_unitario NUMERIC(6,2),
    FOREIGN KEY (id_pedido) REFERENCES pedidos(id_pedido) ON DELETE CASCADE,
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto)
);

-- 100%
INSERT INTO categorias (nombre_categoria,img_categoria) VALUES ('Armarios',"Armarios.jpg");
INSERT INTO categorias (nombre_categoria,img_categoria) VALUES ('Mesas',"Mesas.jpg");
INSERT INTO categorias (nombre_categoria,img_categoria) VALUES ('Camas',"Camas.jpg");
INSERT INTO categorias (nombre_categoria,img_categoria) VALUES ('Sillas',"Sillas.jpg");
INSERT INTO categorias (nombre_categoria,img_categoria) VALUES ('Sofás',"Sofas.jpg");
INSERT INTO categorias (nombre_categoria,img_categoria) VALUES ('Sillones',"Sillones.jpg");
INSERT INTO categorias (nombre_categoria,img_categoria) VALUES ('Cómodas',"Cómodas.png");

INSERT INTO ofertas (nombre, porcentaje) VALUES ('Verano', 20);
INSERT INTO ofertas (nombre, porcentaje) VALUES ('Invierno', 30);

INSERT INTO suscripciones (nombre, precio, max_usos_plano) VALUES ("Básica", 0, 1);
INSERT INTO suscripciones (nombre, precio, max_usos_plano) VALUES ("Premium", 10, 4);
INSERT INTO suscripciones (nombre, precio, max_usos_plano) VALUES ("VIP", 25, -1); -- (-1 = Va a ser nuestro infinito)

-- Después de crear una cuenta de empresa
INSERT INTO productos (nombre, precio, categoria, stock, descripcion, medidas, id_proveedor, img_producto, id_oferta) VALUES ("Armario Vintage", 433.00, "Armarios", 7, "Armario de madera de nogal.", '{"alto": "230", "ancho": "180", "largo": "110"}', 1, "armarios.jpg", 1);
INSERT INTO productos (nombre, precio, categoria, stock, descripcion, medidas, id_proveedor, img_producto) VALUES ("Cama Moderna", 325.00, "Camas", 11, "Cama viscoelástica.", '{"alto": "50", "ancho": "135", "largo": "190"}', 1, "cama.jpg");
INSERT INTO productos (nombre, precio, categoria, stock, descripcion, medidas, id_proveedor, img_producto, id_oferta) VALUES ("Cama Vintage", 340.00, "Camas", 5, "Cama viscoelástica retro.", '{"alto": "50", "ancho": "135", "largo": "190"}', 1, "colchones.jpg", 2);
INSERT INTO productos (nombre, precio, categoria, stock, descripcion, medidas, id_proveedor, img_producto) VALUES ("Mesa Minimalista", 75.00, "Mesas", 25, "Mesa minimalista pequeña para disfrutar de una deliciosa merienda.", '{"alto": "50", "ancho": "67", "largo": "67"}', 1, "mesa.jpg");
INSERT INTO productos (nombre, precio, categoria, stock, descripcion, medidas, id_proveedor, img_producto) VALUES ("Mesa Vintage", 88.00, "Mesas", 15, "Mesa vintage para compartir una deliciosa comida en familia.", '{"alto": "60", "ancho": "160", "largo": "75"}', 1, "mesas.jpg");
INSERT INTO productos (nombre, precio, categoria, stock, descripcion, medidas, id_proveedor, img_producto, id_oferta) VALUES ("Armario Vintage", 210.00, "Armarios", 6, "Armario vintage blanco para un cuarto acogedor.", '{"alto": "210", "ancho": "120", "largo": "60"}', 1, "mueble.jpeg", 1);
INSERT INTO productos (nombre, precio, categoria, stock, descripcion, medidas, id_proveedor, img_producto) VALUES ("Sillón Blanco", 88.00, "Sillones", 24, "Sillón blanco particular y especial.", '{"alto": "115", "ancho": "80", "largo": "80"}', 1, "sillon-blanco.jpg");
INSERT INTO productos (nombre, precio, categoria, stock, descripcion, medidas, id_proveedor, img_producto, id_oferta) VALUES ("Sofá Gris", 320.00, "Sofás", 7, "Sofá gris minimalisa", '{"alto": "130", "ancho": "200", "largo": "75"}', 1, "sofa.jpg", 2);
INSERT INTO productos (nombre, precio, categoria, stock, descripcion, medidas, id_proveedor, img_producto) VALUES ("Sofá Celeste", 299.00, "Sofás", 1, "Sofá celeste perfecto para salones abiertos al aire libre", '{"alto": "60", "ancho": "190", "largo": "85"}', 1, "sofas.jpg");

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

-- Reiniciar usos del plano
UPDATE usuarios SET usos_plano = 0 WHERE id_usuario = 1;
UPDATE usuarios SET id_suscripcion = 3 WHERE id_usuario = 1;

DELETE FROM usuarios WHERE id_usuario = 2;

DELETE FROM suscripciones WHERE nombre="VIP";

DELETE FROM detalle_pedidos WHERE id_detalle = 1;
DELETE FROM pedidos WHERE id_pedido = 1;

DELETE FROM categorias WHERE nombre_categoria = "Armarios";