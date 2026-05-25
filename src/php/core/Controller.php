<?php
/**
 * ============================================================
 *  Controller.php — Clase base para todos los controladores
 * ============================================================
 *  Proporciona el método view() que carga las vistas dentro
 *  del layout principal (sidebar + header + contenido).
 * ============================================================
 */

class Controller
{
    /**
     * Renderiza una vista dentro del layout principal.
     *
     * @param string $vista   Ruta relativa desde views/ (ej: 'dashboard/index')
     * @param array  $datos   Variables disponibles en la vista
     */
    protected function view(string $vista, array $datos = []): void
    {
        // Extrae las claves del arreglo como variables
        // ['titulo' => 'Hola'] → $titulo = 'Hola'
        extract($datos);

        // Ruta base de las vistas
        $rutaBase = __DIR__ . '/../views/';

        // Guardar la ruta de la vista para usarla dentro del layout
        $contenidoVista = $rutaBase . $vista . '.php';

        // Cargar el layout principal (que incluirá sidebar + header + vista)
        require $rutaBase . 'layouts/main.php';
    }

    /**
     * Renderiza una vista SIN el layout principal (login, etc.).
     * La vista es responsable de su propio <html>...</html>.
     */
    protected function viewSinLayout(string $vista, array $datos = []): void
    {
        extract($datos);
        require __DIR__ . '/../views/' . $vista . '.php';
    }

    /**
     * Responde con JSON (para peticiones AJAX).
     */
    protected function json(array $datos, int $codigo = 200): void
    {
        http_response_code($codigo);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Redirecciona a otra página.
     */
    protected function redirect(string $url): void
    {
        header("Location: $url");
        exit;
    }
}
