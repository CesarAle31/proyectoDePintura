<?php
/**
 * ============================================================
 *  Clientes — Vista de listado con filtros avanzados
 * ============================================================
 */

$f = $filtros ?? [];
$hayFiltros = !empty($f['buscar']) || !empty($f['telefono']) || $f['activo'] !== '';
?>

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h2 class="text-2xl lg:text-3xl font-display font-bold text-gray-900">Clientes</h2>
        <p class="text-gray-500 mt-1">Directorio de clientes registrados</p>
    </div>
    <a href="index.php?page=clientes&action=crear"
       class="btn-primary inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
        </svg>
        Nuevo Cliente
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
        <input type="hidden" name="page" value="clientes">

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">

            <!-- Búsqueda general -->
            <div class="lg:col-span-2">
                <label class="block text-xs font-medium text-gray-500 mb-1">Buscar cliente</label>
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="buscar" value="<?= htmlspecialchars($f['buscar'] ?? '') ?>"
                           placeholder="Nombre o correo electrónico..."
                           class="form-input w-full pl-9 pr-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:ring-0">
                </div>
            </div>

            <!-- Teléfono -->
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Teléfono</label>
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    <input type="text" name="telefono" value="<?= htmlspecialchars($f['telefono'] ?? '') ?>"
                           placeholder="Ej. 9511234567"
                           class="form-input w-full pl-9 pr-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:ring-0">
                </div>
            </div>

            <!-- Estado -->
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Estado</label>
                <select name="activo" class="form-input w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:ring-0 bg-white">
                    <option value="" <?= ($f['activo'] ?? '') === '' ? 'selected' : '' ?>>Solo activos</option>
                    <option value="1"  <?= ($f['activo'] ?? '') === '1'  ? 'selected' : '' ?>>Activos</option>
                    <option value="0"  <?= ($f['activo'] ?? '') === '0'  ? 'selected' : '' ?>>Inactivos (baja)</option>
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
            <a href="index.php?page=clientes" class="btn-secondary inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold">
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
            <?= count($clientes) ?> <?= count($clientes) === 1 ? 'cliente' : 'clientes' ?>
            <?php if ($hayFiltros): ?>
                <span class="text-gray-400 font-normal">encontrados con los filtros aplicados</span>
            <?php endif; ?>
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="table-modern w-full" id="tablaClientes">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre Completo</th>
                    <th>Correo</th>
                    <th>Teléfono</th>
                    <th>Dirección</th>
                    <th>Estado</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($clientes)): ?>
                <tr class="empty-row">
                    <td colspan="7" class="empty-state">
                        <p class="font-medium">No se encontraron clientes</p>
                        <?php if ($hayFiltros): ?>
                            <p class="text-sm mt-1 text-gray-400">Intenta ajustar los filtros de búsqueda</p>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($clientes as $c): ?>
                    <tr class="table-row">
                        <td class="font-mono text-xs text-gray-400"><?= $c['idCliente'] ?></td>
                        <td class="font-medium text-gray-800">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-600 text-xs font-bold">
                                    <?= strtoupper(substr($c['nombre'], 0, 1) . substr($c['apellidoP'], 0, 1)) ?>
                                </div>
                                <?= htmlspecialchars($c['nombreCompleto']) ?>
                            </div>
                        </td>
                        <td class="text-sm"><?= htmlspecialchars($c['correo'] ?? '—') ?></td>
                        <td class="text-sm"><?= htmlspecialchars($c['telefonoCliente'] ?? $c['telefono'] ?? '—') ?></td>
                        <td class="text-xs text-gray-500">
                            <?php if ($c['calle']): ?>
                                <?= htmlspecialchars($c['calle']) ?> #<?= htmlspecialchars($c['numero']) ?>,
                                <?= htmlspecialchars($c['colonia'] ?? '') ?>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (isset($c['activo'])): ?>
                                <span class="badge <?= $c['activo'] ? 'badge-success' : 'badge-danger' ?>">
                                    <?= $c['activo'] ? 'Activo' : 'Inactivo' ?>
                                </span>
                            <?php else: ?>
                                <span class="badge badge-success">Activo</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="flex items-center justify-center gap-1">
                                <a href="index.php?page=clientes&action=editar&id=<?= $c['idCliente'] ?>"
                                   class="p-2 rounded-lg hover:bg-blue-50 text-blue-500 transition-colors" data-tooltip="Editar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <button onclick="confirmarEliminar('index.php?page=clientes&action=eliminar&id=<?= $c['idCliente'] ?>', '<?= htmlspecialchars($c['nombreCompleto']) ?>')"
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
