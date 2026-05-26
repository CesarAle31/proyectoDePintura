<?php
/**
 * ============================================================
 *  Empleados — Vista de listado con filtros avanzados
 * ============================================================
 */

$f = $filtros ?? [];
$hayFiltros = !empty($f['buscar']) || !empty($f['rol']) || $f['activo'] === '0';
?>

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h2 class="text-2xl lg:text-3xl font-display font-bold text-gray-900">Empleados</h2>
        <p class="text-gray-500 mt-1">Personal de la empresa</p>
    </div>
    <a href="index.php?page=empleados&action=crear"
       class="btn-primary inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
        </svg>
        Nuevo Empleado
    </a>
</div>

<?php if (!empty($noEncontradoPorId)): ?>
<!-- Alerta: ID buscado no existe -->
<div class="mb-4 flex items-start gap-3 bg-amber-50 border border-amber-200 text-amber-800 rounded-2xl px-5 py-4 max-w-full animate-fadeInUp">
    <svg class="w-5 h-5 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
    </svg>
    <div>
        <p class="font-semibold">Empleado no encontrado</p>
        <p class="text-sm mt-0.5">
            No existe un empleado activo con el ID
            <strong>#<?= htmlspecialchars($f['buscar']) ?></strong>.
            Verifica el ID e intenta nuevamente.
        </p>
    </div>
</div>
<?php endif; ?>

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
        <input type="hidden" name="page" value="empleados">

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">

            <!-- Búsqueda: acepta ID numérico o texto -->
            <div class="lg:col-span-2">
                <label class="block text-xs font-medium text-gray-500 mb-1">
                    Buscar
                    <span class="text-gray-400 font-normal">(ID, nombre, apellido, teléfono, usuario o rol)</span>
                </label>
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="buscar" value="<?= htmlspecialchars($f['buscar'] ?? '') ?>"
                           placeholder="ID numérico o nombre..."
                           class="form-input w-full pl-9 pr-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:ring-0">
                </div>
                <p class="text-xs text-gray-400 mt-1">
                    Si escribes un número se busca por ID exacto.
                </p>
            </div>

            <!-- Rol -->
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Rol</label>
                <select name="rol" class="form-input w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:ring-0 bg-white">
                    <option value="">Todos los roles</option>
                    <?php foreach ($roles as $r): ?>
                        <option value="<?= $r['idRol'] ?>" <?= ($f['rol'] ?? 0) == $r['idRol'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($r['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Estado del empleado -->
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Estado</label>
                <select name="activo" class="form-input w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:ring-0 bg-white">
                    <option value=""  <?= ($f['activo'] ?? '') === ''  ? 'selected' : '' ?>>Empleados activos</option>
                    <option value="0" <?= ($f['activo'] ?? '') === '0' ? 'selected' : '' ?>>Dados de baja</option>
                </select>
            </div>

        </div>

        <!-- Botones -->
        <div class="flex items-center gap-3 mt-4">
            <button type="submit"
                    class="btn-primary inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Filtrar
            </button>
            <a href="index.php?page=empleados"
               class="btn-secondary inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold">
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
            <?= count($empleados) ?> <?= count($empleados) === 1 ? 'empleado' : 'empleados' ?>
            <?php if ($hayFiltros && !empty($empleados)): ?>
                <span class="text-gray-400 font-normal">encontrados</span>
            <?php endif; ?>
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="table-modern w-full" id="tablaEmpleados">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre Completo</th>
                    <th>Teléfono</th>
                    <th>Usuario</th>
                    <th>Rol</th>
                    <th>Acceso</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($empleados)): ?>
                <tr class="empty-row">
                    <td colspan="7" class="empty-state">
                        <?php if ($hayFiltros): ?>
                            <svg class="w-10 h-10 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <p class="font-medium">Sin resultados</p>
                            <p class="text-sm mt-1 text-gray-400">Intenta ajustar los filtros de búsqueda</p>
                        <?php else: ?>
                            <p class="font-medium">No hay empleados registrados</p>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($empleados as $e): ?>
                    <tr class="table-row">
                        <!-- ID -->
                        <td class="font-mono text-xs font-semibold text-brand-600">
                            #<?= $e['idEmpleado'] ?>
                        </td>

                        <!-- Nombre con avatar -->
                        <td class="font-medium text-gray-800">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-purple-50 flex items-center justify-center text-purple-600 text-xs font-bold shrink-0">
                                    <?= strtoupper(substr($e['nombre'], 0, 1) . substr($e['apellidoP'], 0, 1)) ?>
                                </div>
                                <?= htmlspecialchars($e['nombreCompleto']) ?>
                            </div>
                        </td>

                        <!-- Teléfono -->
                        <td class="text-sm"><?= htmlspecialchars($e['telefonoEmpleado'] ?? '—') ?></td>

                        <!-- Usuario -->
                        <td class="text-sm">
                            <?php if (!empty($e['usuario'])): ?>
                                <span class="font-mono text-gray-700"><?= htmlspecialchars($e['usuario']) ?></span>
                            <?php else: ?>
                                <span class="text-gray-400 text-xs">Sin usuario</span>
                            <?php endif; ?>
                        </td>

                        <!-- Rol -->
                        <td>
                            <?php if (!empty($e['rol'])): ?>
                                <span class="badge badge-info"><?= htmlspecialchars($e['rol']) ?></span>
                            <?php else: ?>
                                <span class="text-gray-400 text-xs">Sin rol</span>
                            <?php endif; ?>
                        </td>

                        <!-- Acceso (activo del usuario) -->
                        <td>
                            <?php if (!isset($e['usuarioActivo']) || $e['usuario'] === null): ?>
                                <span class="badge badge-warning">Sin acceso</span>
                            <?php elseif ($e['usuarioActivo']): ?>
                                <span class="badge badge-success">Activo</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Inactivo</span>
                            <?php endif; ?>
                        </td>

                        <!-- Acciones -->
                        <td>
                            <div class="flex items-center justify-center gap-1">
                                <a href="index.php?page=empleados&action=editar&id=<?= $e['idEmpleado'] ?>"
                                   class="p-2 rounded-lg hover:bg-blue-50 text-blue-500 transition-colors"
                                   data-tooltip="Editar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <button onclick="confirmarEliminar(
                                            'index.php?page=empleados&action=eliminar&id=<?= $e['idEmpleado'] ?>',
                                            '<?= htmlspecialchars($e['nombreCompleto'], ENT_QUOTES) ?>')"
                                        class="p-2 rounded-lg hover:bg-red-50 text-red-400 transition-colors"
                                        data-tooltip="Dar de baja">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
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
