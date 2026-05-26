-- ============================================================
--  MIGRACIÓN REQUERIDA — Ejecutar UNA SOLA VEZ en pinturadb
--  Accede a phpMyAdmin → pinturadb → SQL y pega este bloque
-- ============================================================

USE pinturadb;

-- 1. Agregar columna activo a la tabla empleado
ALTER TABLE empleado
    ADD COLUMN activo TINYINT(1) NOT NULL DEFAULT 1
    COMMENT 'Baja lógica: 1=activo, 0=dado de baja';

-- 2. Asegurarse de que todos los empleados existentes queden como activos
UPDATE empleado SET activo = 1 WHERE activo IS NULL;

-- 3. Verificar resultado
SELECT idEmpleado, nombre, apellidoP, activo FROM empleado ORDER BY idEmpleado;
