# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**ColorMax POS** — a point-of-sale and inventory management system for a paint store, built as a custom lightweight PHP MVC application. No Composer, no npm. All external libraries (Tailwind CSS, SweetAlert2, Google Fonts) are loaded via CDN.

## Running the App

Requires **Laragon** (local dev server at `C:\laragon\www`).

- Start Laragon and ensure Apache + MySQL are running.
- Access via: `http://localhost/pinturas-pos-colormax/`
- Database: `pinturadb` on `localhost:3306` (credentials in `.env`)

There are no build or test commands — this is a pure PHP app.

## Architecture

### Routing

All requests go through `index.php` (front controller). Routing uses query parameters:

```
?page=<module>&action=<method>&id=<id>
```

Supported pages: `dashboard`, `productos`, `clientes`, `proveedores`, `empleados`, `ventas`, `reportes`. Default is `dashboard`.

### MVC Structure (`src/php/`)

- `config/` — loads `.env` and defines constants
- `core/Database.php` — PDO singleton; reads DB config from env
- `core/Model.php` — base model with `query()`, `queryOne()`, `execute()` helpers
- `core/Controller.php` — base controller with `view()` (renders with layout), `json()`, and `redirect()`
- `controllers/` — one controller per module; `index()` is the default action
- `models/` — one model per entity; all extend `Model`
- `views/` — PHP templates organized by module; `layouts/main.php` is the master template

### View Rendering

`Controller::view($template, $datos)` extracts `$datos` into local variables and includes the template wrapped in `layouts/main.php`. The layout file uses `$titulo` and `$contenido`.

### AJAX / JSON Endpoints

Several controller actions return JSON (used by the POS and dashboard):
- `ProductoController::buscar()`, `getJson()`, `listaJson()`
- `VentaController::guardar()` — accepts a JSON POST payload to create a sale with line items in a transaction
- `VentaController::statsJson()` — chart data for the dashboard

### Database

- All queries use PDO prepared statements via the base `Model` methods.
- Sales creation uses transactions (`venta` + `ticket` rows together).
- Audit trail is maintained by MySQL triggers (defined in `sql/triggers.sql`).
- Reusable JOIN queries are abstracted into views (`sql/vistas.sql`).
- Complex reports use stored procedures (`sql/procedimientos.sql`).

### Frontend

- **Tailwind CSS** (CDN) for layout and utilities.
- **SweetAlert2** (CDN) for modals and toasts.
- `src/js/app.js` — sidebar toggle, delete confirmations, URL-param flash messages (`?msg=creado|actualizado|eliminado`), table filtering, currency formatting.
- `src/css/estilos.css` — animations, custom scrollbar, brand color (`#ed7425` orange).

## Language

All code, variable names, comments, and UI text are in **Spanish**.