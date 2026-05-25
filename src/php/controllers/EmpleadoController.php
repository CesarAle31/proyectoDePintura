<?php
class EmpleadoController extends Controller
{
    private Empleado $modelo;

    public function __construct() { $this->modelo = new Empleado(); }

    public function index(): void
    {
        $this->view('empleados/index', [
            'titulo'       => 'Empleados',
            'paginaActual' => 'empleados',
            'empleados'    => $this->modelo->getAll(),
        ]);
    }

    public function crear(): void
    {
        $this->view('empleados/form', [
            'titulo'       => 'Nuevo Empleado',
            'paginaActual' => 'empleados',
            'accion'       => 'guardar',
            'empleado'     => null,
            'nextId'       => $this->modelo->getNextId(),
        ]);
    }

    public function guardar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->redirect('index.php?page=empleados'); return; }
        try {
            $this->modelo->create($_POST);
            $this->redirect('index.php?page=empleados&msg=creado');
        } catch (\Exception $e) {
            $this->redirect('index.php?page=empleados&action=crear&error=' . urlencode($e->getMessage()));
        }
    }

    public function editar(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $emp = $this->modelo->getById($id);
        if (!$emp) { $this->redirect('index.php?page=empleados&error=no_encontrado'); return; }
        $this->view('empleados/form', [
            'titulo'       => 'Editar Empleado',
            'paginaActual' => 'empleados',
            'accion'       => 'actualizar',
            'empleado'     => $emp,
            'nextId'       => null,
        ]);
    }

    public function actualizar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->redirect('index.php?page=empleados'); return; }
        $id = (int) ($_POST['idEmpleado'] ?? 0);
        try {
            $this->modelo->update($id, $_POST);
            $this->redirect('index.php?page=empleados&msg=actualizado');
        } catch (\Exception $e) {
            $this->redirect('index.php?page=empleados&error=' . urlencode($e->getMessage()));
        }
    }

    public function eliminar(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        try {
            $this->modelo->delete($id);
            $this->redirect('index.php?page=empleados&msg=eliminado');
        } catch (\Exception $e) {
            $this->redirect('index.php?page=empleados&error=' . urlencode($e->getMessage()));
        }
    }
}
