-- ============================================================
--  consultas-joins.sql — Consultas con JOINs
-- ============================================================
--  Base de datos: pinturadb
--  Propósito: Demostrar diferentes tipos de JOINs y consultas
--  combinadas sobre el esquema de la tienda de pinturas.
-- ============================================================

USE pinturadb;

-- ────────────────────────────────────────────────────────────
--  1. INNER JOIN — Productos con su clasificación
-- ────────────────────────────────────────────────────────────
--  Muestra cada pintura junto con el tipo y línea de su
--  clasificación. Solo aparecen pinturas que TIENEN clasificación.

SELECT p.idPintura,
       p.nombre,
       p.color,
       p.capacidad,
       p.presentacion,
       p.costo,
       p.stock,
       c.tipo        AS tipoClasificacion,
       c.linea       AS lineaProducto,
       c.descripcion AS descripcionLinea
FROM   pintura p
INNER JOIN clasificacion c ON p.claveClasificacion = c.claveClasificacion
ORDER BY c.linea, p.nombre;


-- ────────────────────────────────────────────────────────────
--  2. INNER JOIN — Productos con su proveedor
-- ────────────────────────────────────────────────────────────
--  Relaciona cada pintura con la razón social de su proveedor.

SELECT p.idPintura,
       p.nombre,
       p.color,
       p.costo,
       pr.razonSocial AS proveedor,
       pr.telefono    AS telProveedor
FROM   pintura p
INNER JOIN proveedor pr ON p.idProveedor = pr.idProveedor
ORDER BY pr.razonSocial, p.nombre;


-- ────────────────────────────────────────────────────────────
--  3. INNER JOIN múltiple — Producto + Clasificación + Proveedor
-- ────────────────────────────────────────────────────────────
--  Combina tres tablas para tener la información completa
--  de cada producto.

SELECT p.idPintura,
       p.nombre,
       p.color,
       p.capacidad,
       p.presentacion,
       p.stock,
       p.costo,
       c.linea       AS lineaProducto,
       c.tipo        AS tipo,
       pr.razonSocial AS proveedor
FROM   pintura p
INNER JOIN clasificacion c  ON p.claveClasificacion = c.claveClasificacion
INNER JOIN proveedor pr     ON p.idProveedor        = pr.idProveedor
ORDER BY p.nombre;


-- ────────────────────────────────────────────────────────────
--  4. INNER JOIN — Ventas con empleado y cliente
-- ────────────────────────────────────────────────────────────
--  Lista todas las ventas mostrando quién vendió y a quién.

SELECT v.folio,
       v.fecha,
       v.montoTotal,
       CONCAT(e.nombre, ' ', e.apellidoP, ' ', e.apellidoM) AS empleado,
       CONCAT(c.nombre, ' ', c.apellidoP, ' ', c.apellidoM) AS cliente
FROM   venta v
INNER JOIN empleado e ON v.idEmpleado = e.idEmpleado
INNER JOIN cliente c  ON v.idCliente  = c.idCliente
ORDER BY v.fecha DESC;


-- ────────────────────────────────────────────────────────────
--  5. INNER JOIN — Detalle completo de tickets
-- ────────────────────────────────────────────────────────────
--  Muestra cada línea de ticket con datos de la pintura,
--  la venta, el cliente y el empleado.

SELECT t.idTicket,
       v.folio,
       v.fecha,
       CONCAT(cl.nombre, ' ', cl.apellidoP) AS cliente,
       CONCAT(em.nombre, ' ', em.apellidoP) AS empleado,
       p.nombre    AS pintura,
       p.color,
       t.cantidad,
       t.precio,
       t.importe
FROM   ticket t
INNER JOIN venta v     ON t.folio      = v.folio
INNER JOIN pintura p   ON t.idPintura  = p.idPintura
INNER JOIN cliente cl  ON v.idCliente  = cl.idCliente
INNER JOIN empleado em ON v.idEmpleado = em.idEmpleado
ORDER BY v.folio, t.idTicket;


