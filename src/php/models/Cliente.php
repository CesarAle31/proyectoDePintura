<?php
/**
 * ============================================================
 *  Cliente.php — Modelo de Clientes
 * ============================================================
 *  JOIN con direccion → colonia → municipio para dirección completa.
 *  JOIN con telefonocliente para teléfono.
 * ============================================================
 */

class Cliente extends Model
{
    private function validarDatos(array $datos): array
    {
        $datos['nombre'] = trim($datos['nombre'] ?? '');
        $datos['apellidoP'] = trim($datos['apellidoP'] ?? '');
        $datos['apellidoM'] = trim($datos['apellidoM'] ?? '');
        $datos['correo'] = trim($datos['correo'] ?? '');
        $datos['telefono'] = trim($datos['telefono'] ?? '');
        $datos['calle'] = trim($datos['calle'] ?? '');
        $datos['numero'] = trim($datos['numero'] ?? '');
        $datos['idColonia'] = trim((string) ($datos['idColonia'] ?? ''));

        if ($datos['nombre'] === '') {
            throw new \Exception('El nombre es obligatorio.');
        }

        if (!preg_match('/^[\p{L} ]+$/u', $datos['nombre'])) {
            throw new \Exception('El nombre solo puede contener letras y espacios.');
        }

        if (!filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
            throw new \Exception('El correo no tiene un formato válido.');
        }

        if (!preg_match('/^[0-9]{10}$/', $datos['telefono'])) {
            throw new \Exception('El teléfono debe tener exactamente 10 dígitos.');
        }

        if ($datos['calle'] === '' || $datos['numero'] === '' || $datos['idColonia'] === '') {
            throw new \Exception('La dirección es obligatoria.');
        }

        return $datos;
    }

    public function getAll(): array
    {
        return $this->query("
            SELECT cl.idCliente, cl.nombre, cl.apellidoP, cl.apellidoM,
                   cl.correo, cl.telefono,
                   CONCAT(cl.nombre, ' ', cl.apellidoP, ' ', cl.apellidoM) AS nombreCompleto,
                   d.calle, d.numero,
                   co.colonia, co.cp,
                   m.nombre AS municipio, m.estado,
                   tc.telefonoCliente
            FROM cliente cl
            LEFT JOIN direccion d ON cl.idDireccion = d.idDireccion
            LEFT JOIN colonia co ON d.idColonia = co.idColonia
            LEFT JOIN municipio m ON co.claveM = m.claveM
            LEFT JOIN telefonocliente tc ON cl.idCliente = tc.idCliente
            WHERE cl.activo = 1
            ORDER BY cl.idCliente ASC
        ");
    }

    /**
     * Listado filtrable con SQL dinámico y parámetros seguros.
     * Soporta: buscar (nombre/correo), teléfono, estado activo/inactivo.
     */
    public function filtrar(array $f): array
    {
        $sql = "
            SELECT cl.idCliente, cl.nombre, cl.apellidoP, cl.apellidoM,
                   cl.correo, cl.telefono, cl.activo,
                   CONCAT(cl.nombre, ' ', cl.apellidoP, ' ', cl.apellidoM) AS nombreCompleto,
                   d.calle, d.numero,
                   co.colonia, co.cp,
                   m.nombre AS municipio, m.estado,
                   tc.telefonoCliente
            FROM cliente cl
            LEFT JOIN direccion      d  ON cl.idDireccion = d.idDireccion
            LEFT JOIN colonia        co ON d.idColonia    = co.idColonia
            LEFT JOIN municipio      m  ON co.claveM      = m.claveM
            LEFT JOIN telefonocliente tc ON cl.idCliente  = tc.idCliente
            WHERE 1=1";
        $params = [];

        if (isset($f['activo']) && $f['activo'] !== '') {
            $sql .= " AND cl.activo = :activo";
            $params['activo'] = (int) $f['activo'];
        } else {
            $sql .= " AND cl.activo = 1";
        }

        if (!empty($f['buscar'])) {
            $like = '%' . $f['buscar'] . '%';
            $sql .= " AND (CONCAT(cl.nombre, ' ', cl.apellidoP, ' ', cl.apellidoM) LIKE :b1
                        OR cl.correo LIKE :b2)";
            $params['b1'] = $like;
            $params['b2'] = $like;
        }

        if (!empty($f['telefono'])) {
            $like = '%' . $f['telefono'] . '%';
            $sql .= " AND (tc.telefonoCliente LIKE :tel1 OR cl.telefono LIKE :tel2)";
            $params['tel1'] = $like;
            $params['tel2'] = $like;
        }

        $sql .= " ORDER BY cl.idCliente ASC";
        return $this->query($sql, $params);
    }

