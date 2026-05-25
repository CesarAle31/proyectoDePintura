-- ==========================================
-- ROLES
-- ==========================================

INSERT INTO rol (idRol, nombre, descripcion) VALUES
                                                 (1, 'Administrador', 'Acceso total al sistema'),
                                                 (2, 'Gerente', 'Gestión de operación, ventas y reportes'),
                                                 (3, 'Cajero', 'Operación de punto de venta y clientes'),
                                                 (4, 'Almacenista', 'Control de productos y proveedores'),
                                                 (5, 'Consulta', 'Solo lectura sobre la información del sistema')

    ON DUPLICATE KEY UPDATE
                         nombre = VALUES(nombre),
                         descripcion = VALUES(descripcion);

-- ==========================================
-- USUARIOS
-- ==========================================

DELETE FROM usuario;

ALTER TABLE usuario AUTO_INCREMENT = 1;

INSERT INTO usuario (
    idEmpleado,
    idRol,
    usuario,
    password_hash,
    activo,
    fecha_creacion
)
VALUES

    (
        16,
        1,
        'cesar',
        '$2y$10$BDmfWK72GErEdzZePc/bEuzod5rl6naDxXiLaE6J6Fnm.4D7AHUFq',
        1,
        NOW()
    ),

    (
        17,
        3,
        'mario',
        '$2y$10$kcSli7sWC/OfIgQiNP1N.u4P65Xzukv2xRE8SKAuuKTEJIfsz1b.e',
        1,
        NOW()
    ),

    (
        18,
        4,
        'sebas',
        '$2y$10$HzSRVoJw6Gk4h/V.UMgoSeFOBZBhMPFnhhQLSQ8mpNyzaeW68WnSC',
        1,
        NOW()
    ),

    (
        4,
        2,
        'ana',
        '$2y$10$uEnMrs7/ghVaMuNO/9.FyeShkBzwHcUFl.Yaph9Cu5E.C.AL2AK6C',
        1,
        NOW()
    ),

    (
        1,
        5,
        'alberto',
        '$2y$10$KSiUVvYcQ7qTUDBnT2DwX.KU2ivTI9i8VgcKa3pWDOK4GFNp3MAgq',
        1,
        NOW()
    );