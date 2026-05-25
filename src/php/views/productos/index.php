<?php
/**
 * ============================================================
 *  Productos / Pinturas — Vista de listado (index)
 * ============================================================
 */
?>

<!-- Encabezado -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h2 class="text-2xl lg:text-3xl font-display font-bold text-gray-900">Productos</h2>
        <p class="text-gray-500 mt-1">Catálogo de pinturas registradas</p>
    </div>
    <a href="index.php?page=productos&action=crear"
       class="btn-primary inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Nueva Pintura
    </a>
</div>

<!-- Buscador -->
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-6">
    <div class="p-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center gap-3">
        <div class="relative flex-1">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" id="buscarProducto" placeholder="Buscar por nombre, color, línea..."
                   class="form-input w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:ring-0">
        </div>
        <span class="text-xs text-gray-400"><?= count($productos) ?> registros</span>
    </div>

    <!-- Tabla -->
    <div class="overflow-x-auto">
        <table class="table-modern w-full" id="tablaProductos">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Color</th>
                    <th>Línea</th>
                    <th>Capacidad</th>
                    <th>Presentación</th>
                    <th>Costo</th>
                    <th>Stock</th>
                    <th>Proveedor</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($productos)): ?>
                <tr class="empty-row">
                    <td colspan="10" class="empty-state">
                        <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        <p class="font-medium">No hay productos registrados</p>
                        <p class="text-sm mt-1">Agrega tu primera pintura con el botón superior</p>
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($productos as $p): ?>
                    <tr class="table-row">
                        <td class="font-mono text-xs text-gray-400"><?= $p['idPintura'] ?></td>
                        <td class="font-medium text-gray-800"><?= htmlspecialchars($p['nombre']) ?></td>
                        <td>
                            <span class="inline-flex items-center gap-1.5">
                                <span class="w-3 h-3 rounded-full border border-gray-200" style="background:<?= strtolower($p['color']) ?>"></span>
                                <?= htmlspecialchars($p['color']) ?>
                            </span>
                        </td>
                        <td><span class="badge badge-brand"><?= htmlspecialchars($p['linea']) ?></span></td>
                        <td><?= htmlspecialchars($p['capacidad']) ?></td>
                        <td><?= htmlspecialchars($p['presentacion']) ?></td>
                        <td class="font-semibold">$<?= number_format($p['costo'], 2) ?></td>
                        <td>
                            <?php
                                $stockClass = $p['stock'] <= 5 ? 'badge-danger' : ($p['stock'] <= 15 ? 'badge-warning' : 'badge-success');
                            ?>
                            <span class="badge <?= $stockClass ?>"><?= $p['stock'] ?> uds</span>
                        </td>
                        <td class="text-xs text-gray-500"><?= htmlspecialchars($p['proveedor']) ?></td>
                        <td>
                            <div class="flex items-center justify-center gap-1">
                                <a href="index.php?page=productos&action=editar&id=<?= $p['idPintura'] ?>"
                                   class="p-2 rounded-lg hover:bg-blue-50 text-blue-500 transition-colors"
                                   data-tooltip="Editar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <button onclick="confirmarEliminar('index.php?page=productos&action=eliminar&id=<?= $p['idPintura'] ?>', '<?= htmlspecialchars($p['nombre']) ?>')"
                                        class="p-2 rounded-lg hover:bg-red-50 text-red-400 transition-colors"
                                        data-tooltip="Eliminar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
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
document.addEventListener('DOMContentLoaded', () => filtrarTabla('buscarProducto', 'tablaProductos'));
</script>