    public function getById(int $id): ?array
    {
        return $this->queryOne("
            SELECT cl.*, d.calle, d.numero, d.idColonia,
                   co.colonia, co.cp, co.claveM,
                   m.nombre AS municipio, m.estado,
                   tc.telefonoCliente
            FROM cliente cl
            LEFT JOIN direccion d ON cl.idDireccion = d.idDireccion
            LEFT JOIN colonia co ON d.idColonia = co.idColonia
            LEFT JOIN municipio m ON co.claveM = m.claveM
            LEFT JOIN telefonocliente tc ON cl.idCliente = tc.idCliente
            WHERE cl.idCliente = :id AND cl.activo = 1
        ", ['id' => $id]);
    }

    public function create(array $datos): string
    {
        $datos = $this->validarDatos($datos);

        // 1. Insertar dirección
        $this->execute("
            INSERT INTO direccion (idColonia, calle, numero)
            VALUES (:colonia, :calle, :numero)
        ", [
            'colonia' => $datos['idColonia'],
            'calle'   => $datos['calle'],
            'numero'  => $datos['numero'],
        ]);
        $idDireccion = $this->lastInsertId();

        // 2. Insertar cliente
        $this->execute("
            INSERT INTO cliente (nombre, apellidoP, apellidoM, telefono, idDireccion, correo)
            VALUES (:nombre, :ap, :am, :tel, :dir, :correo)
        ", [
            'nombre' => $datos['nombre'],
            'ap'     => $datos['apellidoP'],
            'am'     => $datos['apellidoM'],
            'tel'    => $datos['telefono'] ?? null,
            'dir'    => $idDireccion,
            'correo' => $datos['correo'],
        ]);
        $idCliente = $this->lastInsertId();

        // 3. Insertar teléfono
        if (!empty($datos['telefonoCliente'])) {
            $this->execute("
                INSERT INTO telefonocliente (idCliente, telefonoCliente)
                VALUES (:id, :tel)
            ", ['id' => $idCliente, 'tel' => $datos['telefonoCliente']]);
        }

        return $idCliente;
    }

    public function update(int $id, array $datos): int
    {
        $datos = $this->validarDatos($datos);

        $cliente = $this->queryOne(
            "SELECT idDireccion FROM cliente WHERE idCliente = :id AND activo = 1",
            ['id' => $id]
        );
        if (!$cliente) {
            throw new \Exception('El cliente no existe o ya fue dado de baja.');
        }

        if (!empty($cliente['idDireccion'])) {
            $this->execute("
                UPDATE direccion SET idColonia = :colonia, calle = :calle, numero = :numero
                WHERE idDireccion = :idDireccion
            ", [
                'idDireccion' => $cliente['idDireccion'],
                'colonia'     => $datos['idColonia'],
                'calle'       => $datos['calle'],
                'numero'      => $datos['numero'],
            ]);
        } else {
            $this->execute("
                INSERT INTO direccion (idColonia, calle, numero)
                VALUES (:colonia, :calle, :numero)
            ", [
                'colonia' => $datos['idColonia'],
                'calle'   => $datos['calle'],
                'numero'  => $datos['numero'],
            ]);
            $cliente['idDireccion'] = $this->lastInsertId();
        }

        // Actualizar cliente
        $this->execute("
            UPDATE cliente SET nombre = :nombre, apellidoP = :ap, apellidoM = :am,
                   telefono = :tel, idDireccion = :dir, correo = :correo
            WHERE idCliente = :id AND activo = 1
        ", [
            'id'     => $id,
            'nombre' => $datos['nombre'],
            'ap'     => $datos['apellidoP'],
            'am'     => $datos['apellidoM'],
            'tel'    => $datos['telefono'] ?? null,
            'dir'    => $cliente['idDireccion'],
            'correo' => $datos['correo'],
        ]);

        // Actualizar teléfono
        if (!empty($datos['telefonoCliente'])) {
            $existe = $this->queryOne("SELECT idTelefonoCliente FROM telefonocliente WHERE idCliente = :id", ['id' => $id]);
            if ($existe) {
                $this->execute("UPDATE telefonocliente SET telefonoCliente = :tel WHERE idCliente = :id",
                    ['tel' => $datos['telefonoCliente'], 'id' => $id]);
            } else {
                $this->execute("INSERT INTO telefonocliente (idCliente, telefonoCliente) VALUES (:id, :tel)",
                    ['id' => $id, 'tel' => $datos['telefonoCliente']]);
            }
        }

        return 1;
    }

    public function delete(int $id): int
    {
        $cliente = $this->queryOne(
            "SELECT idCliente FROM cliente WHERE idCliente = :id AND activo = 1",
            ['id' => $id]
        );
        if (!$cliente) {
            throw new \Exception('El cliente no existe o ya fue dado de baja.');
        }
        return $this->execute(
            "UPDATE cliente SET activo = 0 WHERE idCliente = :id",
            ['id' => $id]
        );
    }

    public function count(): int
    {
        $r = $this->queryOne("SELECT COUNT(*) AS total FROM cliente WHERE activo = 1");
        return (int) ($r['total'] ?? 0);
    }

    public function getForSelect(): array
    {
        return $this->query("
            SELECT idCliente, CONCAT(nombre, ' ', apellidoP, ' ', apellidoM) AS nombreCompleto
            FROM cliente WHERE activo = 1 ORDER BY nombre
        ");
    }

    public function getColonias(): array
    {
        return $this->query("
            SELECT co.idColonia, co.colonia, co.cp, m.nombre AS municipio
            FROM colonia co
            INNER JOIN municipio m ON co.claveM = m.claveM
            ORDER BY m.nombre, co.colonia
        ");
    }
}
