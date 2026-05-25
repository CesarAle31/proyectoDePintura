<?php
/**
 * ============================================================
 *  Proveedor.php — Modelo de Proveedores
 * ============================================================
 */

class Proveedor extends Model
{
    public function getAll(): array
    {
        return $this->query("
            SELECT pr.idProveedor, pr.razonSocial, pr.telefono,
                   d.calle, d.numero,
                   co.colonia, co.cp,
                   m.nombre AS municipio, m.estado
            FROM proveedor pr
            LEFT JOIN direccion d ON pr.idDireccion = d.idDireccion
            LEFT JOIN colonia co ON d.idColonia = co.idColonia
            LEFT JOIN municipio m ON co.claveM = m.claveM
            ORDER BY pr.idProveedor ASC
        ");
    }

    public function getById(int $id): ?array
    {
        return $this->queryOne("
            SELECT pr.*, d.calle, d.numero, d.idColonia,
                   co.colonia, co.cp, co.claveM,
                   m.nombre AS municipio
            FROM proveedor pr
            LEFT JOIN direccion d ON pr.idDireccion = d.idDireccion
            LEFT JOIN colonia co ON d.idColonia = co.idColonia
            LEFT JOIN municipio m ON co.claveM = m.claveM
            WHERE pr.idProveedor = :id
        ", ['id' => $id]);
    }

    public function create(array $datos): string
    {
        $this->execute("
            INSERT INTO direccion (idColonia, calle, numero)
            VALUES (:colonia, :calle, :numero)
        ", [
            'colonia' => $datos['idColonia'],
            'calle'   => $datos['calle'],
            'numero'  => $datos['numero'],
        ]);
        $idDir = $this->lastInsertId();

        $this->execute("
            INSERT INTO proveedor (razonSocial, telefono, idDireccion)
            VALUES (:razon, :tel, :dir)
        ", [
            'razon' => $datos['razonSocial'],
            'tel'   => $datos['telefono'] ?? null,
            'dir'   => $idDir,
        ]);

        return $this->lastInsertId();
    }

    public function update(int $id, array $datos): int
    {
        return $this->execute("
            UPDATE proveedor SET razonSocial = :razon, telefono = :tel
            WHERE idProveedor = :id
        ", [
            'id'    => $id,
            'razon' => $datos['razonSocial'],
            'tel'   => $datos['telefono'] ?? null,
        ]);
    }

    public function delete(int $id): int
    {
        return $this->execute("DELETE FROM proveedor WHERE idProveedor = :id", ['id' => $id]);
    }

    public function count(): int
    {
        $r = $this->queryOne("SELECT COUNT(*) AS total FROM proveedor");
        return (int) ($r['total'] ?? 0);
    }

    public function getForSelect(): array
    {
        return $this->query("SELECT idProveedor, razonSocial FROM proveedor ORDER BY razonSocial");
    }

    /**
     * Proveedores con la cantidad de pinturas que suministran (JOIN + GROUP).
     */
    public function getConProductos(): array
    {
        return $this->query("
            SELECT pr.idProveedor, pr.razonSocial,
                   COUNT(p.idPintura) AS totalPinturas,
                   COALESCE(SUM(p.stock), 0) AS stockTotal
            FROM proveedor pr
            LEFT JOIN pintura p ON pr.idProveedor = p.idProveedor
            GROUP BY pr.idProveedor, pr.razonSocial
            ORDER BY totalPinturas DESC
        ");
    }
}
