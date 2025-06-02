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
INSERT INTO categorias (nombre_categoria,img_categoria) VALUES ('Sofás',"Sofás.jpg");
INSERT INTO categorias (nombre_categoria,img_categoria) VALUES ('Sillones',"Sillones.jpg");
INSERT INTO categorias (nombre_categoria,img_categoria) VALUES ('Cómodas',"Cómodas.png");

INSERT INTO ofertas (nombre, porcentaje) VALUES ('Verano', 20);
INSERT INTO ofertas (nombre, porcentaje) VALUES ('Invierno', 30);

INSERT INTO suscripciones (nombre, precio, max_usos_plano) VALUES ("Básica", 0, 1);
INSERT INTO suscripciones (nombre, precio, max_usos_plano) VALUES ("Premium", 10, 4);
INSERT INTO suscripciones (nombre, precio, max_usos_plano) VALUES ("VIP", 25, -1); -- (-1 = Va a ser nuestro infinito)

-- Después de crear una cuenta de empresa
-- Proveedor 1
INSERT INTO productos (nombre, precio, categoria, stock, descripcion, medidas, id_proveedor, img_producto, id_oferta) VALUES ("Armario Para Baño", 235.00, "Armarios", 7, "Armario perfecto para cuartos de baño blanco.", '{"alto": "180", "ancho": "100", "largo": "70"}', 1, "Armario_1.png", 1);
INSERT INTO productos (nombre, precio, categoria, stock, descripcion, medidas, id_proveedor, img_producto) VALUES ("Cama Moderna", 325.00, "Camas", 11, "Cama viscoelástica dual comfort.", '{"alto": "50", "ancho": "135", "largo": "180"}', 1, "Cama_1.jpg");
INSERT INTO productos (nombre, precio, categoria, stock, descripcion, medidas, id_proveedor, img_producto, id_oferta) VALUES ("Cómoda Madera", 120.00, "Cómodas", 5, "Cómoda de color madera perfecta para salones y pasillos.", '{"alto": "50", "ancho": "89", "largo": "40"}', 1, "Cómoda_1.png", 2);
INSERT INTO productos (nombre, precio, categoria, stock, descripcion, medidas, id_proveedor, img_producto) VALUES ("Mesa Madera Cocina", 75.00, "Mesas", 25, "Mesa perfecta para cocina o comedor de color madera.", '{"alto": "75", "ancho": "110", "largo": "60"}', 1, "Mesa_1.png");
INSERT INTO productos (nombre, precio, categoria, stock, descripcion, medidas, id_proveedor, img_producto) VALUES ("Silla Blanca Para Jardín", 15.00, "Sillas", 15, "Silla blanca minimalista para jardines y exteriores.", '{"alto": "40", "ancho": "60", "largo": "50"}', 1, "Silla_1.png");
INSERT INTO productos (nombre, precio, categoria, stock, descripcion, medidas, id_proveedor, img_producto, id_oferta) VALUES ("Sillón Gris Negro Con Banqueta", 60.00, "Sillones", 6, "Sillón con banqueta gris oscuro perfecto para sala de estar.", '{"alto": "120", "ancho": "82", "largo": "96"}', 1, "Sillón_1.png", 1);
INSERT INTO productos (nombre, precio, categoria, stock, descripcion, medidas, id_proveedor, img_producto) VALUES ("Sofá Blanco", 530.00, "Sofás", 24, "Sofá blanco minimalista.", '{"alto": "140", "ancho": "220", "largo": "88"}', 1, "Sofá_1.png");

