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
    FOREIGN KEY (id_pedido) REFERENCES pedidos(id_pedido),
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto)
);

UPDATE usuarios SET usos_plano = 0 WHERE id_usuario = 1;
UPDATE usuarios SET id_suscripcion = 3 WHERE id_usuario = 1;

INSERT INTO categorias (nombre_categoria,img_categoria) VALUES ('Armarios',"armarios.jpg");
INSERT INTO categorias (nombre_categoria,img_categoria) VALUES ('Mesas y sillas',"mesas.jpg");
INSERT INTO categorias (nombre_categoria,img_categoria) VALUES ('Camas y colchones',"colchones.jpg");
INSERT INTO categorias (nombre_categoria,img_categoria) VALUES ('Escritorios y sillas de escritorio', "sillas.jpg");
INSERT INTO categorias (nombre_categoria,img_categoria) VALUES ('Sofás y sillones',"sofas.jpg");
INSERT INTO categorias (nombre_categoria,img_categoria) VALUES ('Iluminación',"iluminacion.jpg");
INSERT INTO categorias (nombre_categoria,img_categoria) VALUES ('Muebles de baño',"muebles-banio.jpg");
INSERT INTO categorias (nombre_categoria,img_categoria) VALUES ('Productos de jardín',"jardin.jpg");
INSERT INTO categorias (nombre_categoria,img_categoria) VALUES ('Cocinas y electrodomésticos',"cocina.jpeg");

INSERT INTO ofertas (nombre, porcentaje) VALUES ('Verano', 20);
INSERT INTO ofertas (nombre, porcentaje) VALUES ('Invierno', 30);

INSERT INTO suscripciones (nombre, precio, max_usos_plano) VALUES ("Básica", 0, 1);
INSERT INTO suscripciones (nombre, precio, max_usos_plano) VALUES ("Premium", 10, 4);
INSERT INTO suscripciones (nombre, precio, max_usos_plano) VALUES ("VIP", 25, -1); -- (-1 = Va a ser nuestro infinito)

INSERT INTO productos (nombre, precio, categoria, stock, descripcion, medidas, id_proveedor, img_producto, id_oferta) VALUES ("Armario", 433.00, "Armarios", 7, "Armario con medidas adecuadas.", '{"alto": "230", "ancho": "180", "largo": "110"}', 1, "armarios.webp", 1);
INSERT INTO productos (nombre, precio, categoria, stock, descripcion, medidas, id_proveedor, img_producto) VALUES ("Cama Moderna", 325.00, "Camas y colchones", 11, "Cama viscoelástica.", '{"alto": "50", "ancho": "190", "largo": "135"}', 1, "cama.jpg");
INSERT INTO productos (nombre, precio, categoria, stock, descripcion, medidas, id_proveedor, img_producto, id_oferta) VALUES ("Cama Vintage", 340.00, "Camas y colchones", 5, "Cama viscoelástica retro.", '{"alto": "50", "ancho": "190", "largo": "135"}', 1, "colchones.jpg", 2);
INSERT INTO productos (nombre, precio, categoria, stock, descripcion, medidas, id_proveedor, img_producto) VALUES ("Mesa Redonda", 75.00, "Mesas y sillas", 25, "Mesa redonda pequeña para disfrutar de una deliciosa merienda.", '{"alto": "50", "ancho": "67", "largo": "67"}', 1, "mesa.jpg");
INSERT INTO productos (nombre, precio, categoria, stock, descripcion, medidas, id_proveedor, img_producto) VALUES ("Mesa Ovalada", 88.00, "Mesas y sillas", 15, "Mesa ovalada para compartir una deliciosa comida en familia.", '{"alto": "60", "ancho": "160", "largo": "75"}', 1, "mesas.jpg");
INSERT INTO productos (nombre, precio, categoria, stock, descripcion, medidas, id_proveedor, img_producto, id_oferta) VALUES ("Armario Vintage", 210.00, "Armarios", 6, "Armario vintage blanco para un cuarto acogedor.", '{"alto": "210", "ancho": "120", "largo": "60"}', 1, "mueble.jpeg", 1);
INSERT INTO productos (nombre, precio, categoria, stock, descripcion, medidas, id_proveedor, img_producto) VALUES ("Sillón Blanco", 88.00, "Sofás y sillones", 24, "Sillón blanco particular y especial.", '{"alto": "115", "ancho": "80", "largo": "80"}', 1, "sillon-blanco.jpg");
INSERT INTO productos (nombre, precio, categoria, stock, descripcion, medidas, id_proveedor, img_producto, id_oferta) VALUES ("Sofá Gris", 320.00, "Sofás y sillones", 7, "Sofá gris minimalisa", '{"alto": "130", "ancho": "100", "largo": "30"}', 1, "sofa.jpg", 2);
INSERT INTO productos (nombre, precio, categoria, stock, descripcion, medidas, id_proveedor, img_producto) VALUES ("Sofá Celeste", 299.00, "Sofás y sillones", 1, "Sofá celeste perfecto para salones abiertos al aire libre", '{"alto": "60", "ancho": "30", "largo": "100"}', 1, "sofas.jpg");

DELETE FROM suscripciones WHERE nombre="VIP";

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

DELETE FROM usuarios WHERE id_usuario = 2;
DELETE FROM categorias WHERE nombre_categoria = "Cocinas y electrodomésticos";