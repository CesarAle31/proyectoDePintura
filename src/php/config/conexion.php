<?php
/**
 * ============================================================
 *  Configuración de conexión — Lee variables del .env
 * ============================================================
 *  Carga las credenciales desde el archivo .env ubicado en la
 *  raíz del proyecto. Se usa para que las contraseñas no estén
 *  directamente en el código fuente.
 * ============================================================
 */

function cargarEnv(string $ruta): void
{
    if (!file_exists($ruta)) {
        die('⚠️  No se encontró el archivo .env — Crea uno en la raíz del proyecto.');
    }

    $lineas = file($ruta, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lineas as $linea) {
        // Ignorar comentarios
        if (str_starts_with(trim($linea), '#')) continue;

        // Separar clave=valor
        [$clave, $valor] = explode('=', $linea, 2);
        $clave = trim($clave);
        $valor = trim($valor);

        // Guardar en variables de entorno
        $_ENV[$clave]    = $valor;
        putenv("$clave=$valor");
    }
}

// Cargar el .env desde la raíz del proyecto
cargarEnv(__DIR__ . '/../../../.env');