-- Proveedor 2
INSERT INTO productos (nombre, precio, categoria, stock, descripcion, medidas, id_proveedor, img_producto) VALUES ("Armario Para Cuarto", 433.00, "Armarios", 7, "Armario de madera de nogal blanco.", '{"alto": "230", "ancho": "180", "largo": "80"}', 2, "Armario_2.png");
INSERT INTO productos (nombre, precio, categoria, stock, descripcion, medidas, id_proveedor, img_producto, id_oferta) VALUES ("Cama Gris Estilizada", 333.00, "Camas", 11, "Cama viscoelástica.", '{"alto": "50", "ancho": "135", "largo": "190"}', 2, "Cama_2.png", 1);
INSERT INTO productos (nombre, precio, categoria, stock, descripcion, medidas, id_proveedor, img_producto) VALUES ("Cómoda Minimalista Blanca", 70.00, "Cómodas", 5, "Cómoda minimalista blanca para casas con decoración minimalista.", '{"alto": "50", "ancho": "60", "largo": "30"}', 2, "Cómoda_2.png");
INSERT INTO productos (nombre, precio, categoria, stock, descripcion, medidas, id_proveedor, img_producto, id_oferta) VALUES ("Mesa Madera Clara Cocina", 75.00, "Mesas", 25, "Mesa perfecta para cocina o comedor de madera clara.", '{"alto": "75", "ancho": "112", "largo": "62"}', 2, "Mesa_2.png", 2);
INSERT INTO productos (nombre, precio, categoria, stock, descripcion, medidas, id_proveedor, img_producto) VALUES ("Silla Minimalista Negra", 20.00, "Sillas", 15, "Silla negra cómoda comfort para interiores.", '{"alto": "40", "ancho": "62", "largo": "52"}', 2, "Silla_2.png");
INSERT INTO productos (nombre, precio, categoria, stock, descripcion, medidas, id_proveedor, img_producto, id_oferta) VALUES ("Sillón Blanco Robusto", 80.00, "Sillones", 6, "Sillón blanco robusto con tejido resistente al agua.", '{"alto": "100", "ancho": "83", "largo": "96"}', 2, "Sillón_2.png", 1);
INSERT INTO productos (nombre, precio, categoria, stock, descripcion, medidas, id_proveedor, img_producto) VALUES ("Sofá Gris", 459.99, "Sofás", 24, "Sillón gris dual comfort.", '{"alto": "140", "ancho": "220", "largo": "80"}', 2, "Sofá_2.png");

-- Proveedor 3
INSERT INTO productos (nombre, precio, categoria, stock, descripcion, medidas, id_proveedor, img_producto, id_oferta) VALUES ("Armario Para Cuarto Pequeño", 360.00, "Armarios", 7, "Armario de madera de roble totalmente blanco para interiores.", '{"alto": "230", "ancho": "160", "largo": "80"}', 3, "Armario_3.png", 2);
INSERT INTO productos (nombre, precio, categoria, stock, descripcion, medidas, id_proveedor, img_producto) VALUES ("Cama Vintage", 260.00, "Camas", 11, "Cama vintage con soporte de madera de roble.", '{"alto": "50", "ancho": "135", "largo": "190"}', 3, "Cama_3.png");
INSERT INTO productos (nombre, precio, categoria, stock, descripcion, medidas, id_proveedor, img_producto, id_oferta) VALUES ("Cómoda Minimalista Negra", 150.00, "Cómodas", 5, "Cómoda minimalista negra para casas con decoración minimalista.", '{"alto": "50", "ancho": "89", "largo": "60"}', 3, "Cómoda_3.png", 1);
INSERT INTO productos (nombre, precio, categoria, stock, descripcion, medidas, id_proveedor, img_producto) VALUES ("Mesa Madera Pulida", 95.00, "Mesas", 25, "Mesa de madera fuerte pulida para comedores.", '{"alto": "77", "ancho": "114", "largo": "64"}', 3, "Mesa_3.png");
INSERT INTO productos (nombre, precio, categoria, stock, descripcion, medidas, id_proveedor, img_producto) VALUES ("Silla Gruesa Blanca", 25.00, "Sillas", 15, "Silla gruesa blanca para cocina o salón.", '{"alto": "40", "ancho": "67", "largo": "57"}', 3, "Silla_3.png");
INSERT INTO productos (nombre, precio, categoria, stock, descripcion, medidas, id_proveedor, img_producto, id_oferta) VALUES ("Sillón Desplazable", 50.00, "Sillones", 6, "Sillón despazable perfecto para escritorio o despacho.", '{"alto": "50", "ancho": "85", "largo": "96"}', 3, "Sillón_3.png", 2);
INSERT INTO productos (nombre, precio, categoria, stock, descripcion, medidas, id_proveedor, img_producto) VALUES ("Sofá Negro Minimalista", 379.99, "Sofás", 24, "Sofá negro con tejido brillante para salón.", '{"alto": "140", "ancho": "220", "largo": "80"}', 3, "Sofá_3.png");

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

DELETE FROM productos;

DELETE FROM suscripciones WHERE nombre="VIP";

DELETE FROM detalle_pedidos WHERE id_detalle = 1;
DELETE FROM pedidos WHERE id_pedido = 1;

DELETE FROM categorias WHERE nombre_categoria = "Armarios";