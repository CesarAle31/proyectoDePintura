<?php
/**
 * ============================================================
 *  Venta.php — Modelo de Ventas
 * ============================================================
 *  JOIN con empleado y cliente. Maneja venta + detalle (ticket).
 * ============================================================
 */

class Venta extends Model
{
    public function getAll(): array
    {
        return $this->query("
            SELECT v.folio, v.fecha, v.montoTotal,
                   CONCAT(e.nombre, ' ', e.apellidoP) AS empleado,
                   CONCAT(c.nombre, ' ', c.apellidoP) AS cliente
            FROM venta v
            INNER JOIN empleado e ON v.idEmpleado = e.idEmpleado
            INNER JOIN cliente c ON v.idCliente = c.idCliente
            ORDER BY v.folio DESC
        ");
    }

    public function getById(int $folio): ?array
    {
        return $this->queryOne("
            SELECT v.*, 
                   CONCAT(e.nombre, ' ', e.apellidoP, ' ', e.apellidoM) AS empleado,
                   CONCAT(c.nombre, ' ', c.apellidoP, ' ', c.apellidoM) AS cliente,
                   c.correo AS correoCliente
            FROM venta v
            INNER JOIN empleado e ON v.idEmpleado = e.idEmpleado
            INNER JOIN cliente c ON v.idCliente = c.idCliente
            WHERE v.folio = :folio
        ", ['folio' => $folio]);
    }

    /**
     * Crea una nueva venta con sus detalles (tickets).
     * Usa transacción para garantizar integridad.
     */
    public function create(array $venta, array $detalles): int
    {
        $this->db->beginTransaction();

        try {
            // Insertar la venta
            $this->execute("
                INSERT INTO venta (folio, idEmpleado, idCliente, fecha, montoTotal)
                VALUES (:folio, :emp, :cli, :fecha, :monto)
            ", [
                'folio' => $venta['folio'],
                'emp'   => $venta['idEmpleado'],
                'cli'   => $venta['idCliente'],
                'fecha' => $venta['fecha'],
                'monto' => $venta['montoTotal'],
            ]);

            // Insertar cada línea del ticket
            foreach ($detalles as $det) {
                $this->execute("
                    INSERT INTO ticket (folio, idPintura, cantidad, precio, importe)
                    VALUES (:folio, :pintura, :cant, :precio, :importe)
                ", [
                    'folio'   => $venta['folio'],
                    'pintura' => $det['idPintura'],
                    'cant'    => $det['cantidad'],
                    'precio'  => $det['precio'],
                    'importe' => $det['importe'],
                ]);
            }

            $this->db->commit();
            return (int) $venta['folio'];

        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function delete(int $folio): int
    {
        $this->db->beginTransaction();
        try {
            $this->execute("DELETE FROM ticket WHERE folio = :f", ['f' => $folio]);
            $result = $this->execute("DELETE FROM venta WHERE folio = :f", ['f' => $folio]);
            $this->db->commit();
            return $result;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function count(): int
    {
        $r = $this->queryOne("SELECT COUNT(*) AS total FROM venta");
        return (int) ($r['total'] ?? 0);
    }

    public function totalIngresos(): float
    {
        $r = $this->queryOne("SELECT COALESCE(SUM(montoTotal), 0) AS total FROM venta");
        return (float) ($r['total'] ?? 0);
    }

    public function getNextFolio(): int
    {
        $r = $this->queryOne("SELECT COALESCE(MAX(folio), 2024000000) + 1 AS next FROM venta");
        return (int) ($r['next'] ?? 2024000001);
    }

    /**
     * Ventas por mes para gráficas.
     */
    public function ventasPorMes(): array
    {
        return $this->query("
            SELECT DATE_FORMAT(fecha, '%Y-%m') AS mes,
                   COUNT(*) AS totalVentas,
                   SUM(montoTotal) AS ingresos
            FROM venta
            GROUP BY DATE_FORMAT(fecha, '%Y-%m')
            ORDER BY mes ASC
        ");
    }

    /**
     * Top 5 productos más vendidos (JOIN ticket + pintura + GROUP).
     */
    public function topProductos(): array
    {
        return $this->query("
            SELECT p.nombre, p.color, SUM(t.cantidad) AS totalVendido,
                   SUM(t.importe) AS totalImporte
            FROM ticket t
            INNER JOIN pintura p ON t.idPintura = p.idPintura
            GROUP BY p.idPintura, p.nombre, p.color
            ORDER BY totalVendido DESC
            LIMIT 5
        ");
    }

    /**
     * Ventas de hoy.
     */
    public function ventasHoy(): array
    {
        return $this->query("
            SELECT v.folio, v.montoTotal,
                   CONCAT(c.nombre, ' ', c.apellidoP) AS cliente
            FROM venta v
            INNER JOIN cliente c ON v.idCliente = c.idCliente
            WHERE v.fecha = CURDATE()
            ORDER BY v.folio DESC
        ");
    }

    /**
     * Listado filtrable con SQL dinámico y parámetros seguros.
     * Soporta: buscar (folio/cliente/empleado), rango de fechas, cliente y empleado.
     */
    public function filtrar(array $f): array
    {
        $sql = "
            SELECT v.folio, v.fecha, v.montoTotal,
                   CONCAT(e.nombre, ' ', e.apellidoP) AS empleado,
                   CONCAT(c.nombre, ' ', c.apellidoP) AS cliente
            FROM venta v
            INNER JOIN empleado e ON v.idEmpleado = e.idEmpleado
            INNER JOIN cliente  c ON v.idCliente  = c.idCliente
            WHERE 1=1";
        $params = [];

        if (!empty($f['buscar'])) {
            $like = '%' . $f['buscar'] . '%';
            $sql .= " AND (v.folio LIKE :b1
                        OR CONCAT(c.nombre, ' ', c.apellidoP) LIKE :b2
                        OR CONCAT(e.nombre, ' ', e.apellidoP) LIKE :b3)";
            $params['b1'] = $like;
            $params['b2'] = $like;
            $params['b3'] = $like;
        }

        if (!empty($f['desde'])) {
            $sql .= " AND v.fecha >= :desde";
            $params['desde'] = $f['desde'];
        }

        if (!empty($f['hasta'])) {
            $sql .= " AND v.fecha <= :hasta";
            $params['hasta'] = $f['hasta'];
        }

        if (!empty($f['cliente'])) {
            $sql .= " AND v.idCliente = :cliente";
            $params['cliente'] = (int) $f['cliente'];
        }

        if (!empty($f['empleado'])) {
            $sql .= " AND v.idEmpleado = :empleado";
            $params['empleado'] = (int) $f['empleado'];
        }

        $sql .= " ORDER BY v.folio DESC";
        return $this->query($sql, $params);
    }

    /**
     * Ventas por rango de fechas.
     */
    public function ventasPorRango(string $desde, string $hasta): array
    {
        return $this->query("
            SELECT v.folio, v.fecha, v.montoTotal,
                   CONCAT(e.nombre, ' ', e.apellidoP) AS empleado,
                   CONCAT(c.nombre, ' ', c.apellidoP) AS cliente
            FROM venta v
            INNER JOIN empleado e ON v.idEmpleado = e.idEmpleado
            INNER JOIN cliente c ON v.idCliente = c.idCliente
            WHERE v.fecha BETWEEN :desde AND :hasta
            ORDER BY v.fecha DESC
        ", ['desde' => $desde, 'hasta' => $hasta]);
    }
}
