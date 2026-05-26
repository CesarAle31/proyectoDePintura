<?php
/**
 * ============================================================
 *  EmpleadoController.php — CRUD de Empleados
 * ============================================================
 */

class EmpleadoController extends Controller
{
    private Empleado $modelo;

    public function __construct()
    {
        $this->modelo = new Empleado();
    }

    // ─── SELECT / Listar ────────────────────────────────────

    public function index(): void
    {
        $filtros = [
            'buscar' => trim($_GET['buscar'] ?? ''),
            'rol'    => (int) ($_GET['rol']    ?? 0),
            'activo' => $_GET['activo'] ?? '',
        ];

        $hayFiltros = $filtros['buscar'] !== ''
                   || $filtros['rol']    > 0
                   || $filtros['activo'] === '0';

        $empleados = $hayFiltros
            ? $this->modelo->filtrar($filtros)
            : $this->modelo->getAll();

        // Detectar búsqueda por ID que no dio resultados
        $buscoPorId = $filtros['buscar'] !== '' && ctype_digit($filtros['buscar']);
        $noEncontradoPorId = $buscoPorId && empty($empleados);

        $this->view('empleados/index', [
            'titulo'            => 'Empleados',
            'paginaActual'      => 'empleados',
            'empleados'         => $empleados,
            'filtros'           => $filtros,
            'roles'             => $this->modelo->getRoles(),
            'noEncontradoPorId' => $noEncontradoPorId,
        ]);
    }

    // ─── INSERT / Formulario de creación ────────────────────

    public function crear(): void
    {
        $this->view('empleados/form', [
            'titulo'       => 'Nuevo Empleado',
            'paginaActual' => 'empleados',
            'accion'       => 'guardar',
            'empleado'     => null,
            'nextId'       => $this->modelo->getNextId(),
            'roles'        => $this->modelo->getRoles(),
        ]);
    }

    // ─── INSERT / Guardar ────────────────────────────────────

    public function guardar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?page=empleados');
            return;
        }
        try {
            $this->modelo->create($_POST);
            $this->redirect('index.php?page=empleados&msg=creado');
        } catch (\Exception $e) {
            $this->redirect(
                'index.php?page=empleados&action=crear&error=' . urlencode($e->getMessage())
            );
        }
    }

    // ─── UPDATE / Formulario de edición ─────────────────────

    public function editar(): void
    {
        $id  = (int) ($_GET['id'] ?? 0);
        $emp = $this->modelo->getById($id);

        if (!$emp || !$emp['activo']) {
            $this->redirect(
                'index.php?page=empleados&error=' . urlencode('No existe un empleado con ese ID.')
            );
            return;
        }

        $this->view('empleados/form', [
            'titulo'       => 'Editar Empleado',
            'paginaActual' => 'empleados',
            'accion'       => 'actualizar',
            'empleado'     => $emp,
            'nextId'       => null,
            'roles'        => $this->modelo->getRoles(),
        ]);
    }

    // ─── UPDATE / Guardar cambios ────────────────────────────

    public function actualizar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?page=empleados');
            return;
        }
        $id = (int) ($_POST['idEmpleado'] ?? 0);
        try {
            $this->modelo->update($id, $_POST);
            $this->redirect('index.php?page=empleados&msg=actualizado');
        } catch (\Exception $e) {
            $this->redirect(
                'index.php?page=empleados&action=editar&id=' . $id
                . '&error=' . urlencode($e->getMessage())
            );
        }
    }

    // ─── DELETE / Baja lógica ────────────────────────────────

    public function eliminar(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        try {
            $this->modelo->delete($id);
            $this->redirect('index.php?page=empleados&msg=eliminado');
        } catch (\Exception $e) {
            $this->redirect(
                'index.php?page=empleados&error=' . urlencode($e->getMessage())
            );
        }
    }
}
