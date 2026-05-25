<?php
/**
 * ============================================================
 *  Proveedores — Formulario (crear / editar)
 * ============================================================
 */
$esEdicion = !empty($proveedor);
$actionUrl = "index.php?page=proveedores&action=" . ($esEdicion ? 'actualizar' : 'guardar');
?>

<div class="flex items-center gap-4 mb-6">
    <a href="index.php?page=proveedores" class="p-2 rounded-xl hover:bg-gray-100 transition-colors">
        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </a>
    <div>
        <h2 class="text-2xl font-display font-bold text-gray-900"><?= $esEdicion ? 'Editar' : 'Nuevo' ?> Proveedor</h2>
        <p class="text-gray-500 mt-0.5 text-sm"><?= $esEdicion ? 'Modifica los datos del proveedor' : 'Registra un nuevo proveedor' ?></p>
    </div>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 lg:p-8 max-w-3xl animate-fadeInUp">
    <form method="POST" action="<?= $actionUrl ?>">
        <?php if ($esEdicion): ?>
            <input type="hidden" name="idProveedor" value="<?= $proveedor['idProveedor'] ?>">
        <?php endif; ?>

        <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Datos del Proveedor</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Razón Social</label>
                <input type="text" name="razonSocial" value="<?= htmlspecialchars($proveedor['razonSocial'] ?? '') ?>"
                       class="form-input w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"
                       placeholder="Nombre de la empresa" required>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Teléfono</label>
                <input type="tel" name="telefono" value="<?= htmlspecialchars($proveedor['telefono'] ?? '') ?>"
                       class="form-input w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"
                       placeholder="951 123 4567">
            </div>
        </div>

        <?php if (!$esEdicion): ?>
        <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Dirección</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Calle</label>
                <input type="text" name="calle" class="form-input w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm" required>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Número</label>
                <input type="text" name="numero" class="form-input w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm" required>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Colonia</label>
                <select name="idColonia" class="form-input w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm" required>
                    <option value="">Seleccionar colonia...</option>
                    <?php foreach ($colonias as $col): ?>
                    <option value="<?= $col['idColonia'] ?>"><?= htmlspecialchars($col['colonia']) ?> (CP <?= $col['cp'] ?>) — <?= htmlspecialchars($col['municipio']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <?php endif; ?>

        <div class="flex items-center gap-3 pt-6 border-t border-gray-100">
            <button type="submit" class="btn-primary px-6 py-2.5 rounded-xl text-sm font-semibold inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <?= $esEdicion ? 'Guardar Cambios' : 'Registrar Proveedor' ?>
            </button>
            <a href="index.php?page=proveedores" class="btn-secondary px-6 py-2.5 rounded-xl text-sm font-semibold">Cancelar</a>
        </div>
    </form>
</div>
