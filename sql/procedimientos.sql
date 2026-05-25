-- ============================================================
--  procedimientos.sql — Procedimientos Almacenados y Funciones
-- ============================================================
--  Base de datos: pinturadb
--  Propósito: Crear procedimientos almacenados (Stored
--  Procedures) y funciones que encapsulan lógica de negocio.
-- ============================================================
--
--  ¿Qué es un Procedimiento Almacenado?
--  Es un bloque de código SQL guardado en el servidor que
--  se puede ejecutar con CALL nombre(parámetros).
--  Ventajas: reutilización, seguridad, rendimiento.
--
--  ¿Qué es una Función?
--  Similar a un procedimiento pero RETORNA un valor.
--  Se puede usar dentro de SELECT, WHERE, etc.
--
--  Tipos de parámetros:
--    IN    = entrada (por defecto)
--    OUT   = salida
--    INOUT = entrada/salida
-- ============================================================

USE pinturadb;

DELIMITER //

-- ════════════════════════════════════════════════════════════
--  PROCEDIMIENTOS ALMACENADOS
-- ════════════════════════════════════════════════════════════

-- ────────────────────────────────────────────────────────────
--  SP 1: sp_buscar_pinturas
-- ────────────────────────────────────────────────────────────
--  Busca pinturas por nombre, color o línea de producto.
--  Usa LIKE para búsqueda parcial (flexible).
--
--  Uso: CALL sp_buscar_pinturas('blanco');

DROP PROCEDURE IF EXISTS sp_buscar_pinturas //

CREATE PROCEDURE sp_buscar_pinturas(
    IN p_termino VARCHAR(100)
)
BEGIN
    SELECT p.idPintura,
           p.nombre,
           p.color,
           p.capacidad,
           p.presentacion,
           p.stock,
           p.costo,
           c.linea AS lineaProducto,
           pr.razonSocial AS proveedor
    FROM   pintura p
    INNER JOIN clasificacion c  ON p.claveClasificacion = c.claveClasificacion
    INNER JOIN proveedor pr     ON p.idProveedor        = pr.idProveedor
    WHERE  p.nombre LIKE CONCAT('%', p_termino, '%')
       OR  p.color  LIKE CONCAT('%', p_termino, '%')
       OR  c.linea  LIKE CONCAT('%', p_termino, '%')
    ORDER BY p.nombre;
END //


-- ────────────────────────────────────────────────────────────
--  SP 2: sp_reporte_ventas_rango
-- ────────────────────────────────────────────────────────────
--  Genera un reporte de ventas entre dos fechas con
--  resumen estadístico mediante parámetros OUT.
--
--  Uso: CALL sp_reporte_ventas_rango('2024-01-01', '2024-12-31',
--                                     @total, @ingresos, @promedio);
--       SELECT @total, @ingresos, @promedio;

DROP PROCEDURE IF EXISTS sp_reporte_ventas_rango //

CREATE PROCEDURE sp_reporte_ventas_rango(
    IN  p_desde     DATE,
    IN  p_hasta     DATE,
    OUT p_total     INT,
    OUT p_ingresos  DECIMAL(12,2),
    OUT p_promedio  DECIMAL(12,2)
)
BEGIN
    -- Resultado detallado
    SELECT v.folio,
           v.fecha,
           v.montoTotal,
           CONCAT(e.nombre, ' ', e.apellidoP) AS empleado,
           CONCAT(c.nombre, ' ', c.apellidoP) AS cliente
    FROM   venta v
    INNER JOIN empleado e ON v.idEmpleado = e.idEmpleado
    INNER JOIN cliente c  ON v.idCliente  = c.idCliente
    WHERE  v.fecha BETWEEN p_desde AND p_hasta
    ORDER BY v.fecha DESC;

    -- Estadísticas en parámetros OUT
    SELECT COUNT(*),
           COALESCE(SUM(montoTotal), 0),
           COALESCE(AVG(montoTotal), 0)
    INTO   p_total, p_ingresos, p_promedio
    FROM   venta
    WHERE  fecha BETWEEN p_desde AND p_hasta;
END //


-- ────────────────────────────────────────────────────────────
--  SP 3: sp_registrar_venta
-- ────────────────────────────────────────────────────────────
--  Registra una venta completa (encabezado). Los detalles
--  se insertan por separado en ticket (donde los triggers
--  validan stock y restan automáticamente).
--
--  Uso: CALL sp_registrar_venta(2024000020, 1, 3, 1500.00, @resultado);
--       SELECT @resultado;

DROP PROCEDURE IF EXISTS sp_registrar_venta //

