<?php
/**
 * ============================================================
 *  AuthController.php — Login, autenticación y logout
 * ============================================================
 *  - login()        Renderiza el formulario.
 *  - authenticate() Valida credenciales con password_verify().
 *  - logout()       Cierra sesión y redirige al login.
 * ============================================================
 */

class AuthController extends Controller
{
    private AuthModel $modelo;

    public function __construct()
    {
        $this->modelo = new AuthModel();
    }

    /** Muestra el formulario de login. */
    public function index(): void
    {
        $this->login();
    }

    public function login(): void
    {
        $this->viewSinLayout('auth/login', [
            'error'         => $_GET['error']   ?? null,
            'usuarioPrevio' => $_GET['usuario'] ?? '',
        ]);
    }

    /** Procesa el POST del formulario de login. */
    public function authenticate(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?page=login');
            return;
        }

        $usuario = trim((string) ($_POST['usuario']    ?? ''));
        $clave   =        (string) ($_POST['password'] ?? '');

        if ($usuario === '' || $clave === '') {
            $this->redirect('index.php?page=login&error=' . urlencode('Ingresa usuario y contraseña.'));
            return;
        }

        $datos = $this->modelo->buscarPorUsuario($usuario);

        // Mensaje genérico para no revelar si existe o no el usuario
        if (!$datos || !password_verify($clave, $datos['password_hash'])) {
            $this->redirect(
                'index.php?page=login'
                . '&usuario=' . urlencode($usuario)
                . '&error='   . urlencode('Usuario o contraseña incorrectos.')
            );
            return;
        }

        Auth::iniciarSesion($datos);
        $this->redirect('index.php?page=' . Auth::paginaInicio());
    }

    public function logout(): void
    {
        Auth::cerrarSesion();
        $this->redirect('index.php?page=login');
    }
}