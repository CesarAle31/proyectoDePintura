-- ============================================================
--  vistas.sql — Vistas (Views) de la base de datos
-- ============================================================
--  Base de datos: pinturadb
--  Propósito: Crear vistas que simplifican consultas comunes
--  y encapsulan JOINs complejos.
-- ============================================================

USE pinturadb;

-- ────────────────────────────────────────────────────────────
--  Vista 1: v_catalogo_completo
-- ────────────────────────────────────────────────────────────
--  Muestra el catálogo completo de pinturas con clasificación
--  y proveedor en una sola vista. Evita repetir los JOINs
--  cada vez que se necesita el catálogo.

DROP VIEW IF EXISTS v_catalogo_completo;

CREATE VIEW v_catalogo_completo AS
SELECT p.idPintura,
       p.nombre,
       p.color,
       p.capacidad,
       p.presentacion,
       p.stock,
       p.costo,
       c.tipo          AS tipoClasificacion,
       c.linea         AS lineaProducto,
       c.descripcion   AS descripcionLinea,
       pr.razonSocial  AS proveedor,
       (p.stock * p.costo) AS valorEnInventario
FROM   pintura p
INNER JOIN clasificacion c  ON p.claveClasificacion = c.claveClasificacion
INNER JOIN proveedor pr     ON p.idProveedor        = pr.idProveedor;

-- Uso:  SELECT * FROM v_catalogo_completo WHERE lineaProducto = 'VINACRIL';


-- ────────────────────────────────────────────────────────────
--  Vista 2: v_clientes_completos
-- ────────────────────────────────────────────────────────────
--  Cliente con dirección completa y teléfonos, sin repetir
--  la cadena de JOINs cliente→dirección→colonia→municipio.

DROP VIEW IF EXISTS v_clientes_completos;

CREATE VIEW v_clientes_completos AS
SELECT c.idCliente,
       CONCAT(c.nombre, ' ', c.apellidoP, ' ', c.apellidoM) AS nombreCompleto,
       c.correo,
       tc.telefono,
       d.calle,
       d.numero,
       col.nombreColonia AS colonia,
       col.cp            AS codigoPostal,
       m.nombreMunicipio AS municipio
FROM   cliente c
LEFT JOIN telefonocliente tc ON c.idCliente        = tc.idCliente
LEFT JOIN direccion d        ON c.idDireccion      = d.idDireccion
LEFT JOIN colonia col        ON d.claveColonia      = col.claveColonia
LEFT JOIN municipio m        ON col.claveMunicipio  = m.claveMunicipio;

-- Uso:  SELECT * FROM v_clientes_completos WHERE municipio = 'Oaxaca de Juárez';


-- ────────────────────────────────────────────────────────────
--  Vista 3: v_ventas_detalle
-- ────────────────────────────────────────────────────────────
--  Cada venta con nombre de empleado, cliente y productos.

DROP VIEW IF EXISTS v_ventas_detalle;

CREATE VIEW v_ventas_detalle AS
SELECT v.folio,
       v.fecha,
       v.montoTotal,
       CONCAT(e.nombre, ' ', e.apellidoP) AS empleado,
       CONCAT(c.nombre, ' ', c.apellidoP) AS cliente,
       c.correo AS correoCliente,
       t.idTicket,
       p.nombre    AS pintura,
       p.color,
       t.cantidad,
       t.precio,
       t.importe
FROM   venta v
INNER JOIN empleado e  ON v.idEmpleado = e.idEmpleado
INNER JOIN cliente c   ON v.idCliente  = c.idCliente
INNER JOIN ticket t    ON v.folio      = t.folio
INNER JOIN pintura p   ON t.idPintura  = p.idPintura;

-- Uso:  SELECT * FROM v_ventas_detalle WHERE folio = 2024000001;


-- ────────────────────────────────────────────────────────────
--  Vista 4: v_resumen_ventas_mensual
-- ────────────────────────────────────────────────────────────
--  Resumen de ventas agrupado por mes para gráficas.

DROP VIEW IF EXISTS v_resumen_ventas_mensual;

