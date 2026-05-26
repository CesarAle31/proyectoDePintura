<?php
/**
 * ============================================================
 *  Empleado.php — Modelo de Empleados
 * ============================================================
 */

class Empleado extends Model
{
    public function getAll(): array
    {
        return $this->query("
            SELECT e.idEmpleado, e.nombre, e.apellidoP, e.apellidoM,
                   CONCAT(e.nombre, ' ', e.apellidoP, ' ', e.apellidoM) AS nombreCompleto,
                   te.telefonoEmpleado
            FROM empleado e
            LEFT JOIN telefonoempleado te ON e.idEmpleado = te.idEmpleado
            ORDER BY e.idEmpleado ASC
        ");
    }

    public function getById(int $id): ?array
    {
        return $this->queryOne("
            SELECT e.*, te.telefonoEmpleado
            FROM empleado e
            LEFT JOIN telefonoempleado te ON e.idEmpleado = te.idEmpleado
            WHERE e.idEmpleado = :id
        ", ['id' => $id]);
    }

    public function create(array $datos): int
    {
        $this->execute("
            INSERT INTO empleado (idEmpleado, nombre, apellidoP, apellidoM)
            VALUES (:id, :nombre, :ap, :am)
        ", [
            'id'     => $datos['idEmpleado'],
            'nombre' => $datos['nombre'],
            'ap'     => $datos['apellidoP'],
            'am'     => $datos['apellidoM'],
        ]);

        if (!empty($datos['telefonoEmpleado'])) {
            $this->execute("
                INSERT INTO telefonoempleado (idEmpleado, telefonoEmpleado)
                VALUES (:id, :tel)
            ", ['id' => $datos['idEmpleado'], 'tel' => $datos['telefonoEmpleado']]);
        }

        return (int) $datos['idEmpleado'];
    }

    public function update(int $id, array $datos): int
    {
        $this->execute("
            UPDATE empleado SET nombre = :nombre, apellidoP = :ap, apellidoM = :am
            WHERE idEmpleado = :id
        ", [
            'id'     => $id,
            'nombre' => $datos['nombre'],
            'ap'     => $datos['apellidoP'],
            'am'     => $datos['apellidoM'],
        ]);

        if (!empty($datos['telefonoEmpleado'])) {
            $existe = $this->queryOne("SELECT idTelefonoEmpleado FROM telefonoempleado WHERE idEmpleado = :id", ['id' => $id]);
            if ($existe) {
                $this->execute("UPDATE telefonoempleado SET telefonoEmpleado = :tel WHERE idEmpleado = :id",
                    ['tel' => $datos['telefonoEmpleado'], 'id' => $id]);
            } else {
                $this->execute("INSERT INTO telefonoempleado (idEmpleado, telefonoEmpleado) VALUES (:id, :tel)",
                    ['id' => $id, 'tel' => $datos['telefonoEmpleado']]);
            }
        }

        return 1;
    }

    public function delete(int $id): int
    {
        $this->execute("DELETE FROM telefonoempleado WHERE idEmpleado = :id", ['id' => $id]);
        return $this->execute("DELETE FROM empleado WHERE idEmpleado = :id", ['id' => $id]);
    }

    public function count(): int
    {
        $r = $this->queryOne("SELECT COUNT(*) AS total FROM empleado");
        return (int) ($r['total'] ?? 0);
    }

    public function getForSelect(): array
    {
        return $this->query("
            SELECT idEmpleado, CONCAT(nombre, ' ', apellidoP, ' ', apellidoM) AS nombreCompleto
            FROM empleado ORDER BY nombre
        ");
    }

    public function getNextId(): int
    {
        $r = $this->queryOne("SELECT COALESCE(MAX(idEmpleado), 0) + 1 AS next FROM empleado");
        return (int) ($r['next'] ?? 1);
    }

    /**
     * Listado filtrable con SQL dinámico y parámetros seguros.
     * Soporta: buscar (nombre), rol (vía tabla usuario/rol), activo (vía usuario).
     */
    public function filtrar(array $f): array
    {
        $sql = "
            SELECT e.idEmpleado, e.nombre, e.apellidoP, e.apellidoM,
                   CONCAT(e.nombre, ' ', e.apellidoP, ' ', e.apellidoM) AS nombreCompleto,
                   te.telefonoEmpleado,
                   r.nombre AS rol,
                   u.activo AS usuarioActivo
            FROM empleado e
            LEFT JOIN telefonoempleado te ON e.idEmpleado = te.idEmpleado
            LEFT JOIN usuario           u  ON e.idEmpleado = u.idEmpleado
            LEFT JOIN rol               r  ON u.idRol      = r.idRol
            WHERE 1=1";
        $params = [];

        if (!empty($f['buscar'])) {
            $like = '%' . $f['buscar'] . '%';
            $sql .= " AND CONCAT(e.nombre, ' ', e.apellidoP, ' ', e.apellidoM) LIKE :b1";
            $params['b1'] = $like;
        }

        if (!empty($f['rol'])) {
            $sql .= " AND u.idRol = :rol";
            $params['rol'] = (int) $f['rol'];
        }

        if (isset($f['activo']) && $f['activo'] !== '') {
            $sql .= " AND u.activo = :activo";
            $params['activo'] = (int) $f['activo'];
        }

        $sql .= " ORDER BY e.idEmpleado ASC";
        return $this->query($sql, $params);
    }

    /**
     * Roles disponibles para el select de filtros.
     */
    public function getRoles(): array
    {
        return $this->query("SELECT idRol, nombre FROM rol ORDER BY idRol");
    }

    /**
     * Empleados con total de ventas realizadas (JOIN + GROUP).
     */
    public function getConVentas(): array
    {
        return $this->query("
            SELECT CONCAT(e.nombre, ' ', e.apellidoP, ' ', e.apellidoM) AS empleado,
                   COUNT(v.folio) AS ventas,
                   COALESCE(SUM(v.montoTotal), 0) AS ingresos
            FROM venta v
            INNER JOIN empleado e ON v.idEmpleado = e.idEmpleado
            GROUP BY e.idEmpleado, e.nombre, e.apellidoP, e.apellidoM
        ");
    }
}
