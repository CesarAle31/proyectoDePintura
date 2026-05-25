<?php
/**
 * ============================================================
 *  Reportes — Panel de reportes y auditoría
 * ============================================================
 *  Gráficas de ventas, top productos, auditoría, filtros.
 * ============================================================
 */
?>

<div class="mb-6">
    <h2 class="text-2xl lg:text-3xl font-display font-bold text-gray-900">Reportes</h2>
    <p class="text-gray-500 mt-1">Análisis de ventas, inventario y auditoría</p>
</div>

<!-- ═══ KPIs ═══ -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Ventas</p>
        <p class="text-3xl font-bold text-gray-900 mt-1"><?= $totalVentas ?></p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Ingresos Totales</p>
        <p class="text-3xl font-bold text-emerald-600 mt-1">$<?= number_format($ingresos, 2) ?></p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Valor Inventario</p>
        <p class="text-3xl font-bold text-brand-600 mt-1">$<?= number_format($valorInventario, 2) ?></p>
    </div>
</div>

<!-- ═══ GRÁFICAS ═══ -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Ventas por mes -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Ventas por Mes</h3>
        <canvas id="chartVentasMes" height="220"></canvas>
    </div>
    <!-- Top productos -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Top 5 Productos</h3>
        <canvas id="chartTopProductos" height="220"></canvas>
    </div>
</div>

<!-- ═══ FILTRO POR FECHAS ═══ -->
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-8">
    <div class="p-5 border-b border-gray-100">
        <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Consultar Ventas por Rango</h3>
        <div class="flex flex-col sm:flex-row items-end gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">Desde</label>
                <input type="date" id="filtroDesde" value="<?= date('Y-m-01') ?>"
                       class="form-input px-4 py-2.5 rounded-xl border border-gray-200 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">Hasta</label>
                <input type="date" id="filtroHasta" value="<?= date('Y-m-d') ?>"
                       class="form-input px-4 py-2.5 rounded-xl border border-gray-200 text-sm">
            </div>
            <button onclick="filtrarVentas()"
                    class="btn-primary px-5 py-2.5 rounded-xl text-sm font-semibold inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                Consultar
            </button>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="table-modern w-full" id="tablaFiltro">
            <thead>
                <tr>
                    <th>Folio</th>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Empleado</th>
                    <th>Monto</th>
                </tr>
            </thead>
            <tbody id="filtroBody">
                <tr><td colspan="5" class="text-center text-gray-400 py-8 text-sm">Selecciona un rango y presiona Consultar</td></tr>
            </tbody>
        </table>
    </div>
    <div id="filtroResumen" class="hidden p-4 border-t border-gray-100 text-right">
        <span class="text-sm text-gray-500">Total del período:</span>
        <span id="filtroTotal" class="ml-2 text-lg font-bold text-brand-600"></span>
    </div>
</div>

