<?php
/**
 * ============================================================
 *  Ipesa Pinturas POS — Punto de entrada principal
 * ============================================================
 *  Todas las peticiones pasan por aquí.
 *  Lee la variable ?page= para decidir qué controlador cargar.
 *  Si no hay sesión activa, redirige al formulario de login.
 * ============================================================
 */

// ── Sesión ───────────────────────────────────────────────────
session_start();

// ── Carga de configuración ───────────────────────────────────
require_once __DIR__ . '/src/php/config/conexion.php';
require_once __DIR__ . '/src/php/core/Database.php';
require_once __DIR__ . '/src/php/core/Controller.php';
require_once __DIR__ . '/src/php/core/Model.php';
require_once __DIR__ . '/src/php/core/Auth.php';

// ── Modelos ──────────────────────────────────────────────────
require_once __DIR__ . '/src/php/models/Producto.php';
require_once __DIR__ . '/src/php/models/Cliente.php';
require_once __DIR__ . '/src/php/models/Proveedor.php';
require_once __DIR__ . '/src/php/models/Empleado.php';
require_once __DIR__ . '/src/php/models/Venta.php';
require_once __DIR__ . '/src/php/models/Ticket.php';
require_once __DIR__ . '/src/php/models/AuthModel.php';

// ── Controladores ────────────────────────────────────────────
require_once __DIR__ . '/src/php/controllers/DashboardController.php';
require_once __DIR__ . '/src/php/controllers/ProductoController.php';
require_once __DIR__ . '/src/php/controllers/ClienteController.php';
require_once __DIR__ . '/src/php/controllers/ProveedorController.php';
require_once __DIR__ . '/src/php/controllers/EmpleadoController.php';
require_once __DIR__ . '/src/php/controllers/VentaController.php';
require_once __DIR__ . '/src/php/controllers/ReporteController.php';
require_once __DIR__ . '/src/php/controllers/AuthController.php';

// ── Router simple ────────────────────────────────────────────
$page   = $_GET['page']   ?? 'dashboard';
$action = $_GET['action'] ?? 'index';

// Mapa de páginas → controladores
$routes = [
    'login'        => 'AuthController',
    'dashboard'    => 'DashboardController',
    'productos'    => 'ProductoController',
    'clientes'     => 'ClienteController',
    'proveedores'  => 'ProveedorController',
    'empleados'    => 'EmpleadoController',
    'ventas'       => 'VentaController',
    'reportes'     => 'ReporteController',
];

// ── Gate de autenticación ────────────────────────────────────
// Página/acciones permitidas sin sesión iniciada.
$rutasPublicas = [
    'login' => ['index', 'login', 'authenticate'],
];

$logueado = Auth::estaLogueado();

if (!$logueado) {
    $accionesPermitidas = $rutasPublicas[$page] ?? [];
    if (!in_array($action, $accionesPermitidas, true)) {
        header('Location: index.php?page=login');
        exit;
    }
}

// Si ya está logueado y pide el formulario de login, lo mandamos a su inicio.
if ($logueado && $page === 'login' && $action !== 'logout') {
    header('Location: index.php?page=' . Auth::paginaInicio());
    exit;
}

// Página inexistente → cae a la página de inicio del rol (o login).
if (!isset($routes[$page])) {
    $page = $logueado ? Auth::paginaInicio() : 'login';
}

// ── Gate de permisos por rol ─────────────────────────────────
if ($logueado && $page !== 'login' && !Auth::puedeAcceder($page, $action)) {
    header(
        'Location: index.php?page=' . Auth::paginaInicio()
        . '&error=' . urlencode('Acceso denegado: no tienes permiso para esa acción.')
    );
    exit;
}

// ── Despacho ─────────────────────────────────────────────────
$controllerName = $routes[$page];
$controller     = new $controllerName();

if (method_exists($controller, $action)) {
    $controller->$action();
} else {
    $controller->index();
}