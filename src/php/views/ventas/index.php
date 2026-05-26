<?php
/**
 * ============================================================
 *  Ventas — Vista de listado con filtros avanzados
 * ============================================================
 */

$f = $filtros ?? [];
$hayFiltros = !empty($f['buscar']) || !empty($f['desde']) || !empty($f['hasta'])
           || !empty($f['cliente']) || !empty($f['empleado']);
?>

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h2 class="text-2xl lg:text-3xl font-display font-bold text-gray-900">Ventas</h2>
        <p class="text-gray-500 mt-1">Historial de ventas realizadas</p>
    </div>
    <a href="index.php?page=ventas&action=crear"
       class="btn-primary inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
        </svg>
        Nueva Venta
    </a>
</div>

<!-- Panel de filtros -->
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-4">
    <div class="px-5 py-3 border-b border-gray-100 flex items-center gap-2">
        <svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
        </svg>
        <span class="text-sm font-semibold text-gray-700">Filtros de búsqueda</span>
        <?php if ($hayFiltros): ?>
            <span class="ml-auto inline-flex items-center gap-1 text-xs font-medium text-brand-600 bg-brand-50 px-2 py-0.5 rounded-full">
                Filtros activos
            </span>
        <?php endif; ?>
    </div>

    <form method="GET" action="index.php" class="p-5">
        <input type="hidden" name="page" value="ventas">

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-3">

            <!-- Búsqueda general -->
            <div class="xl:col-span-2">
                <label class="block text-xs font-medium text-gray-500 mb-1">Buscar</label>
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="buscar" value="<?= htmlspecialchars($f['buscar'] ?? '') ?>"
                           placeholder="Folio, cliente, empleado..."
                           class="form-input w-full pl-9 pr-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:ring-0">
                </div>
            </div>

            <!-- Fecha inicio -->
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Desde</label>
                <input type="date" name="desde" value="<?= htmlspecialchars($f['desde'] ?? '') ?>"
                       class="form-input w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:ring-0">
            </div>

            <!-- Fecha fin -->
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Hasta</label>
                <input type="date" name="hasta" value="<?= htmlspecialchars($f['hasta'] ?? '') ?>"
                       class="form-input w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:ring-0">
            </div>

            <!-- Cliente -->
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Cliente</label>
                <select name="cliente" class="form-input w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:ring-0 bg-white">
                    <option value="">Todos los clientes</option>
                    <?php foreach ($clientes as $c): ?>
                        <option value="<?= $c['idCliente'] ?>" <?= ($f['cliente'] ?? 0) == $c['idCliente'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['nombreCompleto']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Empleado -->
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Empleado</label>
                <select name="empleado" class="form-input w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:ring-0 bg-white">
                    <option value="">Todos los empleados</option>
                    <?php foreach ($empleados as $e): ?>
                        <option value="<?= $e['idEmpleado'] ?>" <?= ($f['empleado'] ?? 0) == $e['idEmpleado'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($e['nombreCompleto']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

        </div>

        <!-- Botones -->
        <div class="flex items-center gap-3 mt-4">
            <button type="submit" class="btn-primary inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Filtrar
            </button>
            <a href="index.php?page=ventas" class="btn-secondary inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Limpiar
            </a>
        </div>
    </form>
</div>

<!-- Tabla de resultados -->
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
    <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
        <span class="text-sm font-medium text-gray-600">
            <?= count($ventas) ?> <?= count($ventas) === 1 ? 'resultado' : 'resultados' ?>
            <?php if ($hayFiltros): ?>
                <span class="text-gray-400 font-normal">encontrados con los filtros aplicados</span>
            <?php endif; ?>
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="table-modern w-full" id="tablaVentas">
            <thead>
                <tr>
                    <th>Folio</th>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Empleado</th>
                    <th>Monto Total</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($ventas)): ?>
                <tr class="empty-row">
                    <td colspan="6" class="empty-state">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <p class="font-medium">No se encontraron ventas</p>
                        <?php if ($hayFiltros): ?>
                            <p class="text-sm mt-1 text-gray-400">Intenta ajustar los filtros de búsqueda</p>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($ventas as $v): ?>
                    <tr class="table-row">
                        <td>
                            <span class="inline-flex items-center gap-1.5 font-mono text-sm font-semibold text-brand-600">
                                #<?= $v['folio'] ?>
                            </span>
                        </td>
                        <td class="text-sm"><?= date('d/m/Y', strtotime($v['fecha'])) ?></td>
                        <td class="text-sm font-medium text-gray-800"><?= htmlspecialchars($v['cliente']) ?></td>
                        <td class="text-sm text-gray-500"><?= htmlspecialchars($v['empleado']) ?></td>
                        <td class="text-sm font-bold text-gray-900">$<?= number_format($v['montoTotal'], 2) ?></td>
                        <td>
                            <div class="flex items-center justify-center gap-1">
                                <a href="index.php?page=ventas&action=ticket&folio=<?= $v['folio'] ?>"
                                   class="p-2 rounded-lg hover:bg-emerald-50 text-emerald-500 transition-colors" data-tooltip="Ver Ticket">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </a>
                                <button onclick="confirmarEliminar('index.php?page=ventas&action=eliminar&id=<?= $v['folio'] ?>', 'Venta #<?= $v['folio'] ?>')"
                                        class="p-2 rounded-lg hover:bg-red-50 text-red-400 transition-colors" data-tooltip="Eliminar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
