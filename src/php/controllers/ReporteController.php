<?php
/**
 * ============================================================
 *  ReporteController.php — Reportes y gráficas
 * ============================================================
 */

class ReporteController extends Controller
{
    public function index(): void
    {
        $venta    = new Venta();
        $producto = new Producto();
        $empleado = new Empleado();
        $ticket   = new Ticket();

        $this->view('reportes/index', [
            'titulo'            => 'Reportes',
            'paginaActual'      => 'reportes',
            'ventasPorMes'      => $venta->ventasPorMes(),
            'topProductos'      => $venta->topProductos(),
            'empleadosVentas'   => $empleado->getConVentas(),
            'stockBajo'         => $producto->getStockBajo(),
            'auditoriaStock'    => $ticket->getAuditoriaStock(),
            'auditoriaCosto'    => $ticket->getAuditoriaCosto(),
            'auditoriaClientes' => $ticket->getAuditoriaClienteInsert(),
            'totalVentas'       => $venta->count(),
            'ingresos'          => $venta->totalIngresos(),
            'valorInventario'   => $producto->valorInventario(),
        ]);
    }

    /** Reporte filtrado por fechas (AJAX) */
    public function filtrar(): void
    {
        $desde = $_GET['desde'] ?? date('Y-m-01');
        $hasta = $_GET['hasta'] ?? date('Y-m-d');
        $venta = new Venta();
        $this->json(['data' => $venta->ventasPorRango($desde, $hasta)]);
    }
}
