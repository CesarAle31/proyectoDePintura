<?php
/**
 * ============================================================
 *  ColorMax POS — Punto de entrada principal (Front Controller)
 * ============================================================
 *  Todas las peticiones pasan por aquí.
 *  Lee la variable ?page= para decidir qué controlador cargar.
 *  Si no hay page, muestra el Dashboard.
 * ============================================================
 */

// ── Carga de configuración ───────────────────────────────────
require_once __DIR__ . '/src/php/config/conexion.php';
require_once __DIR__ . '/src/php/core/Database.php';
require_once __DIR__ . '/src/php/core/Controller.php';
require_once __DIR__ . '/src/php/core/Model.php';

// ── Modelos ──────────────────────────────────────────────────
require_once __DIR__ . '/src/php/models/Producto.php';
require_once __DIR__ . '/src/php/models/Cliente.php';
require_once __DIR__ . '/src/php/models/Proveedor.php';
require_once __DIR__ . '/src/php/models/Empleado.php';
require_once __DIR__ . '/src/php/models/Venta.php';
require_once __DIR__ . '/src/php/models/Ticket.php';

// ── Controladores ────────────────────────────────────────────
require_once __DIR__ . '/src/php/controllers/DashboardController.php';
require_once __DIR__ . '/src/php/controllers/ProductoController.php';
require_once __DIR__ . '/src/php/controllers/ClienteController.php';
require_once __DIR__ . '/src/php/controllers/ProveedorController.php';
require_once __DIR__ . '/src/php/controllers/EmpleadoController.php';
require_once __DIR__ . '/src/php/controllers/VentaController.php';
require_once __DIR__ . '/src/php/controllers/ReporteController.php';

// ── Router simple ────────────────────────────────────────────
$page   = $_GET['page']   ?? 'dashboard';
$action = $_GET['action'] ?? 'index';

// Mapa de páginas → controladores
$routes = [
    'dashboard'    => 'DashboardController',
    'productos'    => 'ProductoController',
    'clientes'     => 'ClienteController',
    'proveedores'  => 'ProveedorController',
    'empleados'    => 'EmpleadoController',
    'ventas'       => 'VentaController',
    'reportes'     => 'ReporteController',
];

// Verificar que la página exista
if (!isset($routes[$page])) {
    $page = 'dashboard';
}

$controllerName = $routes[$page];
$controller     = new $controllerName();

// Verificar que la acción (método) exista
if (method_exists($controller, $action)) {
    $controller->$action();
} else {
    $controller->index();
}
