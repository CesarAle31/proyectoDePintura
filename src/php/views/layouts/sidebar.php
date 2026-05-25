<?php
/**
 * Sidebar de navegación lateral.
 * Resalta la página activa usando la variable $paginaActual.
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
?>

<!-- Overlay para móvil -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-30 hidden lg:hidden" onclick="toggleSidebar()"></div>

<aside id="sidebar" class="fixed left-0 top-0 h-full w-64 bg-slate_dark-900 text-white z-40 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 flex flex-col">

    <!-- Logo -->
    <div class="p-6 border-b border-white/10">
        <a href="index.php" class="flex items-center gap-3 group">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center shadow-lg shadow-brand-500/25 group-hover:shadow-brand-500/40 transition-shadow">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                </svg>
            </div>
            <div>
                <h1 class="font-display text-lg font-bold tracking-tight">ColorMax</h1>
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

    <!-- Footer sidebar -->
    <div class="p-4 border-t border-white/10">
        <div class="flex items-center gap-3 px-2">
            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-brand-400 to-orange-600 flex items-center justify-center text-xs font-bold">
                CM
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-medium text-gray-300 truncate">ColorMax Pinturas</p>
                <p class="text-[10px] text-gray-500">Oaxaca, MX</p>
            </div>
        </div>
    </div>
</aside>