<!-- ═══ EMPLEADOS CON MÁS VENTAS ═══ -->
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-8">
    <div class="p-5 border-b border-gray-100">
        <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Rendimiento de Empleados</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="table-modern w-full">
            <thead>
                <tr>
                    <th>Empleado</th>
                    <th>Ventas Realizadas</th>
                    <th>Ingresos Generados</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($empleadosVentas)): ?>
                <tr><td colspan="3" class="text-center text-gray-400 py-6 text-sm">Sin datos</td></tr>
                <?php else: ?>
                    <?php foreach ($empleadosVentas as $ev): ?>
                    <tr class="table-row">
                        <td class="font-medium text-gray-800 text-sm"><?= htmlspecialchars($ev['empleado']) ?></td>
                        <td class="text-sm"><?= $ev['totalVentas'] ?></td>
                        <td class="text-sm font-bold text-gray-900">$<?= number_format($ev['ingresos'], 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ═══ STOCK BAJO ═══ -->
<?php if (!empty($stockBajo)): ?>
<div class="bg-white rounded-2xl border border-red-100 shadow-sm mb-8">
    <div class="p-5 border-b border-red-100">
        <h3 class="text-xs font-semibold text-red-400 uppercase tracking-wider">Productos con Stock Bajo (&le; 5)</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="table-modern w-full">
            <thead>
                <tr><th>Producto</th><th>Color</th><th>Stock</th><th>Costo</th></tr>
            </thead>
            <tbody>
                <?php foreach ($stockBajo as $sb): ?>
                <tr class="table-row">
                    <td class="font-medium text-gray-800 text-sm"><?= htmlspecialchars($sb['nombre']) ?></td>
                    <td class="text-sm"><?= htmlspecialchars($sb['color']) ?></td>
                    <td><span class="badge-danger text-xs font-bold px-2 py-1 rounded-full"><?= $sb['stock'] ?></span></td>
                    <td class="text-sm">$<?= number_format($sb['costo'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- ═══ AUDITORÍA ═══ -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Auditoría de Stock -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
        <div class="p-5 border-b border-gray-100">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Auditoría de Stock</h3>
        </div>
        <div class="overflow-x-auto max-h-64 overflow-y-auto">
            <table class="table-modern w-full text-xs">
                <thead class="sticky top-0 bg-white"><tr><th>Pintura</th><th>Anterior</th><th>Nuevo</th><th>Fecha</th></tr></thead>
                <tbody>
                    <?php if (empty($auditoriaStock)): ?>
                    <tr><td colspan="4" class="text-center text-gray-400 py-4">Sin registros</td></tr>
                    <?php else: ?>
                        <?php foreach ($auditoriaStock as $a): ?>
                        <tr class="table-row">
                            <td><?= $a['idPintura'] ?? $a['id_pintura'] ?? '-' ?></td>
                            <td><?= $a['stock_anterior'] ?? '-' ?></td>
                            <td><?= $a['stock_nuevo'] ?? '-' ?></td>
                            <td class="text-gray-400"><?= $a['fecha_cambio'] ?? '-' ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Auditoría de Costos -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
        <div class="p-5 border-b border-gray-100">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Auditoría de Costos</h3>
        </div>
        <div class="overflow-x-auto max-h-64 overflow-y-auto">
            <table class="table-modern w-full text-xs">
                <thead class="sticky top-0 bg-white"><tr><th>Pintura</th><th>Anterior</th><th>Nuevo</th><th>Fecha</th></tr></thead>
                <tbody>
                    <?php if (empty($auditoriaCosto)): ?>
                    <tr><td colspan="4" class="text-center text-gray-400 py-4">Sin registros</td></tr>
                    <?php else: ?>
                        <?php foreach ($auditoriaCosto as $a): ?>
                        <tr class="table-row">
                            <td><?= $a['idPintura'] ?? $a['id_pintura'] ?? '-' ?></td>
                            <td>$<?= number_format($a['costo_anterior'] ?? 0, 2) ?></td>
                            <td>$<?= number_format($a['costo_nuevo'] ?? 0, 2) ?></td>
                            <td class="text-gray-400"><?= $a['fecha_cambio'] ?? '-' ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Auditoría de Clientes -->
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-8">
    <div class="p-5 border-b border-gray-100">
        <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Auditoría de Clientes (Inserciones)</h3>
    </div>
    <div class="overflow-x-auto max-h-64 overflow-y-auto">
        <table class="table-modern w-full text-xs">
            <thead class="sticky top-0 bg-white"><tr><th>Cliente</th><th>Correo</th><th>Fecha Registro</th></tr></thead>
            <tbody>
                <?php if (empty($auditoriaClientes)): ?>
                <tr><td colspan="3" class="text-center text-gray-400 py-4">Sin registros</td></tr>
                <?php else: ?>
                    <?php foreach ($auditoriaClientes as $a): ?>
                    <tr class="table-row">
                        <td><?= htmlspecialchars(($a['nombre'] ?? '') . ' ' . ($a['apellidoP'] ?? '')) ?></td>
                        <td class="text-gray-500"><?= htmlspecialchars($a['correo'] ?? '-') ?></td>
                        <td class="text-gray-400"><?= $a['fecha_registro'] ?? '-' ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ═══ SCRIPTS: Gráficas y filtro AJAX ═══ -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    // ── Gráfica: Ventas por Mes ──
    const ventasMes = <?= json_encode($ventasPorMes) ?>;
    new Chart(document.getElementById('chartVentasMes'), {
        type: 'bar',
        data: {
            labels: ventasMes.map(v => v.mes),
            datasets: [{
                label: 'Ingresos ($)',
                data: ventasMes.map(v => v.ingresos),
                backgroundColor: 'rgba(237, 116, 37, 0.15)',
                borderColor: '#ed7425',
                borderWidth: 2,
                borderRadius: 8,
            }, {
                label: 'Num. Ventas',
                data: ventasMes.map(v => v.totalVentas),
                backgroundColor: 'rgba(99, 102, 241, 0.15)',
                borderColor: '#6366f1',
                borderWidth: 2,
                borderRadius: 8,
                yAxisID: 'y1',
            }]
        },
        options: {
            responsive: true,
            interaction: { intersect: false, mode: 'index' },
            plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, padding: 16 } } },
            scales: {
                y:  { beginAtZero: true, ticks: { callback: v => '$' + v.toLocaleString() } },
                y1: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false } },
            }
        }
    });

    // ── Gráfica: Top Productos ──
    const topProd = <?= json_encode($topProductos) ?>;
    const colores = ['#ed7425', '#6366f1', '#10b981', '#f59e0b', '#ef4444'];
    new Chart(document.getElementById('chartTopProductos'), {
        type: 'doughnut',
        data: {
            labels: topProd.map(p => p.nombre + ' (' + p.color + ')'),
            datasets: [{
                data: topProd.map(p => p.totalVendido),
                backgroundColor: colores.map(c => c + '22'),
                borderColor: colores,
                borderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            cutout: '60%',
            plugins: {
                legend: { position: 'bottom', labels: { usePointStyle: true, padding: 12, font: { size: 11 } } },
            }
        }
    });
});

