<?php
/**
 * ============================================================
 *  ProductoController.php — CRUD de Pinturas/Productos
 * ============================================================
 */

class ProductoController extends Controller
{
    private Producto $modelo;
    private Proveedor $proveedorModelo;

    public function __construct()
    {
        $this->modelo = new Producto();
        $this->proveedorModelo = new Proveedor();
    }

    /** Listar productos con filtros avanzados */
    public function index(): void
    {
        $filtros = [
            'buscar'      => trim($_GET['buscar']      ?? ''),
            'proveedor'   => (int) ($_GET['proveedor']   ?? 0),
            'presentacion' => trim($_GET['presentacion'] ?? ''),
            'stock_bajo'  => !empty($_GET['stock_bajo']),
        ];

        $hayFiltros = $filtros['buscar'] !== '' || $filtros['proveedor'] > 0
                   || $filtros['presentacion'] !== '' || $filtros['stock_bajo'];

        $this->view('productos/index', [
            'titulo'         => 'Productos / Pinturas',
            'paginaActual'   => 'productos',
            'productos'      => $hayFiltros ? $this->modelo->filtrar($filtros) : $this->modelo->getAll(),
            'filtros'        => $filtros,
            'proveedores'    => $this->proveedorModelo->getForSelect(),
            'presentaciones' => $this->modelo->getPresentaciones(),
        ]);
    }

    /** Mostrar formulario de creación */
    public function crear(): void
    {
        $this->view('productos/form', [
            'titulo'          => 'Nueva Pintura',
            'paginaActual'    => 'productos',
            'accion'          => 'guardar',
            'producto'        => null,
            'clasificaciones' => $this->modelo->getClasificaciones(),
            'proveedores'     => $this->proveedorModelo->getForSelect(),
            'nextId'          => $this->modelo->getNextId(),
        ]);
    }

    /** Guardar nuevo producto */
    public function guardar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?page=productos');
            return;
        }

        try {
            $this->modelo->create($_POST);
            $this->redirect('index.php?page=productos&msg=creado');
        } catch (\Exception $e) {
            $this->redirect('index.php?page=productos&action=crear&error=' . urlencode($e->getMessage()));
        }
    }

    /** Mostrar formulario de edición */
    public function editar(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $producto = $this->modelo->getById($id);

        if (!$producto) {
            $this->redirect('index.php?page=productos&error=no_encontrado');
            return;
        }

        $this->view('productos/form', [
            'titulo'          => 'Editar Pintura',
            'paginaActual'    => 'productos',
            'accion'          => 'actualizar',
            'producto'        => $producto,
            'clasificaciones' => $this->modelo->getClasificaciones(),
            'proveedores'     => $this->proveedorModelo->getForSelect(),
            'nextId'          => null,
        ]);
    }

    /** Actualizar producto existente */
    public function actualizar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?page=productos');
            return;
        }

        $id = (int) ($_POST['idPintura'] ?? 0);

        try {
            $this->modelo->update($id, $_POST);
            $this->redirect('index.php?page=productos&msg=actualizado');
        } catch (\Exception $e) {
            $this->redirect('index.php?page=productos&error=' . urlencode($e->getMessage()));
        }
    }

    /** Eliminar producto */
    public function eliminar(): void
    {
        $id = (int) ($_GET['id'] ?? 0);

        try {
            $this->modelo->delete($id);
            $this->redirect('index.php?page=productos&msg=eliminado');
        } catch (\Exception $e) {
            $this->redirect('index.php?page=productos&error=' . urlencode($e->getMessage()));
        }
    }

    /** API: Búsqueda AJAX */
    public function buscar(): void
    {
        $termino = $_GET['q'] ?? '';
        $resultados = $this->modelo->buscar($termino);
        $this->json(['data' => $resultados]);
    }

    /** API: Obtener producto por ID (para ventas) */
    public function getJson(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $producto = $this->modelo->getById($id);
        $this->json($producto ? ['data' => $producto] : ['error' => 'No encontrado'], $producto ? 200 : 404);
    }

    /** API: Lista para select (ventas) */
    public function listaJson(): void
    {
        $productos = $this->modelo->getAll();
        $this->json(['data' => $productos]);
    }
}
