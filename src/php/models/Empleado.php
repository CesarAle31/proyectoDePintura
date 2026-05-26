<?php
/**
 * ============================================================
 *  Empleado.php — Modelo de Empleados (CRUD completo)
 * ============================================================
 *  REQUISITO SQL (ejecutar UNA VEZ en pinturadb):
 *    ALTER TABLE empleado
 *      ADD COLUMN activo TINYINT(1) NOT NULL DEFAULT 1;
 * ============================================================
 */

class Empleado extends Model
{
    // ─── Validación ─────────────────────────────────────────

    /**
     * Valida los datos del empleado y, opcionalmente, del acceso al sistema.
     * Lanza \Exception con mensaje descriptivo si algo falla.
     *
     * @param bool $esNuevo  true = INSERT, false = UPDATE
     */
    private function validarDatos(array $d, bool $esNuevo = true): void
    {
        $nombre    = trim($d['nombre']           ?? '');
        $apellidoP = trim($d['apellidoP']        ?? '');
        $apellidoM = trim($d['apellidoM']        ?? '');
        $telefono  = trim($d['telefonoEmpleado'] ?? '');

        // ── Campos obligatorios del empleado ────────────────
        if ($nombre === '') {
            throw new \Exception('El nombre es obligatorio.');
        }
        if (!preg_match('/^[\p{L}\s]+$/u', $nombre)) {
            throw new \Exception('El nombre solo puede contener letras y espacios.');
        }
        if ($apellidoP === '') {
            throw new \Exception('El apellido paterno es obligatorio.');
        }
        if (!preg_match('/^[\p{L}\s]+$/u', $apellidoP)) {
            throw new \Exception('El apellido paterno solo puede contener letras y espacios.');
        }
        if ($apellidoM === '') {
            throw new \Exception('El apellido materno es obligatorio.');
        }
        if ($telefono === '') {
            throw new \Exception('El teléfono es obligatorio.');
        }
        if (!preg_match('/^[0-9]{10}$/', $telefono)) {
            throw new \Exception('El teléfono debe tener exactamente 10 dígitos numéricos.');
        }

        // ── Validación condicional de acceso al sistema ─────
        $usuario  = trim($d['usuario']  ?? '');
        $password = trim($d['password'] ?? '');
        $idRol    = (int) ($d['idRol']  ?? 0);

        // Si el usuario llenó ALGÚN campo de acceso, todos se vuelven obligatorios
        $quiereAcceso = $usuario !== '' || $password !== '' || $idRol > 0;

        if ($quiereAcceso) {
            if ($usuario === '') {
                throw new \Exception('El nombre de usuario es obligatorio para crear acceso al sistema.');
            }
            if ($idRol <= 0) {
                throw new \Exception('El rol es obligatorio para crear acceso al sistema.');
            }
            if ($esNuevo && $password === '') {
                throw new \Exception('La contraseña es obligatoria para crear acceso al sistema.');
            }
        }
    }

    // ─── SELECT ─────────────────────────────────────────────