// ── Filtro por fechas (AJAX) ──
async function filtrarVentas() {
    const desde = document.getElementById('filtroDesde').value;
    const hasta = document.getElementById('filtroHasta').value;

    if (!desde || !hasta) { showToast('Selecciona ambas fechas', 'warning'); return; }

    try {
        const res = await fetch(`index.php?page=reportes&action=filtrar&desde=${desde}&hasta=${hasta}`);
        const json = await res.json();
        const body = document.getElementById('filtroBody');
        const resumen = document.getElementById('filtroResumen');

        if (!json.data || json.data.length === 0) {
            body.innerHTML = '<tr><td colspan="5" class="text-center text-gray-400 py-8 text-sm">No hay ventas en ese período</td></tr>';
            resumen.classList.add('hidden');
            return;
        }

        let total = 0;
        body.innerHTML = json.data.map(v => {
            total += parseFloat(v.montoTotal);
            return `<tr class="table-row">
                <td class="font-mono text-sm font-semibold text-brand-600">#${v.folio}</td>
                <td class="text-sm">${new Date(v.fecha).toLocaleDateString('es-MX')}</td>
                <td class="text-sm font-medium">${v.cliente}</td>
                <td class="text-sm text-gray-500">${v.empleado}</td>
                <td class="text-sm font-bold">$${parseFloat(v.montoTotal).toLocaleString('es-MX', {minimumFractionDigits: 2})}</td>
            </tr>`;
        }).join('');

        document.getElementById('filtroTotal').textContent = '$' + total.toLocaleString('es-MX', {minimumFractionDigits: 2});
        resumen.classList.remove('hidden');

    } catch (err) {
        showToast('Error al consultar: ' + err.message, 'error');
    }
}
</script>
