<?php
/**
 * ============================================================
 *  Dashboard — Vista principal con estadísticas
 * ============================================================
 */
?>

<!-- Título de la página -->
<div class="mb-8">
    <h2 class="text-2xl lg:text-3xl font-display font-bold text-gray-900">Dashboard</h2>
    <p class="text-gray-500 mt-1">Resumen general del sistema de pinturas</p>
</div>

<!-- ══════ TARJETAS DE RESUMEN ══════ -->
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 lg:gap-6 mb-8">

    <!-- Productos -->
    <div class="stat-card bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Productos</p>
                <p class="text-3xl font-bold text-gray-900 mt-2"><?= $totalProductos ?></p>
                <p class="text-xs text-gray-400 mt-1">pinturas registradas</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Clientes -->
    <div class="stat-card bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Clientes</p>
                <p class="text-3xl font-bold text-gray-900 mt-2"><?= $totalClientes ?></p>
                <p class="text-xs text-gray-400 mt-1">clientes activos</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center">
                <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Ventas Totales -->
    <div class="stat-card bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Ventas</p>
                <p class="text-3xl font-bold text-gray-900 mt-2"><?= $totalVentas ?></p>
                <p class="text-xs text-gray-400 mt-1">operaciones realizadas</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-brand-50 flex items-center justify-center">
                <svg class="w-6 h-6 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Ingresos -->
    <div class="stat-card bg-gradient-to-br from-brand-500 to-brand-700 rounded-2xl p-5 shadow-lg shadow-brand-500/20">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-white/70">Ingresos Totales</p>
                <p class="text-3xl font-bold text-white mt-2">$<?= number_format($ingresos, 2) ?></p>
                <p class="text-xs text-white/60 mt-1">acumulado general</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- ══════ GRÁFICAS ══════ -->
<div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-8">

    <!-- Ventas por mes -->
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
        <h3 class="text-sm font-semibold text-gray-700 mb-4 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-brand-500"></span>
            Ventas por Mes
        </h3>
        <div class="h-64">
            <canvas id="chartVentasMes"></canvas>
        </div>
    </div>

    <!-- Top productos -->
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
        <h3 class="text-sm font-semibold text-gray-700 mb-4 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
            Top 5 Productos Más Vendidos
        </h3>
        <div class="h-64">
            <canvas id="chartTopProductos"></canvas>
        </div>
    </div>
</div>

<!-- ══════ ALERTAS DE STOCK BAJO + VENTAS RECIENTES ══════ -->
<div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

    <!-- Stock bajo -->
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
        <h3 class="text-sm font-semibold text-gray-700 mb-4 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
            Alertas de Stock Bajo
        </h3>
        <?php if (empty($stockBajo)): ?>
            <p class="text-gray-400 text-sm py-8 text-center">Todo el inventario tiene stock suficiente ✓</p>
        <?php else: ?>
            <div class="space-y-3 max-h-72 overflow-y-auto">
                <?php foreach ($stockBajo as $p): ?>
                <div class="flex items-center justify-between p-3 rounded-xl bg-red-50/50 border border-red-100">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center">
                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800"><?= htmlspecialchars($p['nombre']) ?></p>
                            <p class="text-xs text-gray-400"><?= htmlspecialchars($p['color']) ?> — <?= htmlspecialchars($p['presentacion'] ?? '') ?></p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 text-xs font-bold rounded-lg <?= $p['stock'] <= 5 ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700' ?>">
                        <?= $p['stock'] ?> uds
                    </span>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Últimas ventas -->
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
        <h3 class="text-sm font-semibold text-gray-700 mb-4 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            Ventas Recientes
        </h3>
        <?php if (empty($ventasRecientes)): ?>
            <p class="text-gray-400 text-sm py-8 text-center">No hay ventas registradas aún</p>
        <?php else: ?>
            <div class="space-y-3">
                <?php foreach ($ventasRecientes as $v): ?>
                <a href="index.php?page=ventas&action=ticket&folio=<?= $v['folio'] ?>"
                   class="flex items-center justify-between p-3 rounded-xl hover:bg-gray-50 transition-colors group">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center">
                            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800 group-hover:text-brand-600">#<?= $v['folio'] ?></p>
                            <p class="text-xs text-gray-400"><?= htmlspecialchars($v['cliente']) ?> — <?= $v['fecha'] ?></p>
                        </div>
                    </div>
                    <span class="text-sm font-semibold text-gray-700">$<?= number_format($v['montoTotal'], 2) ?></span>
                </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- ══════ CHART.JS ══════ -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const brandColor = '#e51e25';
    const brandLight = '#fff1f2';

    // ── Ventas por Mes ──
    const ventasMes = <?= json_encode($ventasPorMes) ?>;
    if (ventasMes.length > 0) {
        new Chart(document.getElementById('chartVentasMes'), {
            type: 'bar',
            data: {
                labels: ventasMes.map(v => v.mes),
                datasets: [{
                    label: 'Ingresos ($)',
                    data: ventasMes.map(v => v.ingresos),
                    backgroundColor: brandColor + '33',
                    borderColor: brandColor,
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { callback: v => '$' + v.toLocaleString() } },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    // ── Top Productos ──
    const top = <?= json_encode($topProductos) ?>;
    if (top.length > 0) {
        const colores = ['#e51e25', '#3b82f6', '#10b981', '#f59e0b', '#8b5cf6'];
        new Chart(document.getElementById('chartTopProductos'), {
            type: 'doughnut',
            data: {
                labels: top.map(t => t.nombre),
                datasets: [{
                    data: top.map(t => t.totalVendido),
                    backgroundColor: colores,
                    borderWidth: 3,
                    borderColor: '#fff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { padding: 15, usePointStyle: true, pointStyle: 'circle' } }
                },
                cutout: '65%',
            }
        });
    }
});
</script>
