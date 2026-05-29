<?php
/**
 * ============================================================
 *  Proveedor.php — Modelo de Proveedores
 * ============================================================
 */

class Proveedor extends Model
{
    /**
     * Mapea columnas de dirección que cambian entre versiones de pinturadb.
     */
    private function esquemaUbicacion(): array
    {
        return [
            'dirCol'    => $this->pickColumn('direccion', ['idColonia', 'claveColonia']),
            'colPk'     => $this->pickColumn('colonia', ['idColonia', 'claveColonia']),
            'colNombre' => $this->pickColumn('colonia', ['colonia', 'nombreColonia']),
            'munFk'     => $this->pickColumn('colonia', ['claveM', 'claveMunicipio']),
            'munPk'     => $this->pickColumn('municipio', ['claveM', 'claveMunicipio']),
            'munNombre' => $this->pickColumn('municipio', ['nombre', 'nombreMunicipio']),
            'munEstado' => $this->hasColumn('municipio', 'estado') ? 'estado' : null,
        ];
    }

    private function joinsUbicacion(array $e): string
    {
        return "
            LEFT JOIN direccion d ON pr.idDireccion = d.idDireccion
            LEFT JOIN colonia co ON d.{$e['dirCol']} = co.{$e['colPk']}
            LEFT JOIN municipio m ON co.{$e['munFk']} = m.{$e['munPk']}
        ";
    }

    private function selectUbicacion(array $e, bool $incluirIds = false): string
    {
        $estado = $e['munEstado'] ? "m.{$e['munEstado']} AS estado" : "'' AS estado";
        $ids = $incluirIds ? "d.{$e['dirCol']} AS idColonia, co.{$e['munFk']} AS claveM," : '';

        return "
                   d.calle, d.numero,
                   {$ids}
                   co.{$e['colNombre']} AS colonia, co.cp,
                   m.{$e['munNombre']} AS municipio, {$estado}";
    }

    public function getAll(): array
    {
        $e = $this->esquemaUbicacion();
        return $this->query("
            SELECT pr.idProveedor, pr.razonSocial, pr.telefono," .
                   $this->selectUbicacion($e) . "
            FROM proveedor pr" . $this->joinsUbicacion($e) . "
            ORDER BY pr.idProveedor ASC
        ");
    }

    public function getById(int $id): ?array
    {
        $e = $this->esquemaUbicacion();
        return $this->queryOne("
            SELECT pr.*," . $this->selectUbicacion($e, true) . "
            FROM proveedor pr" . $this->joinsUbicacion($e) . "
            WHERE pr.idProveedor = :id
        ", ['id' => $id]);
    }

    public function create(array $datos): string
    {
        $e = $this->esquemaUbicacion();
        $this->execute("
            INSERT INTO direccion ({$e['dirCol']}, calle, numero)
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
