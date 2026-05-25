<?php
/**
 * ============================================================
 *  DashboardController.php — Panel principal
 * ============================================================
 *  Recopila estadísticas de todos los módulos para mostrar
 *  tarjetas resumen, gráficas y alertas.
 * ============================================================
 */

class DashboardController extends Controller
{
    public function index(): void
    {
        $producto  = new Producto();
        $cliente   = new Cliente();
        $proveedor = new Proveedor();
        $empleado  = new Empleado();
        $venta     = new Venta();

        $this->view('dashboard/index', [
            'titulo'          => 'Dashboard',
            'paginaActual'    => 'dashboard',
            'totalProductos'  => $producto->count(),
            'totalClientes'   => $cliente->count(),
            'totalProveedores'=> $proveedor->count(),
            'totalEmpleados'  => $empleado->count(),
            'totalVentas'     => $venta->count(),
            'ingresos'        => $venta->totalIngresos(),
            'valorInventario' => $producto->valorInventario(),
            'stockBajo'       => $producto->getStockBajo(),
            'ventasPorMes'    => $venta->ventasPorMes(),
            'topProductos'    => $venta->topProductos(),
            'ventasRecientes' => array_slice($venta->getAll(), 0, 5),
        ]);
    }
}
