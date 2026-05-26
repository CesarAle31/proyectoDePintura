<?php
/**
 * ============================================================
 *  Producto.php — Modelo de Pinturas
 * ============================================================
 *  Maneja todas las consultas relacionadas con la tabla pintura.
 *  Incluye JOINs con clasificacion y proveedor.
 * ============================================================
 */

class Producto extends Model
{
    /**
     * Obtiene todas las pinturas con su clasificación y proveedor (JOIN).
     */
    public function getAll(): array
    {
        return $this->query("
            SELECT p.idPintura, p.nombre, p.color, p.capacidad, p.stock,
                   p.costo, p.presentacion,
                   c.tipo AS tipoClasificacion, c.linea, c.descripcion AS descClasificacion,
                   pr.razonSocial AS proveedor
            FROM pintura p
            INNER JOIN clasificacion c ON p.claveClasificacion = c.claveClasificacion
            INNER JOIN proveedor pr ON p.idProveedor = pr.idProveedor
            ORDER BY p.idPintura ASC
        ");
    }

    /**
     * Obtiene una pintura por su ID.
     */
    public function getById(int $id): ?array
    {
        return $this->queryOne("
            SELECT p.*, c.tipo AS tipoClasificacion, c.linea,
                   pr.razonSocial AS proveedor
            FROM pintura p
            INNER JOIN clasificacion c ON p.claveClasificacion = c.claveClasificacion
            INNER JOIN proveedor pr ON p.idProveedor = pr.idProveedor
            WHERE p.idPintura = :id
        ", ['id' => $id]);
    }

    /**
     * Inserta una nueva pintura.
     */
    public function create(array $datos): int
    {
        return $this->execute("
            INSERT INTO pintura (idPintura, claveClasificacion, idProveedor, nombre, color, capacidad, stock, costo, presentacion)
            VALUES (:id, :clave, :proveedor, :nombre, :color, :capacidad, :stock, :costo, :presentacion)
        ", [
            'id'         => $datos['idPintura'],
            'clave'      => $datos['claveClasificacion'],
            'proveedor'  => $datos['idProveedor'],
            'nombre'     => $datos['nombre'],
            'color'      => $datos['color'],
            'capacidad'  => $datos['capacidad'],
            'stock'      => $datos['stock'],
            'costo'      => $datos['costo'],
            'presentacion'=> $datos['presentacion'],
        ]);
    }

    /**
     * Actualiza una pintura existente.
     */
    public function update(int $id, array $datos): int
    {
        return $this->execute("
            UPDATE pintura SET
                claveClasificacion = :clave,
                idProveedor = :proveedor,
                nombre = :nombre,
                color = :color,
                capacidad = :capacidad,
                stock = :stock,
                costo = :costo,
                presentacion = :presentacion
            WHERE idPintura = :id
        ", [
            'id'         => $id,
            'clave'      => $datos['claveClasificacion'],
            'proveedor'  => $datos['idProveedor'],
            'nombre'     => $datos['nombre'],
            'color'      => $datos['color'],
            'capacidad'  => $datos['capacidad'],
            'stock'      => $datos['stock'],
            'costo'      => $datos['costo'],
            'presentacion'=> $datos['presentacion'],
        ]);
    }

    /**
     * Elimina una pintura.
     */
    public function delete(int $id): int
    {
        return $this->execute("DELETE FROM pintura WHERE idPintura = :id", ['id' => $id]);
    }

    /**
     * Obtiene productos con stock bajo (< 15 unidades).
     */
    public function getStockBajo(): array
    {
        return $this->query("
            SELECT nombre, color, stock, costo
            FROM pintura
            WHERE stock <= 5
            ORDER BY stock ASC
        ");
    }

    /**
     * Total de pinturas registradas.
     */
    public function count(): int
    {
        $r = $this->queryOne("SELECT COUNT(*) AS total FROM pintura");
        return (int) ($r['total'] ?? 0);
    }

    /**
     * Valor total del inventario (stock × costo).
     */
    public function valorInventario(): float
    {
        $r = $this->queryOne("SELECT COALESCE(SUM(stock * costo), 0) AS valor FROM pintura");
        return (float) ($r['valor'] ?? 0);
    }

    /**
     * Obtiene todas las clasificaciones para los selects del formulario.
     */
    public function getClasificaciones(): array
    {
        return $this->query("SELECT * FROM clasificacion ORDER BY claveClasificacion");
    }

    /**
     * Siguiente ID disponible para pintura.
     */
    public function getNextId(): int
    {
        $r = $this->queryOne("SELECT COALESCE(MAX(idPintura), 0) + 1 AS next FROM pintura");
        return (int) ($r['next'] ?? 1);
    }

    /**
     * Listado filtrable con SQL dinámico y parámetros seguros.
     * Soporta: buscar (nombre/color), proveedor, presentación y stock bajo.
     */
    public function filtrar(array $f): array
    {
        $sql = "
            SELECT p.idPintura, p.nombre, p.color, p.capacidad, p.stock,
                   p.costo, p.presentacion,
                   c.tipo AS tipoClasificacion, c.linea, c.descripcion AS descClasificacion,
                   pr.razonSocial AS proveedor
            FROM pintura p
            INNER JOIN clasificacion c  ON p.claveClasificacion = c.claveClasificacion
            INNER JOIN proveedor    pr  ON p.idProveedor         = pr.idProveedor
            WHERE 1=1";
        $params = [];

        if (!empty($f['buscar'])) {
            $like = '%' . $f['buscar'] . '%';
            $sql .= " AND (p.nombre LIKE :b1 OR p.color LIKE :b2)";
            $params['b1'] = $like;
            $params['b2'] = $like;
        }

        if (!empty($f['proveedor'])) {
            $sql .= " AND p.idProveedor = :proveedor";
            $params['proveedor'] = (int) $f['proveedor'];
        }

        if (!empty($f['presentacion'])) {
            $sql .= " AND p.presentacion = :presentacion";
            $params['presentacion'] = $f['presentacion'];
        }

        if (!empty($f['stock_bajo'])) {
            $sql .= " AND p.stock <= 5";
        }

        $sql .= " ORDER BY p.idPintura ASC";
        return $this->query($sql, $params);
    }

    /**
     * Valores distintos de presentación para el select de filtros.
     */
    public function getPresentaciones(): array
    {
        return $this->query("SELECT DISTINCT presentacion FROM pintura WHERE presentacion IS NOT NULL AND presentacion <> '' ORDER BY presentacion");
    }

    /**
     * Búsqueda por nombre o color.
     */
    public function buscar(string $termino): array
    {
        $like = "%$termino%";
        return $this->query("
            SELECT p.*, c.linea, pr.razonSocial AS proveedor
            FROM pintura p
            INNER JOIN clasificacion c ON p.claveClasificacion = c.claveClasificacion
            INNER JOIN proveedor pr ON p.idProveedor = pr.idProveedor
            WHERE p.nombre LIKE :t1 OR p.color LIKE :t2
            ORDER BY p.nombre
        ", ['t1' => $like, 't2' => $like]);
    }
}
