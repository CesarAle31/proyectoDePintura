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

    /** @var array<string, array<string, bool>> Cache de columnas por tabla */
    private static array $columnasPorTabla = [];

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

    /**
     * Indica si una tabla contiene una columna en la BD conectada.
     * Sirve para soportar los dos nombres usados en los scripts del proyecto
     * y en la base local descrita por el profesor (idColonia/claveColonia,
     * colonia/nombreColonia, telefonoCliente/telefono, etc.).
     */
    protected function hasColumn(string $tabla, string $columna): bool
    {
        $this->assertIdentifier($tabla);
        $this->assertIdentifier($columna);
        $columnas = $this->getColumnasTabla($tabla);
        return isset($columnas[strtolower($columna)]);
    }

    /**
     * Devuelve la primera columna existente de la lista de candidatos.
     */
    protected function pickColumn(string $tabla, array $candidatas): string
    {
        foreach ($candidatas as $columna) {
            $this->assertIdentifier($columna);
            if ($this->hasColumn($tabla, $columna)) {
                return $columna;
            }
        }

        // Fallback para entornos de instalación incompleta: deja que MySQL
        // reporte el error real usando el primer nombre esperado.
        return $candidatas[0];
    }

    /**
     * Carga y cachea los nombres de columnas de una tabla.
     *
     * @return array<string, bool>
     */
    private function getColumnasTabla(string $tabla): array
    {
        $clave = strtolower($tabla);
        if (!array_key_exists($clave, self::$columnasPorTabla)) {
            $stmt = $this->db->prepare(
                "SELECT COLUMN_NAME
                 FROM information_schema.COLUMNS
                 WHERE TABLE_SCHEMA = DATABASE()
                   AND TABLE_NAME = :tabla"
            );
            $stmt->execute(['tabla' => $tabla]);

            $columnas = [];
            foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $columna) {
                $columnas[strtolower((string) $columna)] = true;
            }
            self::$columnasPorTabla[$clave] = $columnas;
        }

        return self::$columnasPorTabla[$clave];
    }

    private function assertIdentifier(string $identifier): void
    {
        if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $identifier)) {
            throw new InvalidArgumentException('Identificador SQL no válido: ' . $identifier);
        }
    }
}