-- ────────────────────────────────────────────────────────────
--  6. INNER JOIN — Clientes con dirección completa
-- ────────────────────────────────────────────────────────────
--  Recorre la cadena: cliente → dirección → colonia → municipio.

SELECT c.idCliente,
       CONCAT(c.nombre, ' ', c.apellidoP, ' ', c.apellidoM) AS cliente,
       c.correo,
       d.calle,
       d.numero,
       col.nombreColonia AS colonia,
       col.cp,
       m.nombreMunicipio AS municipio
FROM   cliente c
INNER JOIN direccion d  ON c.idDireccion      = d.idDireccion
INNER JOIN colonia col  ON d.claveColonia      = col.claveColonia
INNER JOIN municipio m  ON col.claveMunicipio  = m.claveMunicipio
ORDER BY c.apellidoP;


-- ────────────────────────────────────────────────────────────
--  7. INNER JOIN + GROUP BY — Top 5 productos más vendidos
-- ────────────────────────────────────────────────────────────
--  Usa GROUP BY y funciones de agregación sobre la tabla
--  ticket unida con pintura.

SELECT p.nombre,
       p.color,
       SUM(t.cantidad)  AS totalUnidadesVendidas,
       SUM(t.importe)   AS totalIngresos,
       COUNT(t.idTicket) AS vecesVendido
FROM   ticket t
INNER JOIN pintura p ON t.idPintura = p.idPintura
GROUP BY p.idPintura, p.nombre, p.color
ORDER BY totalUnidadesVendidas DESC
LIMIT 5;


-- ────────────────────────────────────────────────────────────
--  8. INNER JOIN + GROUP BY — Empleados con total de ventas
-- ────────────────────────────────────────────────────────────

SELECT CONCAT(e.nombre, ' ', e.apellidoP) AS empleado,
       COUNT(v.folio)        AS totalVentas,
       SUM(v.montoTotal)     AS ingresos,
       AVG(v.montoTotal)     AS promedioVenta
FROM   empleado e
INNER JOIN venta v ON e.idEmpleado = v.idEmpleado
GROUP BY e.idEmpleado, e.nombre, e.apellidoP
ORDER BY ingresos DESC;


-- ────────────────────────────────────────────────────────────
--  9. INNER JOIN + GROUP BY — Clientes con total de compras
-- ────────────────────────────────────────────────────────────

SELECT CONCAT(c.nombre, ' ', c.apellidoP) AS cliente,
       COUNT(v.folio)    AS totalCompras,
       SUM(v.montoTotal) AS totalGastado,
       MAX(v.fecha)      AS ultimaCompra
FROM   cliente c
INNER JOIN venta v ON c.idCliente = v.idCliente
GROUP BY c.idCliente, c.nombre, c.apellidoP
ORDER BY totalGastado DESC;


-- ────────────────────────────────────────────────────────────
--  10. LEFT JOIN — Proveedores y sus productos (incluye sin productos)
-- ────────────────────────────────────────────────────────────
--  LEFT JOIN muestra TODOS los proveedores, incluso aquellos
--  que no tienen pinturas asociadas.

SELECT pr.idProveedor,
       pr.razonSocial,
       p.nombre AS pintura,
       p.color,
       p.stock
FROM   proveedor pr
LEFT JOIN pintura p ON pr.idProveedor = p.idProveedor
ORDER BY pr.razonSocial;


-- ────────────────────────────────────────────────────────────
--  11. LEFT JOIN + GROUP BY — Proveedores con conteo de productos
-- ────────────────────────────────────────────────────────────

SELECT pr.razonSocial AS proveedor,
       COUNT(p.idPintura)               AS totalProductos,
       COALESCE(SUM(p.stock), 0)        AS stockTotal,
       COALESCE(SUM(p.costo * p.stock), 0) AS valorInventario
FROM   proveedor pr
LEFT JOIN pintura p ON pr.idProveedor = p.idProveedor
GROUP BY pr.idProveedor, pr.razonSocial
ORDER BY totalProductos DESC;


