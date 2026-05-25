-- ============================================================
--  triggers.sql — Triggers de la base de datos
-- ============================================================
--  Base de datos: pinturadb
--  Propósito: Documentar los triggers existentes y agregar
--  nuevos triggers académicos con explicaciones detalladas.
-- ============================================================
--
--  ¿Qué es un Trigger?
--  Un trigger es un bloque de código SQL que se ejecuta
--  automáticamente antes o después de un evento (INSERT,
--  UPDATE, DELETE) sobre una tabla específica.
--
--  Sintaxis:
--    CREATE TRIGGER nombre
--    {BEFORE | AFTER} {INSERT | UPDATE | DELETE}
--    ON tabla FOR EACH ROW
--    BEGIN
--        -- Código SQL
--    END;
--
--  NEW = registro nuevo (INSERT/UPDATE)
--  OLD = registro anterior (UPDATE/DELETE)
-- ============================================================

USE pinturadb;

DELIMITER //

-- ────────────────────────────────────────────────────────────
--  TRIGGER 1: validar_correo_cliente (BEFORE INSERT)
-- ────────────────────────────────────────────────────────────
--  Valida que el correo electrónico del cliente tenga un
--  formato válido antes de insertarlo. Si no cumple,
--  lanza un error y la inserción se cancela.
--
--  Ya existe en la BD. Se documenta aquí como referencia.
-- ────────────────────────────────────────────────────────────

/*
CREATE TRIGGER validar_correo_cliente
BEFORE INSERT ON cliente
FOR EACH ROW
BEGIN
    IF NEW.correo NOT REGEXP '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El correo electrónico no tiene un formato válido';
    END IF;
END //
*/


-- ────────────────────────────────────────────────────────────
--  TRIGGER 2: auditoria_cliente_insert (AFTER INSERT)
-- ────────────────────────────────────────────────────────────
--  Registra en la tabla de auditoría cada vez que se inserta
--  un nuevo cliente. Guarda nombre, correo y fecha.
--
--  Ya existe en la BD. Se documenta aquí como referencia.
-- ────────────────────────────────────────────────────────────

/*
CREATE TRIGGER auditoria_cliente_insert
AFTER INSERT ON cliente
FOR EACH ROW
BEGIN
    INSERT INTO auditoria_cliente_insert (nombre, apellidoP, correo, fecha_registro)
    VALUES (NEW.nombre, NEW.apellidoP, NEW.correo, NOW());
END //
*/


-- ────────────────────────────────────────────────────────────
--  TRIGGER 3: auditoria_cliente_delete (AFTER DELETE)
-- ────────────────────────────────────────────────────────────
--  Registra en auditoría cada vez que se elimina un cliente.
--  Guarda los datos del cliente eliminado para trazabilidad.
--
--  Ya existe en la BD.
-- ────────────────────────────────────────────────────────────

/*
CREATE TRIGGER auditoria_cliente_delete
AFTER DELETE ON cliente
FOR EACH ROW
BEGIN
    INSERT INTO auditoria_cliente_delete (nombre, apellidoP, correo, fecha_eliminacion)
    VALUES (OLD.nombre, OLD.apellidoP, OLD.correo, NOW());
END //
*/


-- ────────────────────────────────────────────────────────────
--  TRIGGER 4: prevenir_eliminar_proveedor (BEFORE DELETE)
-- ────────────────────────────────────────────────────────────
--  Impide eliminar un proveedor si tiene pinturas asociadas.
--  Este trigger protege la integridad referencial a nivel
--  de lógica de negocio (además de las FK).
--
--  Ya existe en la BD.
-- ────────────────────────────────────────────────────────────

/*
CREATE TRIGGER prevenir_eliminar_proveedor
BEFORE DELETE ON proveedor
FOR EACH ROW
BEGIN
    DECLARE total INT;
    SELECT COUNT(*) INTO total FROM pintura WHERE idProveedor = OLD.idProveedor;
    IF total > 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'No se puede eliminar el proveedor porque tiene pinturas asociadas';
    END IF;
END //
*/


-- ────────────────────────────────────────────────────────────
--  TRIGGER 5: validar_stock_ticket (BEFORE INSERT)
-- ────────────────────────────────────────────────────────────
--  Antes de insertar un registro en ticket, verifica que
--  haya suficiente stock de la pintura solicitada.
--
--  Ya existe en la BD.
-- ────────────────────────────────────────────────────────────

/*
CREATE TRIGGER validar_stock_ticket
BEFORE INSERT ON ticket
FOR EACH ROW
BEGIN
    DECLARE stockActual INT;
    SELECT stock INTO stockActual FROM pintura WHERE idPintura = NEW.idPintura;
    IF stockActual < NEW.cantidad THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Stock insuficiente para esta pintura';
    END IF;
END //
*/