CREATE PROCEDURE sp_registrar_venta(
    IN  p_folio       BIGINT,
    IN  p_idEmpleado  INT,
    IN  p_idCliente   INT,
    IN  p_montoTotal  DECIMAL(12,2),
    OUT p_resultado   VARCHAR(100)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        SET p_resultado = 'ERROR: No se pudo registrar la venta';
        ROLLBACK;
    END;

    START TRANSACTION;

    INSERT INTO venta (folio, idEmpleado, idCliente, fecha, montoTotal)
    VALUES (p_folio, p_idEmpleado, p_idCliente, CURDATE(), p_montoTotal);

    SET p_resultado = CONCAT('OK: Venta registrada con folio ', p_folio);
    COMMIT;
END //


-- ────────────────────────────────────────────────────────────
--  SP 4: sp_actualizar_stock
-- ────────────────────────────────────────────────────────────
--  Actualiza el stock de una pintura. Puede sumar o restar.
--  El trigger de auditoría registrará el cambio.
--
--  Uso: CALL sp_actualizar_stock(1, 10, 'SUMAR', @res);
--       CALL sp_actualizar_stock(1, 3, 'RESTAR', @res);

DROP PROCEDURE IF EXISTS sp_actualizar_stock //

CREATE PROCEDURE sp_actualizar_stock(
    IN  p_idPintura  INT,
    IN  p_cantidad   INT,
    IN  p_operacion  VARCHAR(10),  -- 'SUMAR' o 'RESTAR'
    OUT p_resultado  VARCHAR(200)
)
BEGIN
    DECLARE v_stockActual INT;
    DECLARE v_stockNuevo  INT;

    -- Verificar que existe la pintura
    SELECT stock INTO v_stockActual
    FROM   pintura
    WHERE  idPintura = p_idPintura;

    IF v_stockActual IS NULL THEN
        SET p_resultado = 'ERROR: Pintura no encontrada';
    ELSEIF UPPER(p_operacion) = 'SUMAR' THEN
        SET v_stockNuevo = v_stockActual + p_cantidad;
        UPDATE pintura SET stock = v_stockNuevo WHERE idPintura = p_idPintura;
        SET p_resultado = CONCAT('OK: Stock actualizado de ', v_stockActual, ' a ', v_stockNuevo);
    ELSEIF UPPER(p_operacion) = 'RESTAR' THEN
        SET v_stockNuevo = v_stockActual - p_cantidad;
        IF v_stockNuevo < 0 THEN
            SET p_resultado = CONCAT('ERROR: Stock insuficiente. Actual: ', v_stockActual);
        ELSE
            UPDATE pintura SET stock = v_stockNuevo WHERE idPintura = p_idPintura;
            SET p_resultado = CONCAT('OK: Stock actualizado de ', v_stockActual, ' a ', v_stockNuevo);
        END IF;
    ELSE
        SET p_resultado = 'ERROR: Operación no válida. Use SUMAR o RESTAR';
    END IF;
END //


-- ────────────────────────────────────────────────────────────
--  SP 5: sp_top_clientes
-- ────────────────────────────────────────────────────────────
--  Devuelve los N clientes que más han comprado.
--
--  Uso: CALL sp_top_clientes(5);

DROP PROCEDURE IF EXISTS sp_top_clientes //

CREATE PROCEDURE sp_top_clientes(
    IN p_limite INT
)
BEGIN
    SELECT CONCAT(c.nombre, ' ', c.apellidoP, ' ', c.apellidoM) AS cliente,
           c.correo,
           COUNT(v.folio)    AS totalCompras,
           SUM(v.montoTotal) AS totalGastado,
           MIN(v.fecha)      AS primeraCompra,
           MAX(v.fecha)      AS ultimaCompra
    FROM   cliente c
    INNER JOIN venta v ON c.idCliente = v.idCliente
    GROUP BY c.idCliente, c.nombre, c.apellidoP, c.apellidoM, c.correo
    ORDER BY totalGastado DESC
    LIMIT p_limite;
END //


-- ────────────────────────────────────────────────────────────
--  SP 6: sp_resumen_inventario
-- ────────────────────────────────────────────────────────────
--  Muestra un resumen del inventario agrupado por línea
--  de producto con totales y alertas.
--
--  Uso: CALL sp_resumen_inventario();

DROP PROCEDURE IF EXISTS sp_resumen_inventario //

CREATE PROCEDURE sp_resumen_inventario()
BEGIN
    SELECT c.linea AS lineaProducto,
           COUNT(p.idPintura)                AS totalProductos,
           SUM(p.stock)                      AS stockTotal,
           SUM(p.costo * p.stock)            AS valorInventario,
           MIN(p.stock)                      AS stockMinimo,
           MAX(p.stock)                      AS stockMaximo,
           ROUND(AVG(p.stock), 1)            AS stockPromedio,
           SUM(CASE WHEN p.stock <= 5 THEN 1 ELSE 0 END) AS productosStockBajo
    FROM   pintura p
    INNER JOIN clasificacion c ON p.claveClasificacion = c.claveClasificacion
    GROUP BY c.linea
    ORDER BY valorInventario DESC;
END //


-- ════════════════════════════════════════════════════════════
--  FUNCIONES
-- ════════════════════════════════════════════════════════════

-- ────────────────────────────────────────────────────────────
--  FN 1: fn_valor_inventario_proveedor
-- ────────────────────────────────────────────────────────────
--  Calcula el valor total del inventario de un proveedor.
--
--  Uso: SELECT fn_valor_inventario_proveedor(1);
--       SELECT razonSocial, fn_valor_inventario_proveedor(idProveedor)
--       FROM proveedor;

DROP FUNCTION IF EXISTS fn_valor_inventario_proveedor //

CREATE FUNCTION fn_valor_inventario_proveedor(
    p_idProveedor INT
)
RETURNS DECIMAL(12,2)
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE v_valor DECIMAL(12,2);

    SELECT COALESCE(SUM(stock * costo), 0)
    INTO   v_valor
    FROM   pintura
    WHERE  idProveedor = p_idProveedor;

    RETURN v_valor;
END //


-- ────────────────────────────────────────────────────────────
--  FN 2: fn_total_compras_cliente
-- ────────────────────────────────────────────────────────────
--  Retorna el total gastado por un cliente específico.
--
--  Uso: SELECT fn_total_compras_cliente(1);

DROP FUNCTION IF EXISTS fn_total_compras_cliente //

CREATE FUNCTION fn_total_compras_cliente(
    p_idCliente INT
)
RETURNS DECIMAL(12,2)
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE v_total DECIMAL(12,2);

    SELECT COALESCE(SUM(montoTotal), 0)
    INTO   v_total
    FROM   venta
    WHERE  idCliente = p_idCliente;

    RETURN v_total;
END //


-- ────────────────────────────────────────────────────────────
--  FN 3: fn_nivel_stock
-- ────────────────────────────────────────────────────────────
--  Retorna una etiqueta de nivel de stock para una pintura:
--  'CRÍTICO' (0-2), 'BAJO' (3-5), 'NORMAL' (6-15), 'ALTO' (>15).
--
--  Uso: SELECT nombre, stock, fn_nivel_stock(idPintura) AS nivel
--       FROM pintura;

DROP FUNCTION IF EXISTS fn_nivel_stock //

CREATE FUNCTION fn_nivel_stock(
    p_idPintura INT
)
RETURNS VARCHAR(20)
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE v_stock INT;
    DECLARE v_nivel VARCHAR(20);

    SELECT stock INTO v_stock
    FROM   pintura
    WHERE  idPintura = p_idPintura;

    IF v_stock IS NULL THEN
        SET v_nivel = 'NO ENCONTRADO';
    ELSEIF v_stock <= 2 THEN
        SET v_nivel = 'CRÍTICO';
    ELSEIF v_stock <= 5 THEN
        SET v_nivel = 'BAJO';
    ELSEIF v_stock <= 15 THEN
        SET v_nivel = 'NORMAL';
    ELSE
        SET v_nivel = 'ALTO';
    END IF;

    RETURN v_nivel;
END //


-- ────────────────────────────────────────────────────────────
--  FN 4: fn_margen_ganancia
-- ────────────────────────────────────────────────────────────
--  Calcula el margen de ganancia porcentual dado un costo
--  y un precio de venta.
--
--  Uso: SELECT nombre, costo,
--              fn_margen_ganancia(costo, 350.00) AS margen
--       FROM pintura;

DROP FUNCTION IF EXISTS fn_margen_ganancia //

CREATE FUNCTION fn_margen_ganancia(
    p_costo  DECIMAL(10,2),
    p_precio DECIMAL(10,2)
)
RETURNS DECIMAL(5,2)
DETERMINISTIC
NO SQL
BEGIN
    IF p_costo <= 0 OR p_precio <= 0 THEN
        RETURN 0.00;
    END IF;
    RETURN ROUND(((p_precio - p_costo) / p_costo) * 100, 2);
END //


DELIMITER ;

-- ════════════════════════════════════════════════════════════
--  EJEMPLOS DE USO
-- ════════════════════════════════════════════════════════════

-- Buscar pinturas que contengan "blanco":
-- CALL sp_buscar_pinturas('blanco');

-- Reporte de enero 2024:
-- CALL sp_reporte_ventas_rango('2024-01-01', '2024-01-31', @t, @i, @p);
-- SELECT @t AS totalVentas, @i AS ingresos, @p AS promedio;

-- Top 3 clientes:
-- CALL sp_top_clientes(3);

-- Resumen de inventario por línea:
-- CALL sp_resumen_inventario();

-- Nivel de stock de todos los productos:
-- SELECT nombre, color, stock, fn_nivel_stock(idPintura) AS nivel FROM pintura;

-- Valor del inventario por proveedor:
-- SELECT razonSocial, fn_valor_inventario_proveedor(idProveedor) AS valor FROM proveedor;
