<?php
/**
 * ============================================================
 *  Database.php — Clase Singleton para conexión PDO
 * ============================================================
 *  Patrón Singleton: solo crea UNA conexión y la reutiliza.
 *  Usa PDO con modo de errores en excepción para que cualquier
 *  fallo de SQL se atrape con try/catch.
 * ============================================================
 */

class Database
{
    /** @var PDO|null  Instancia única de la conexión */
    private static ?PDO $conexion = null;

    /**
     * Devuelve la conexión PDO. Si no existe, la crea.
     */
    public static function getConexion(): PDO
    {
        if (self::$conexion === null) {
            $host    = $_ENV['DB_HOST']    ?? 'localhost';
            $port    = $_ENV['DB_PORT']    ?? '3306';
            $dbname  = $_ENV['DB_NAME']    ?? 'pinturadb';
            $user    = $_ENV['DB_USER']    ?? '';
            $pass    = $_ENV['DB_PASS']    ?? '';
            $charset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';

            $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=$charset";

            try {
                self::$conexion = new PDO($dsn, $user, $pass, [
                    // Lanza excepciones en errores SQL
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    // Devuelve resultados como arreglos asociativos
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    // Desactiva emulación de sentencias preparadas (más seguro)
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                die('❌ Error de conexión a la base de datos: ' . $e->getMessage());
            }
        }

        return self::$conexion;
    }

    /** Evita clonar la instancia */
    private function __clone() {}
}