    /**
     * Lista todos los empleados activos con rol y usuario (JOIN).
     * Consulta principal del módulo.
     */
    public function getAll(): array
    {
        return $this->query("
            SELECT e.idEmpleado,
                   e.nombre, e.apellidoP, e.apellidoM,
                   CONCAT(e.nombre, ' ', e.apellidoP, ' ', e.apellidoM) AS nombreCompleto,
                   te.telefonoEmpleado,
                   u.usuario,
                   u.idRol,
                   u.activo  AS usuarioActivo,
                   r.nombre  AS rol
            FROM   empleado e
            LEFT JOIN telefonoempleado te ON e.idEmpleado = te.idEmpleado
            LEFT JOIN usuario           u  ON e.idEmpleado = u.idEmpleado
            LEFT JOIN rol               r  ON u.idRol      = r.idRol
            WHERE  e.activo = 1
            ORDER  BY e.idEmpleado ASC
        ");
    }

    /**
     * Obtiene un empleado por ID (incluyendo inactivos para edición).
     * Incluye JOIN con usuario y rol.
     */
    public function getById(int $id): ?array
    {
        return $this->queryOne("
            SELECT e.idEmpleado, e.nombre, e.apellidoP, e.apellidoM, e.activo,
                   te.telefonoEmpleado,
                   u.idUsuario, u.usuario, u.idRol, u.activo AS usuarioActivo,
                   r.nombre AS rol
            FROM   empleado e
            LEFT JOIN telefonoempleado te ON e.idEmpleado = te.idEmpleado
            LEFT JOIN usuario           u  ON e.idEmpleado = u.idEmpleado
            LEFT JOIN rol               r  ON u.idRol      = r.idRol
            WHERE  e.idEmpleado = :id
        ", ['id' => $id]);
    }

    /**
     * Búsqueda filtrable con SQL dinámico y prepared statements.
     *
     * Si el término es puramente numérico → busca por idEmpleado exacto.
     * Si es texto → busca por nombre, teléfono, usuario o rol (LIKE).
     */
    public function filtrar(array $f): array
    {
        // Decidir si mostrar activos (1) o inactivos (0)
        $activoBase = (isset($f['activo']) && $f['activo'] === '0') ? 0 : 1;

        $sql = "
            SELECT e.idEmpleado,
                   e.nombre, e.apellidoP, e.apellidoM,
                   CONCAT(e.nombre, ' ', e.apellidoP, ' ', e.apellidoM) AS nombreCompleto,
                   te.telefonoEmpleado,
                   u.usuario, u.idRol, u.activo AS usuarioActivo,
                   r.nombre AS rol
            FROM   empleado e
            LEFT JOIN telefonoempleado te ON e.idEmpleado = te.idEmpleado
            LEFT JOIN usuario           u  ON e.idEmpleado = u.idEmpleado
            LEFT JOIN rol               r  ON u.idRol      = r.idRol
            WHERE  e.activo = :activo_base";
        $params = ['activo_base' => $activoBase];

        $buscar = trim($f['buscar'] ?? '');
        if ($buscar !== '') {
            if (ctype_digit($buscar)) {
                // Búsqueda exacta por ID
                $sql .= " AND e.idEmpleado = :id";
                $params['id'] = (int) $buscar;
            } else {
                // Búsqueda por texto en múltiples campos
                $like = '%' . $buscar . '%';
                $sql .= " AND (
                            CONCAT(e.nombre, ' ', e.apellidoP, ' ', e.apellidoM) LIKE :b1
                         OR te.telefonoEmpleado LIKE :b2
                         OR u.usuario LIKE :b3
                         OR r.nombre  LIKE :b4
                         )";
                $params['b1'] = $like;
                $params['b2'] = $like;
                $params['b3'] = $like;
                $params['b4'] = $like;
            }
        }

        if (!empty($f['rol'])) {
            $sql .= " AND u.idRol = :rol";
            $params['rol'] = (int) $f['rol'];
        }

        $sql .= " ORDER BY e.idEmpleado ASC";
        return $this->query($sql, $params);
    }

    // ─── INSERT ─────────────────────────────────────────────

    /**
     * Registra un nuevo empleado.
     * - Valida todos los campos.
     * - Verifica que el ID y el usuario no existan.
     * - Crea usuario con password_hash si se proporcionan credenciales.
     */
    public function create(array $datos): int
    {
        $this->validarDatos($datos, true);

        $idEmpleado = (int) ($datos['idEmpleado'] ?? 0);
        if ($idEmpleado <= 0) {
            throw new \Exception('El ID de empleado no es válido.');
        }

        // Verificar duplicado de ID (activo e inactivo)
        $existe = $this->queryOne(
            "SELECT idEmpleado FROM empleado WHERE idEmpleado = :id",
            ['id' => $idEmpleado]
        );
        if ($existe) {
            throw new \Exception("Ya existe un empleado con el ID {$idEmpleado}.");
        }

        // Verificar duplicado de usuario
        $usuario = trim($datos['usuario'] ?? '');
        if ($usuario !== '') {
            $existeUsr = $this->queryOne(
                "SELECT idUsuario FROM usuario WHERE usuario = :u",
                ['u' => $usuario]
            );
            if ($existeUsr) {
                throw new \Exception("El nombre de usuario '{$usuario}' ya está en uso.");
            }
        }

        // Insertar empleado
        $this->execute("
            INSERT INTO empleado (idEmpleado, nombre, apellidoP, apellidoM, activo)
            VALUES (:id, :nombre, :ap, :am, 1)
        ", [
            'id'     => $idEmpleado,
            'nombre' => trim($datos['nombre']),
            'ap'     => trim($datos['apellidoP']),
            'am'     => trim($datos['apellidoM']),
        ]);

        // Insertar teléfono
        $telefono = trim($datos['telefonoEmpleado'] ?? '');
        if ($telefono !== '') {
            $this->execute("
                INSERT INTO telefonoempleado (idEmpleado, telefonoEmpleado)
                VALUES (:id, :tel)
            ", ['id' => $idEmpleado, 'tel' => $telefono]);
        }

        // Insertar usuario/contraseña si se proporcionaron
        $password = trim($datos['password'] ?? '');
        $idRol    = (int) ($datos['idRol']   ?? 0);

        if ($usuario !== '' && $password !== '' && $idRol > 0) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $this->execute("
                INSERT INTO usuario (idEmpleado, idRol, usuario, password_hash, activo, fecha_creacion)
                VALUES (:emp, :rol, :usr, :hash, 1, NOW())
            ", [
                'emp'  => $idEmpleado,
                'rol'  => $idRol,
                'usr'  => $usuario,
                'hash' => $hash,
            ]);
        }

