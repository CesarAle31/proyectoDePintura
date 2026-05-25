<?php
class ProveedorController extends Controller
{
    private Proveedor $modelo;

    public function __construct() { $this->modelo = new Proveedor(); }

    public function index(): void
    {
        $this->view('proveedores/index', [
            'titulo'       => 'Proveedores',
            'paginaActual' => 'proveedores',
            'proveedores'  => $this->modelo->getAll(),
        ]);
    }

    public function crear(): void
    {
        $cliente = new Cliente();
        $this->view('proveedores/form', [
            'titulo'       => 'Nuevo Proveedor',
            'paginaActual' => 'proveedores',
            'accion'       => 'guardar',
            'proveedor'    => null,
            'colonias'     => $cliente->getColonias(),
        ]);
    }

    public function guardar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->redirect('index.php?page=proveedores'); return; }
        try {
            $this->modelo->create($_POST);
            $this->redirect('index.php?page=proveedores&msg=creado');
        } catch (\Exception $e) {
            $this->redirect('index.php?page=proveedores&action=crear&error=' . urlencode($e->getMessage()));
        }
    }

    public function editar(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $prov = $this->modelo->getById($id);
        if (!$prov) { $this->redirect('index.php?page=proveedores&error=no_encontrado'); return; }
        $cliente = new Cliente();
        $this->view('proveedores/form', [
            'titulo'       => 'Editar Proveedor',
            'paginaActual' => 'proveedores',
            'accion'       => 'actualizar',
            'proveedor'    => $prov,
            'colonias'     => $cliente->getColonias(),
        ]);
    }

    public function actualizar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->redirect('index.php?page=proveedores'); return; }
        $id = (int) ($_POST['idProveedor'] ?? 0);
        try {
            $this->modelo->update($id, $_POST);
            $this->redirect('index.php?page=proveedores&msg=actualizado');
        } catch (\Exception $e) {
            $this->redirect('index.php?page=proveedores&error=' . urlencode($e->getMessage()));
        }
    }

    public function eliminar(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        try {
            $this->modelo->delete($id);
            $this->redirect('index.php?page=proveedores&msg=eliminado');
        } catch (\Exception $e) {
            $this->redirect('index.php?page=proveedores&error=' . urlencode($e->getMessage()));
        }
    }
}