CREATE VIEW v_resumen_ventas_mensual AS
SELECT DATE_FORMAT(fecha, '%Y-%m')  AS mes,
       DATE_FORMAT(fecha, '%M %Y')  AS mesNombre,
       COUNT(*)                     AS totalVentas,
       SUM(montoTotal)              AS ingresos,
       AVG(montoTotal)              AS promedioVenta,
       MIN(montoTotal)              AS ventaMinima,
       MAX(montoTotal)              AS ventaMaxima
FROM   venta
GROUP BY DATE_FORMAT(fecha, '%Y-%m'),
         DATE_FORMAT(fecha, '%M %Y');

-- Uso:  SELECT * FROM v_resumen_ventas_mensual ORDER BY mes;


-- ────────────────────────────────────────────────────────────
--  Vista 5: v_ranking_productos
-- ────────────────────────────────────────────────────────────
--  Ranking de productos por unidades vendidas.

DROP VIEW IF EXISTS v_ranking_productos;

CREATE VIEW v_ranking_productos AS
SELECT p.idPintura,
       p.nombre,
       p.color,
       p.presentacion,
       p.stock        AS stockActual,
       COALESCE(SUM(t.cantidad), 0)  AS totalVendido,
       COALESCE(SUM(t.importe), 0)   AS totalIngresos,
       COUNT(t.idTicket)             AS vecesVendido
FROM   pintura p
LEFT JOIN ticket t ON p.idPintura = t.idPintura
GROUP BY p.idPintura, p.nombre, p.color, p.presentacion, p.stock;

-- Uso:  SELECT * FROM v_ranking_productos ORDER BY totalVendido DESC LIMIT 10;


-- ────────────────────────────────────────────────────────────
--  Vista 6: v_rendimiento_empleados
-- ────────────────────────────────────────────────────────────

DROP VIEW IF EXISTS v_rendimiento_empleados;

CREATE VIEW v_rendimiento_empleados AS
SELECT e.idEmpleado,
       CONCAT(e.nombre, ' ', e.apellidoP, ' ', e.apellidoM) AS empleado,
       COUNT(v.folio)         AS totalVentas,
       COALESCE(SUM(v.montoTotal), 0)  AS ingresos,
       COALESCE(AVG(v.montoTotal), 0)  AS promedioVenta,
       MAX(v.fecha)           AS ultimaVenta
FROM   empleado e
LEFT JOIN venta v ON e.idEmpleado = v.idEmpleado
GROUP BY e.idEmpleado, e.nombre, e.apellidoP, e.apellidoM;

-- Uso:  SELECT * FROM v_rendimiento_empleados ORDER BY ingresos DESC;


-- ────────────────────────────────────────────────────────────
--  Vista 7: v_stock_critico
-- ────────────────────────────────────────────────────────────
--  Productos con stock menor o igual a 5 unidades.

DROP VIEW IF EXISTS v_stock_critico;

CREATE VIEW v_stock_critico AS
SELECT p.idPintura,
       p.nombre,
       p.color,
       p.presentacion,
       p.stock,
       p.costo,
       pr.razonSocial AS proveedor,
       pr.telefono    AS telProveedor
FROM   pintura p
INNER JOIN proveedor pr ON p.idProveedor = pr.idProveedor
WHERE  p.stock <= 5;

-- Uso:  SELECT * FROM v_stock_critico ORDER BY stock ASC;


-- ────────────────────────────────────────────────────────────
--  Vista 8: v_proveedores_inventario
-- ────────────────────────────────────────────────────────────

DROP VIEW IF EXISTS v_proveedores_inventario;

CREATE VIEW v_proveedores_inventario AS
SELECT pr.idProveedor,
       pr.razonSocial,
       pr.telefono,
       COUNT(p.idPintura)                  AS totalProductos,
       COALESCE(SUM(p.stock), 0)           AS stockTotal,
       COALESCE(SUM(p.costo * p.stock), 0) AS valorInventario
FROM   proveedor pr
LEFT JOIN pintura p ON pr.idProveedor = p.idProveedor
GROUP BY pr.idProveedor, pr.razonSocial, pr.telefono;

-- Uso:  SELECT * FROM v_proveedores_inventario ORDER BY valorInventario DESC;
