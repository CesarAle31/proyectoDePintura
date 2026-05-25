<?php
/**
 * ============================================================
 *  Clientes — Vista de listado
 * ============================================================
 */
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

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
    <div class="p-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center gap-3">
        <div class="relative flex-1">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" id="buscarCliente" placeholder="Buscar por nombre, correo, teléfono..."
                   class="form-input w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:ring-0">
        </div>
        <span class="text-xs text-gray-400"><?= count($clientes) ?> registros</span>
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
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($clientes)): ?>
                <tr class="empty-row">
                    <td colspan="6" class="empty-state">
                        <p class="font-medium">No hay clientes registrados</p>
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

<script>
document.addEventListener('DOMContentLoaded', () => filtrarTabla('buscarCliente', 'tablaClientes'));
</script>
