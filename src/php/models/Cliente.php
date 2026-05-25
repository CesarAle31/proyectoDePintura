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
            ORDER BY cl.idCliente ASC
        ");
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
            WHERE cl.idCliente = :id
        ", ['id' => $id]);
    }

    public function create(array $datos): string
    {
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
        // Actualizar cliente
        $this->execute("
            UPDATE cliente SET nombre = :nombre, apellidoP = :ap, apellidoM = :am,
                   telefono = :tel, correo = :correo
            WHERE idCliente = :id
        ", [
            'id'     => $id,
            'nombre' => $datos['nombre'],
            'ap'     => $datos['apellidoP'],
            'am'     => $datos['apellidoM'],
            'tel'    => $datos['telefono'] ?? null,
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
        $this->execute("DELETE FROM telefonocliente WHERE idCliente = :id", ['id' => $id]);
        return $this->execute("DELETE FROM cliente WHERE idCliente = :id", ['id' => $id]);
    }

    public function count(): int
    {
        $r = $this->queryOne("SELECT COUNT(*) AS total FROM cliente");
        return (int) ($r['total'] ?? 0);
    }

    public function getForSelect(): array
    {
        return $this->query("
            SELECT idCliente, CONCAT(nombre, ' ', apellidoP, ' ', apellidoM) AS nombreCompleto
            FROM cliente ORDER BY nombre
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
