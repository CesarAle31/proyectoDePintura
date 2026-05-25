<?php
/**
 * Sidebar de navegación lateral.
 * Resalta la página activa usando la variable $paginaActual
 * y filtra las opciones del menú según el rol del usuario.
 */
$paginaActual = $paginaActual ?? 'dashboard';

$menu = [
    ['page' => 'dashboard',    'label' => 'Dashboard',    'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
    ['page' => 'productos',    'label' => 'Productos',    'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
    ['page' => 'clientes',     'label' => 'Clientes',     'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
    ['page' => 'proveedores',  'label' => 'Proveedores',  'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
    ['page' => 'empleados',    'label' => 'Empleados',    'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
    ['page' => 'ventas',       'label' => 'Ventas',       'icon' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z'],
    ['page' => 'reportes',     'label' => 'Reportes',     'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
];

// Filtrar el menú según los permisos del rol actual
$menu = array_values(array_filter($menu, fn($item) => Auth::puedeVer($item['page'])));

$usuarioActual = Auth::usuario();
$rolActual     = $usuarioActual['rol']            ?? '';
$nombreActual  = $usuarioActual['nombreCompleto'] ?? '';
$iniciales     = '';
foreach (preg_split('/\s+/', trim($nombreActual)) as $parte) {
    if ($parte !== '' && strlen($iniciales) < 2) {
        $iniciales .= mb_strtoupper(mb_substr($parte, 0, 1));
    }
}
$iniciales = $iniciales ?: 'U';
?>

<!-- Overlay para móvil -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-30 hidden lg:hidden" onclick="toggleSidebar()"></div>

<aside id="sidebar" class="fixed left-0 top-0 h-full w-64 bg-slate_dark-900 text-white z-40 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 flex flex-col">

    <!-- Logo -->
    <div class="p-5 border-b border-white/10">
        <a href="index.php" class="flex items-center gap-3 group">
            <div class="w-12 h-12 rounded-xl bg-white flex items-center justify-center shadow-lg group-hover:shadow-brand-500/40 transition-shadow overflow-hidden shrink-0">
                <img src="src/assets/img/Captura de pantalla 2026-05-24 232344 (1).png" alt="IPESA Pinturas" class="w-full h-full object-contain">
            </div>
            <div>
                <h1 class="font-display text-base font-bold tracking-tight">IPESA Pinturas</h1>
                <p class="text-[10px] uppercase tracking-[0.2em] text-gray-400 font-medium">Punto de Venta</p>
            </div>
        </a>
    </div>

    <!-- Navegación -->
    <nav class="flex-1 py-4 px-3 space-y-1 overflow-y-auto sidebar-scroll">
        <p class="px-3 pt-2 pb-3 text-[10px] uppercase tracking-[0.2em] text-gray-500 font-semibold">Menú Principal</p>

        <?php foreach ($menu as $item): ?>
            <?php $activo = ($paginaActual === $item['page']); ?>
            <a href="index.php?page=<?= $item['page'] ?>"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                      <?= $activo
                          ? 'bg-brand-500/15 text-brand-400 shadow-sm'
                          : 'text-gray-400 hover:text-white hover:bg-white/5' ?>">
                <svg class="w-5 h-5 shrink-0 <?= $activo ? 'text-brand-400' : '' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="<?= $item['icon'] ?>"/>
                </svg>
                <?= $item['label'] ?>
                <?php if ($activo): ?>
                    <span class="ml-auto w-1.5 h-1.5 rounded-full bg-brand-400"></span>
                <?php endif; ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <!-- Footer sidebar: usuario + logout -->
    <div class="p-4 border-t border-white/10 space-y-3">
        <div class="flex items-center gap-3 px-2">
            <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center text-xs font-bold shrink-0">
                <?= htmlspecialchars($iniciales) ?>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-white truncate"><?= htmlspecialchars($nombreActual ?: 'Usuario') ?></p>
                <p class="text-[10px] text-brand-400 uppercase tracking-wider font-medium truncate"><?= htmlspecialchars($rolActual) ?></p>
            </div>
        </div>

        <a href="index.php?page=login&action=logout"
           class="flex items-center justify-center gap-2 w-full px-3 py-2 rounded-lg text-xs font-semibold text-gray-300 bg-white/5 hover:bg-red-500/20 hover:text-red-300 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
            Cerrar sesión
        </a>
    </div>
</aside>