-- ────────────────────────────────────────────────────────────
--  TRIGGER 6: restar_stock_ticket (AFTER INSERT)
-- ────────────────────────────────────────────────────────────
--  Después de insertar un ticket, resta automáticamente
--  la cantidad vendida del stock de la pintura.
--
--  Ya existe en la BD.
-- ────────────────────────────────────────────────────────────

/*
CREATE TRIGGER restar_stock_ticket
AFTER INSERT ON ticket
FOR EACH ROW
BEGIN
    UPDATE pintura
    SET stock = stock - NEW.cantidad
    WHERE idPintura = NEW.idPintura;
END //
*/


-- ────────────────────────────────────────────────────────────
--  TRIGGER 7: auditoria_stock_pintura (AFTER UPDATE)
-- ────────────────────────────────────────────────────────────
--  Registra cada cambio en el stock de una pintura.
--  Guarda el valor anterior y el nuevo para trazabilidad.
--
--  Ya existe en la BD.
-- ────────────────────────────────────────────────────────────

/*
CREATE TRIGGER auditoria_stock_pintura
AFTER UPDATE ON pintura
FOR EACH ROW
BEGIN
    IF OLD.stock <> NEW.stock THEN
        INSERT INTO auditoria_stock_pintura (idPintura, stock_anterior, stock_nuevo, fecha_cambio)
        VALUES (NEW.idPintura, OLD.stock, NEW.stock, NOW());
    END IF;
END //
*/


-- ────────────────────────────────────────────────────────────
--  TRIGGER 8: auditoria_costo_pintura (AFTER UPDATE)
-- ────────────────────────────────────────────────────────────
--  Registra cada cambio en el costo de una pintura.
--
--  Ya existe en la BD.
-- ────────────────────────────────────────────────────────────

/*
CREATE TRIGGER auditoria_costo_pintura
AFTER UPDATE ON pintura
FOR EACH ROW
BEGIN
    IF OLD.costo <> NEW.costo THEN
        INSERT INTO auditoria_costo_pintura (idPintura, costo_anterior, costo_nuevo, fecha_cambio)
        VALUES (NEW.idPintura, OLD.costo, NEW.costo, NOW());
    END IF;
END //
*/


-- ════════════════════════════════════════════════════════════
--  TRIGGERS NUEVOS (académicos adicionales)
-- ════════════════════════════════════════════════════════════

-- ────────────────────────────────────────────────────────────
--  TRIGGER 9: validar_cantidad_positiva (BEFORE INSERT)
-- ────────────────────────────────────────────────────────────
--  Asegura que la cantidad en un ticket sea siempre positiva.
--  Complementa la validación de stock del trigger 5.

DROP TRIGGER IF EXISTS validar_cantidad_positiva //

CREATE TRIGGER validar_cantidad_positiva
BEFORE INSERT ON ticket
FOR EACH ROW
BEGIN
    IF NEW.cantidad <= 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La cantidad debe ser mayor a cero';
    END IF;
    IF NEW.precio <= 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El precio debe ser mayor a cero';
    END IF;
END //


-- ────────────────────────────────────────────────────────────
--  TRIGGER 10: calcular_importe_ticket (BEFORE INSERT)
-- ────────────────────────────────────────────────────────────
--  Calcula automáticamente el importe del ticket como
--  precio × cantidad. Garantiza que el importe sea correcto
--  sin importar lo que envíe la aplicación.

DROP TRIGGER IF EXISTS calcular_importe_ticket //

CREATE TRIGGER calcular_importe_ticket
BEFORE INSERT ON ticket
FOR EACH ROW
BEGIN
    SET NEW.importe = NEW.precio * NEW.cantidad;
END //


-- ────────────────────────────────────────────────────────────
--  TRIGGER 11: validar_stock_no_negativo (BEFORE UPDATE)
-- ────────────────────────────────────────────────────────────
--  Impide que el stock de una pintura baje de cero al
--  actualizar la tabla pintura directamente.

DROP TRIGGER IF EXISTS validar_stock_no_negativo //

CREATE TRIGGER validar_stock_no_negativo
BEFORE UPDATE ON pintura
FOR EACH ROW
BEGIN
    IF NEW.stock < 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El stock no puede ser negativo';
    END IF;
END //


DELIMITER ;

-- ════════════════════════════════════════════════════════════
--  NOTA IMPORTANTE:
--  Los triggers 1-8 ya existen en la BD y están comentados
--  para evitar errores al ejecutar este script. Si necesitas
--  recrearlos, descomenta el bloque correspondiente y
--  primero elimina el trigger existente con:
--    DROP TRIGGER IF EXISTS nombre_trigger;
--
--  Los triggers 9-11 son nuevos y se pueden ejecutar
--  directamente. Verificar que no entren en conflicto con
--  los triggers existentes (MariaDB solo permite un trigger
--  por combinación de evento y timing por tabla).
-- ════════════════════════════════════════════════════════════
