<?php
/**
 * ============================================================
 *  Empleados — Formulario (crear / editar)
 * ============================================================
 */
$esEdicion = !empty($empleado);
$actionUrl = "index.php?page=empleados&action=" . ($esEdicion ? 'actualizar' : 'guardar');
?>

<div class="flex items-center gap-4 mb-6">
    <a href="index.php?page=empleados" class="p-2 rounded-xl hover:bg-gray-100 transition-colors">
        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </a>
    <div>
        <h2 class="text-2xl font-display font-bold text-gray-900"><?= $esEdicion ? 'Editar' : 'Nuevo' ?> Empleado</h2>
        <p class="text-gray-500 mt-0.5 text-sm"><?= $esEdicion ? 'Modifica los datos del empleado' : 'Registra un nuevo empleado' ?></p>
    </div>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 lg:p-8 max-w-2xl animate-fadeInUp">
    <form method="POST" action="<?= $actionUrl ?>">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">ID Empleado</label>
                <input type="number" name="idEmpleado"
                       value="<?= $esEdicion ? $empleado['idEmpleado'] : $nextId ?>"
                       <?= $esEdicion ? 'readonly' : '' ?>
                       class="form-input w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm <?= $esEdicion ? 'bg-gray-50 text-gray-400' : '' ?>"
                       required>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Nombre</label>
                <input type="text" name="nombre" value="<?= htmlspecialchars($empleado['nombre'] ?? '') ?>"
                       class="form-input w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm" required>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Apellido Paterno</label>
                <input type="text" name="apellidoP" value="<?= htmlspecialchars($empleado['apellidoP'] ?? '') ?>"
                       class="form-input w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm" required>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Apellido Materno</label>
                <input type="text" name="apellidoM" value="<?= htmlspecialchars($empleado['apellidoM'] ?? '') ?>"
                       class="form-input w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm" required>
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Teléfono</label>
                <input type="tel" name="telefonoEmpleado" value="<?= htmlspecialchars($empleado['telefonoEmpleado'] ?? '') ?>"
                       class="form-input w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"
                       placeholder="951 123 4567">
            </div>
        </div>

        <div class="flex items-center gap-3 mt-8 pt-6 border-t border-gray-100">
            <button type="submit" class="btn-primary px-6 py-2.5 rounded-xl text-sm font-semibold inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <?= $esEdicion ? 'Guardar Cambios' : 'Registrar Empleado' ?>
            </button>
            <a href="index.php?page=empleados" class="btn-secondary px-6 py-2.5 rounded-xl text-sm font-semibold">Cancelar</a>
        </div>
    </form>
</div>
