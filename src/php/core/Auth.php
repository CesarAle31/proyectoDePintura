<?php
/**
 * ============================================================
 *  Auth.php — Helper de sesión y permisos por rol
 * ============================================================
 *  Centraliza el acceso a $_SESSION y la matriz de permisos.
 *  Los controladores y vistas NO deben tocar $_SESSION
 *  directamente: deben usar Auth::usuario(), Auth::rol(), etc.
 *
 *  Matriz de permisos:
 *    'rw' = puede ver y modificar (crear/editar/eliminar)
 *    'r'  = solo lectura (index, listados, consultas JSON)
 *    '*'  = acceso total a todas las páginas (Administrador)
 * ============================================================
 */

class Auth
{
    /** Acciones que escriben en BD — bloqueadas para roles con 'r'. */
    private const ACCIONES_ESCRITURA = [
        'crear',
        'guardar',
        'editar',
        'actualizar',
        'eliminar',
    ];

    /** Matriz de permisos: rol => página => 'rw' | 'r' (o '*' para todos). */
    private const PERMISOS = [
        'Administrador' => '*',
        'Gerente' => [
            'dashboard'   => 'rw',
            'productos'   => 'rw',
            'clientes'    => 'rw',
            'ventas'      => 'rw',
            'reportes'    => 'rw',
        ],
        'Cajero' => [
            'ventas'      => 'rw',
            'clientes'    => 'rw',
            'productos'   => 'r',
        ],
        'Almacenista' => [
            'productos'   => 'rw',
            'proveedores' => 'rw',
        ],
        'Consulta' => [
            'dashboard'   => 'r',
            'productos'   => 'r',
            'clientes'    => 'r',
            'proveedores' => 'r',
            'ventas'      => 'r',
            'reportes'    => 'r',
        ],
    ];

    // ─── Estado de sesión ────────────────────────────────────

    public static function estaLogueado(): bool
    {
        return isset($_SESSION['idUsuario']);
    }

    public static function usuario(): ?array
    {
        if (!self::estaLogueado()) return null;
        return [
            'idUsuario'      => $_SESSION['idUsuario'],
            'idEmpleado'     => $_SESSION['idEmpleado']     ?? null,
            'usuario'        => $_SESSION['usuario']        ?? '',
            'nombreCompleto' => $_SESSION['nombreCompleto'] ?? '',
            'rol'            => $_SESSION['rol']            ?? '',
        ];
    }

    public static function rol(): ?string
    {
        return $_SESSION['rol'] ?? null;
    }

    public static function nombreCompleto(): ?string
    {
        return $_SESSION['nombreCompleto'] ?? null;
    }

    /**
     * Guarda los datos del usuario en sesión tras un login exitoso.
     * Regenera el ID para prevenir fijación de sesión.
     */
    public static function iniciarSesion(array $usuario): void
    {
        session_regenerate_id(true);
        $_SESSION['idUsuario']      = (int) $usuario['idUsuario'];
        $_SESSION['idEmpleado']     = (int) $usuario['idEmpleado'];
        $_SESSION['usuario']        = $usuario['usuario'];
        $_SESSION['nombreCompleto'] = trim(
            $usuario['empNombre'] . ' ' .
            $usuario['empApellidoP'] . ' ' .
            $usuario['empApellidoM']
        );
        $_SESSION['rol']            = $usuario['rolNombre'];
    }

    public static function cerrarSesion(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        session_destroy();
    }

    // ─── Permisos ────────────────────────────────────────────

    /**
     * Página a la que se redirige al rol tras login (o tras un
     * intento de acceso denegado). Toma la primera con permiso.
     */
    public static function paginaInicio(): string
    {
        $rol = self::rol();
        if ($rol === null) return 'login';

        $permisos = self::PERMISOS[$rol] ?? [];
        if ($permisos === '*') return 'dashboard';

        // Orden de preferencia para la pantalla de inicio
        foreach (['dashboard', 'ventas', 'productos', 'clientes', 'proveedores', 'reportes'] as $page) {
            if (isset($permisos[$page])) return $page;
        }
        return 'login';
    }

    public static function puedeVer(string $page): bool
    {
        $rol = self::rol();
        if ($rol === null) return false;

        $permisos = self::PERMISOS[$rol] ?? [];
        if ($permisos === '*') return true;

        return isset($permisos[$page]);
    }

    public static function puedeEscribir(string $page): bool
    {
        $rol = self::rol();
        if ($rol === null) return false;

        $permisos = self::PERMISOS[$rol] ?? [];
        if ($permisos === '*') return true;

        return ($permisos[$page] ?? null) === 'rw';
    }

    /**
     * Comprueba si un usuario puede ejecutar (page, action).
     * Bloquea acciones de escritura cuando el rol es 'r'.
     */
    public static function puedeAcceder(string $page, string $action): bool
    {
        if (!self::puedeVer($page)) return false;

        if (in_array($action, self::ACCIONES_ESCRITURA, true)) {
            return self::puedeEscribir($page);
        }
        return true;
    }
}
