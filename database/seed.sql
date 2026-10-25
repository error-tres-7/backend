-- Fictional demo data matching the frontend's demo journey. Idempotent: fixed ids + INSERT IGNORE.
-- Requires @demo_hash (a password_hash() bcrypt string) set in the same session, e.g.:
--   SET @demo_hash = '$2y$10$...';
-- Hours and accepted payment methods are illustrative, not real merchant data.

INSERT IGNORE INTO Usuarios (id_usuario, nombre, correo, contraseña, rol) VALUES
    (1, 'Mesa directiva Mercado Medellín', 'medellin@example.com', @demo_hash, 'comerciante'),
    (2, 'Lucha García', 'lucha@example.com', @demo_hash, 'comerciante'),
    (3, 'Pepe Hernández', 'pepe@example.com', @demo_hash, 'comerciante'),
    (4, 'Rosa Martínez', 'rosa@example.com', @demo_hash, 'comerciante'),
    (5, 'Ana López', 'ana@example.com', @demo_hash, 'cliente'),
    (6, 'Luis Martínez', 'luis@example.com', @demo_hash, 'cliente'),
    (7, 'Carmen Díaz', 'carmen@example.com', @demo_hash, 'cliente');

INSERT IGNORE INTO Mercados (id_mercado, id_usuario, descripcion, direccion, horario, telefono) VALUES
    (1, 1, 'Mercado Medellín', 'Campeche 101, Roma Sur, Cuauhtémoc, CDMX', 'Lun–Dom 7:00–18:00', '5500000001'),
    (2, 1, 'Mercado Melchor Ocampo', 'Morelia 1, Roma Norte, Cuauhtémoc, CDMX', 'Lun–Sáb 7:00–17:00 · Dom 8:00–15:00', '5500000002');

INSERT IGNORE INTO Locales (id_local, id_mercado, nombre, descripcion, horario, metodos_pago) VALUES
    (1, 1, 'Doña Lucha', 'Local 24 · Pasillo B · Frutas y verduras', 'Lun–Sáb 8:00–18:00 · Dom 8:00–15:00', 'efectivo,transferencia,tarjeta'),
    (2, 1, 'El huerto de Pepe', 'Local 18 · Pasillo A · Frutas de temporada', 'Mar–Dom 7:30–17:00', 'efectivo,transferencia'),
    (3, 1, 'Hierbas Doña Rosa', 'Local 31 · Pasillo C · Hierbas y especias', 'Lun–Vie 9:00–16:00 · Sáb 8:00–14:00', 'efectivo'),
    (4, 2, 'Canasta Fresca', 'Local 7 · Verduras del día', 'Lun–Sáb 8:00–17:00', 'efectivo,tarjeta'),
    (5, 2, 'Abarrotes La Güera', 'Local 12 · Granos y abarrotes', 'Lun–Dom 8:00–16:00', '');

INSERT IGNORE INTO Categorias (id_categoria, nombre) VALUES
    (1, 'Frutas y verduras'),
    (2, 'Hierbas y especias'),
    (3, 'Abarrotes');

INSERT IGNORE INTO Productos (id_producto, id_categoria, nombre, descripcion, imagen, precio) VALUES
    (1, 1, 'Jitomate saladet', 'Por kilo', 'productos/jitomate.png', 32.00),
    (2, 1, 'Aguacate Hass', 'Por kilo', 'productos/aguacate.png', 70.00),
    (3, 2, 'Cilantro fresco', 'Por manojo', 'productos/cilantro.png', 10.00),
    (4, 1, 'Limón sin semilla', 'Por kilo', 'productos/limon.png', 28.00),
    (5, 1, 'Cebolla blanca', 'Por kilo', 'productos/cebolla.png', 24.00),
    (6, 1, 'Chile serrano', 'Por kilo', 'productos/serrano.png', 40.00),
    (7, 2, 'Epazote', 'Por manojo', 'productos/epazote.png', 12.00),
    (8, 3, 'Frijol negro', 'Por kilo', 'productos/frijol.png', 38.00);

INSERT IGNORE INTO Inventario (id_inventario, id_producto, id_mercado, cantidad) VALUES
    (1, 1, 1, 40), (2, 2, 1, 25), (3, 3, 1, 3), (4, 4, 1, 30),
    (5, 5, 1, 20), (6, 6, 1, 8), (7, 7, 1, 15),
    (8, 1, 2, 18), (9, 5, 2, 12), (10, 8, 2, 50);

-- Today's orders for Doña Lucha, matching the dashboard design ($77 / $135 / $98).
INSERT IGNORE INTO Pedidos (id_pedido, id_usuario, id_local, fecha, total) VALUES
    (39, 7, 1, TIMESTAMP(CURRENT_DATE, '11:30:00'), 98.00),
    (40, 6, 1, TIMESTAMP(CURRENT_DATE, '12:00:00'), 135.00),
    (41, 5, 1, TIMESTAMP(CURRENT_DATE, '12:30:00'), 77.00);
