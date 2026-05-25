<?php
/**
 * ============================================================
 *  ClienteController.php — CRUD de Clientes
 * ============================================================
 */

class ClienteController extends Controller
{
    private Cliente $modelo;

    public function __construct()
    {
        $this->modelo = new Cliente();
    }

    public function index(): void
    {
        $this->view('clientes/index', [
            'titulo'       => 'Clientes',
            'paginaActual' => 'clientes',
            'clientes'     => $this->modelo->getAll(),
        ]);
    }

    public function crear(): void
    {
        $this->view('clientes/form', [
            'titulo'       => 'Nuevo Cliente',
            'paginaActual' => 'clientes',
            'accion'       => 'guardar',
            'cliente'      => null,
            'colonias'     => $this->modelo->getColonias(),
        ]);
    }

    public function guardar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?page=clientes');
            return;
        }
        try {
            $this->modelo->create($_POST);
            $this->redirect('index.php?page=clientes&msg=creado');
        } catch (\Exception $e) {
            $this->redirect('index.php?page=clientes&action=crear&error=' . urlencode($e->getMessage()));
        }
    }

    public function editar(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $cliente = $this->modelo->getById($id);
        if (!$cliente) {
            $this->redirect('index.php?page=clientes&error=no_encontrado');
            return;
        }
        $this->view('clientes/form', [
            'titulo'       => 'Editar Cliente',
            'paginaActual' => 'clientes',
            'accion'       => 'actualizar',
            'cliente'      => $cliente,
            'colonias'     => $this->modelo->getColonias(),
        ]);
    }

    public function actualizar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?page=clientes');
            return;
        }
        $id = (int) ($_POST['idCliente'] ?? 0);
        try {
            $this->modelo->update($id, $_POST);
            $this->redirect('index.php?page=clientes&msg=actualizado');
        } catch (\Exception $e) {
            $this->redirect('index.php?page=clientes&error=' . urlencode($e->getMessage()));
        }
    }

    public function eliminar(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        try {
            $this->modelo->delete($id);
            $this->redirect('index.php?page=clientes&msg=eliminado');
        } catch (\Exception $e) {
            $this->redirect('index.php?page=clientes&error=' . urlencode($e->getMessage()));
        }
    }
}
