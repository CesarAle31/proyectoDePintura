<?php
/**
 * ============================================================
 *  hash_password.php — Utilidad CLI para generar hashes bcrypt
 * ============================================================
 *  Uso (desde la terminal, en la raíz del proyecto):
 *
 *    php sql/hash_password.php <contraseña>
 *
 *  Copia el hash que imprime y pégalo en el INSERT de
 *  sql/usuarios.sql en la columna password_hash.
 *
 *  ⚠️  No commitees contraseñas ni hashes reales al repositorio.
 * ============================================================
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("Esta utilidad solo se ejecuta desde la línea de comandos.\n");
}

if ($argc < 2) {
    fwrite(STDERR, "Uso: php sql/hash_password.php <contraseña>\n");
    exit(1);
}

$clave = $argv[1];
$hash  = password_hash($clave, PASSWORD_BCRYPT);

echo $hash . PHP_EOL;