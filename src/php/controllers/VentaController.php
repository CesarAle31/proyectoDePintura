<?php
/**
 * ============================================================
 *  VentaController.php — Ventas y generación de tickets
 * ============================================================
 */

class VentaController extends Controller
{
    private Venta $modelo;
    private Ticket $ticketModelo;

    public function __construct()
    {
        $this->modelo = new Venta();
        $this->ticketModelo = new Ticket();
    }

    /** Listar ventas */
    public function index(): void
    {
        $this->view('ventas/index', [
            'titulo'       => 'Ventas',
            'paginaActual' => 'ventas',
            'ventas'       => $this->modelo->getAll(),
        ]);
    }

    /** Formulario de nueva venta (POS) */
    public function crear(): void
    {
        $cliente  = new Cliente();
        $producto = new Producto();
        $usuarioActual = Auth::usuario();

        $this->view('ventas/form', [
            'titulo'       => 'Nueva Venta',
            'paginaActual' => 'ventas',
            'clientes'     => $cliente->getForSelect(),
            'empleadoActual' => $usuarioActual,
            'productos'    => $producto->getAll(),
            'nextFolio'    => $this->modelo->getNextFolio(),
        ]);
    }

    /** Guardar venta (recibe JSON vía AJAX) */
    public function guardar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Método no permitido'], 405);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input || empty($input['detalles'])) {
            $this->json(['error' => 'Datos incompletos'], 400);
            return;
        }

        try {
            $usuario = Auth::usuario();
            $idEmpleado = (int) ($usuario['idEmpleado'] ?? 0);

            if ($idEmpleado <= 0) {
                $this->json(['error' => 'No se pudo identificar el empleado de la sesiÃ³n'], 401);
                return;
            }

            $venta = [
                'folio'      => $input['folio'],
                'idEmpleado' => $idEmpleado,
                'idCliente'  => $input['idCliente'],
                'fecha'      => date('Y-m-d'),
                'montoTotal' => $input['montoTotal'],
            ];

            $folio = $this->modelo->create($venta, $input['detalles']);
            $this->json(['ok' => true, 'folio' => $folio]);
        } catch (\Exception $e) {
            $this->json(['error' => $e->getMessage()], 500);
        }
    }

    /** Ver ticket de una venta */
    public function ticket(): void
    {
        $folio = (int) ($_GET['folio'] ?? 0);
        $venta = $this->modelo->getById($folio);

        if (!$venta) {
            $this->redirect('index.php?page=ventas&error=no_encontrado');
            return;
        }

        $detalles = $this->ticketModelo->getByFolio($folio);

        $this->view('ventas/ticket', [
            'titulo'       => 'Ticket #' . $folio,
            'paginaActual' => 'ventas',
            'venta'        => $venta,
            'detalles'     => $detalles,
        ]);
    }

    /** Eliminar venta */
    public function eliminar(): void
    {
        $folio = (int) ($_GET['id'] ?? 0);
        try {
            $this->modelo->delete($folio);
            $this->redirect('index.php?page=ventas&msg=eliminado');
        } catch (\Exception $e) {
            $this->redirect('index.php?page=ventas&error=' . urlencode($e->getMessage()));
        }
    }

    /** API: datos del dashboard de ventas */
    public function statsJson(): void
    {
        $this->json([
            'ventasPorMes'  => $this->modelo->ventasPorMes(),
            'topProductos'  => $this->modelo->topProductos(),
        ]);
    }
}
