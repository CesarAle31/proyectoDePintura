<?php
/**
 * ============================================================
 *  Configuración de conexión — Lee variables del .env
 * ============================================================
 *  Carga credenciales desde un archivo .env opcional ubicado en
 *  la raíz del proyecto. Si no existe, el sistema usa valores
 *  locales por defecto para Laragon/XAMPP y permite sobreescribir
 *  con variables de entorno del sistema.
 * ============================================================
 */

function cargarEnv(string $ruta): void
{
    if (!file_exists($ruta)) {
        return;
    }

    $lineas = file($ruta, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lineas as $linea) {
        $linea = trim($linea);

        // Ignorar comentarios o líneas sin asignación
        if ($linea === '' || str_starts_with($linea, '#') || !str_contains($linea, '=')) {
            continue;
        }

        // Separar clave=valor
        [$clave, $valor] = explode('=', $linea, 2);
        $clave = trim($clave);
        $valor = trim($valor, " \t\n\r\0\x0B\"'");

        // No pisar variables definidas por Apache, Laragon, Docker, etc.
        if (getenv($clave) !== false) {
            $_ENV[$clave] = getenv($clave);
            continue;
        }

        // Guardar en variables de entorno
        $_ENV[$clave] = $valor;
        putenv("$clave=$valor");
    }
}

// Cargar el .env desde la raíz del proyecto. Es opcional para facilitar
// el uso directo con una base local llamada pinturadb.
cargarEnv(__DIR__ . '/../../../.env');
