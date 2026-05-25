<?php
/**
 * ============================================================
 *  Model.php — Clase base para todos los modelos
 * ============================================================
 *  Proporciona acceso a la conexión PDO y métodos genéricos
 *  que heredan todos los modelos (Producto, Cliente, etc.).
 * ============================================================
 */

class Model
{
    /** @var PDO  Conexión a la base de datos */
    protected PDO $db;

    public function __construct()
    {
        $this->db = Database::getConexion();
    }

    /**
     * Ejecuta una consulta preparada y devuelve todos los resultados.
     */
    protected function query(string $sql, array $params = []): array
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Ejecuta una consulta preparada y devuelve UN solo registro.
     */
    protected function queryOne(string $sql, array $params = []): ?array
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Ejecuta INSERT/UPDATE/DELETE y devuelve filas afectadas.
     */
    protected function execute(string $sql, array $params = []): int
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    /**
     * Devuelve el último ID insertado (AUTO_INCREMENT).
     */
    protected function lastInsertId(): string
    {
        return $this->db->lastInsertId();
    }
}