-- ────────────────────────────────────────────────────────────
--  12. LEFT JOIN — Clientes que NO han comprado
-- ────────────────────────────────────────────────────────────
--  Uso de LEFT JOIN + WHERE IS NULL para encontrar registros
--  sin relación en la otra tabla.

SELECT CONCAT(c.nombre, ' ', c.apellidoP, ' ', c.apellidoM) AS cliente,
       c.correo
FROM   cliente c
LEFT JOIN venta v ON c.idCliente = v.idCliente
WHERE  v.folio IS NULL;


-- ────────────────────────────────────────────────────────────
--  13. Subconsulta correlacionada — Pinturas con precio
--      superior al promedio de su línea
-- ────────────────────────────────────────────────────────────

SELECT p.nombre, p.color, p.costo, c.linea
FROM   pintura p
INNER JOIN clasificacion c ON p.claveClasificacion = c.claveClasificacion
WHERE  p.costo > (
    SELECT AVG(p2.costo)
    FROM   pintura p2
    WHERE  p2.claveClasificacion = p.claveClasificacion
)
ORDER BY c.linea, p.costo DESC;


-- ────────────────────────────────────────────────────────────
--  14. Subconsulta — Última venta de cada cliente
-- ────────────────────────────────────────────────────────────

SELECT CONCAT(c.nombre, ' ', c.apellidoP) AS cliente,
       v.folio,
       v.fecha,
       v.montoTotal
FROM   venta v
INNER JOIN cliente c ON v.idCliente = c.idCliente
WHERE  v.fecha = (
    SELECT MAX(v2.fecha)
    FROM   venta v2
    WHERE  v2.idCliente = v.idCliente
)
ORDER BY v.fecha DESC;


-- ────────────────────────────────────────────────────────────
--  15. INNER JOIN + HAVING — Proveedores con más de 2 productos
-- ────────────────────────────────────────────────────────────

SELECT pr.razonSocial AS proveedor,
       COUNT(p.idPintura) AS numProductos
FROM   proveedor pr
INNER JOIN pintura p ON pr.idProveedor = p.idProveedor
GROUP BY pr.idProveedor, pr.razonSocial
HAVING COUNT(p.idPintura) > 2
ORDER BY numProductos DESC;


-- ────────────────────────────────────────────────────────────
--  16. JOIN + funciones de fecha — Ventas del mes actual
-- ────────────────────────────────────────────────────────────

SELECT v.folio,
       v.fecha,
       v.montoTotal,
       CONCAT(c.nombre, ' ', c.apellidoP) AS cliente
FROM   venta v
INNER JOIN cliente c ON v.idCliente = c.idCliente
WHERE  MONTH(v.fecha) = MONTH(CURDATE())
  AND  YEAR(v.fecha)  = YEAR(CURDATE())
ORDER BY v.fecha DESC;


-- ────────────────────────────────────────────────────────────
--  17. JOIN + GROUP BY + DATE_FORMAT — Ventas agrupadas por mes
-- ────────────────────────────────────────────────────────────

SELECT DATE_FORMAT(v.fecha, '%Y-%m')  AS mes,
       DATE_FORMAT(v.fecha, '%M %Y')  AS mesNombre,
       COUNT(v.folio)                 AS totalVentas,
       SUM(v.montoTotal)              AS ingresos
FROM   venta v
GROUP BY DATE_FORMAT(v.fecha, '%Y-%m'), DATE_FORMAT(v.fecha, '%M %Y')
ORDER BY mes;


-- ────────────────────────────────────────────────────────────
--  18. CROSS JOIN (ejemplo académico) — Combinación de colores y presentaciones
-- ────────────────────────────────────────────────────────────
--  Genera todas las combinaciones posibles de colores y
--  presentaciones existentes en el catálogo.

SELECT DISTINCT p1.color, p2.presentacion
FROM   pintura p1
CROSS JOIN (SELECT DISTINCT presentacion FROM pintura) p2
ORDER BY p1.color, p2.presentacion;