        return $idEmpleado;
    }

    // ─── UPDATE ─────────────────────────────────────────────

    /**
     * Actualiza un empleado activo y (opcionalmente) su cuenta de usuario.
     * - Si el empleado no existe: lanza excepción.
     * - Si se cambia el usuario: verifica que no esté tomado.
     * - Si hay cuenta existente y se deja contraseña en blanco: la conserva.
     * - Si no hay cuenta y se proporcionan credenciales: la crea.
     */
    public function update(int $id, array $datos): int
    {
        $this->validarDatos($datos, false);

        $emp = $this->queryOne(
            "SELECT idEmpleado FROM empleado WHERE idEmpleado = :id AND activo = 1",
            ['id' => $id]
        );
        if (!$emp) {
            throw new \Exception('No existe un empleado activo con ese ID.');
        }

        // Actualizar datos personales
        $this->execute("
            UPDATE empleado
            SET nombre = :nombre, apellidoP = :ap, apellidoM = :am
            WHERE idEmpleado = :id AND activo = 1
        ", [
            'id'     => $id,
            'nombre' => trim($datos['nombre']),
            'ap'     => trim($datos['apellidoP']),
            'am'     => trim($datos['apellidoM']),
        ]);

        // Actualizar teléfono
        $telefono = trim($datos['telefonoEmpleado'] ?? '');
        if ($telefono !== '') {
            $existeTel = $this->queryOne(
                "SELECT idTelefonoEmpleado FROM telefonoempleado WHERE idEmpleado = :id",
                ['id' => $id]
            );
            if ($existeTel) {
                $this->execute(
                    "UPDATE telefonoempleado SET telefonoEmpleado = :tel WHERE idEmpleado = :id",
                    ['tel' => $telefono, 'id' => $id]
                );
            } else {
                $this->execute(
                    "INSERT INTO telefonoempleado (idEmpleado, telefonoEmpleado) VALUES (:id, :tel)",
                    ['id' => $id, 'tel' => $telefono]
                );
            }
        }

        // Gestionar cuenta de usuario
        $usuario  = trim($datos['usuario']  ?? '');
        $password = trim($datos['password'] ?? '');
        $idRol    = (int) ($datos['idRol']  ?? 0);

        if ($usuario !== '' && $idRol > 0) {
            $existeUsr = $this->queryOne(
                "SELECT idUsuario FROM usuario WHERE idEmpleado = :id",
                ['id' => $id]
            );

            if ($existeUsr) {
                // Verificar que el nuevo usuario no esté tomado por otro
                $tomado = $this->queryOne(
                    "SELECT idUsuario FROM usuario WHERE usuario = :usr AND idEmpleado != :id",
                    ['usr' => $usuario, 'id' => $id]
                );
                if ($tomado) {
                    throw new \Exception("El nombre de usuario '{$usuario}' ya está en uso.");
                }

                if ($password !== '') {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $this->execute(
                        "UPDATE usuario SET usuario = :usr, idRol = :rol, password_hash = :hash WHERE idEmpleado = :id",
                        ['usr' => $usuario, 'rol' => $idRol, 'hash' => $hash, 'id' => $id]
                    );
                } else {
                    // Sin nueva contraseña: conservar la actual
                    $this->execute(
                        "UPDATE usuario SET usuario = :usr, idRol = :rol WHERE idEmpleado = :id",
                        ['usr' => $usuario, 'rol' => $idRol, 'id' => $id]
                    );
                }
            } else {
                // Crear cuenta nueva
                if ($password === '') {
                    throw new \Exception('La contraseña es obligatoria al crear acceso al sistema.');
                }
                $tomado = $this->queryOne(
                    "SELECT idUsuario FROM usuario WHERE usuario = :usr",
                    ['usr' => $usuario]
                );
                if ($tomado) {
                    throw new \Exception("El nombre de usuario '{$usuario}' ya está en uso.");
                }
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $this->execute("
                    INSERT INTO usuario (idEmpleado, idRol, usuario, password_hash, activo, fecha_creacion)
                    VALUES (:emp, :rol, :usr, :hash, 1, NOW())
                ", ['emp' => $id, 'rol' => $idRol, 'usr' => $usuario, 'hash' => $hash]);
            }
        }

        return 1;
    }

    // ─── DELETE (baja lógica) ────────────────────────────────

    /**
     * Da de baja lógica al empleado y desactiva su cuenta de usuario.
     * NO elimina físicamente: usa activo = 0 para preservar relaciones.
     * Lanza excepción si el ID no existe o ya está inactivo.
     */
    public function delete(int $id): int
    {
        $emp = $this->queryOne(
            "SELECT idEmpleado FROM empleado WHERE idEmpleado = :id AND activo = 1",
            ['id' => $id]
        );
        if (!$emp) {
            throw new \Exception('No existe un empleado con ese ID.');
        }

        // Desactivar cuenta de usuario asociada (si existe)
        $this->execute("UPDATE usuario SET activo = 0 WHERE idEmpleado = :id", ['id' => $id]);

        // Baja lógica del empleado
        return $this->execute("UPDATE empleado SET activo = 0 WHERE idEmpleado = :id", ['id' => $id]);
    }

    // ─── Auxiliares ─────────────────────────────────────────

    public function count(): int
    {
        $r = $this->queryOne("SELECT COUNT(*) AS total FROM empleado WHERE activo = 1");
        return (int) ($r['total'] ?? 0);
    }

    public function getForSelect(): array
    {
        return $this->query("
            SELECT idEmpleado,
                   CONCAT(nombre, ' ', apellidoP, ' ', apellidoM) AS nombreCompleto
            FROM   empleado
            WHERE  activo = 1
            ORDER  BY nombre
        ");
    }

    public function getNextId(): int
    {
        $r = $this->queryOne("SELECT COALESCE(MAX(idEmpleado), 0) + 1 AS next FROM empleado");
        return (int) ($r['next'] ?? 1);
    }

    public function getRoles(): array
    {
        return $this->query("SELECT idRol, nombre FROM rol ORDER BY idRol");
    }

    public function getConVentas(): array
    {
        return $this->query("
            SELECT CONCAT(e.nombre, ' ', e.apellidoP, ' ', e.apellidoM) AS empleado,
                   COUNT(v.folio)                    AS ventas,
                   COALESCE(SUM(v.montoTotal), 0)    AS ingresos
            FROM   venta v
            INNER JOIN empleado e ON v.idEmpleado = e.idEmpleado
            GROUP  BY e.idEmpleado, e.nombre, e.apellidoP, e.apellidoM
        ");
    }
}
