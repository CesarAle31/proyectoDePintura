<?php
/**
 * ============================================================
 *  Clientes — Formulario (crear / editar)
 * ============================================================
 */
$esEdicion = !empty($cliente);
$actionUrl = "index.php?page=clientes&action=" . ($esEdicion ? 'actualizar' : 'guardar');
?>

<div class="flex items-center gap-4 mb-6">
    <a href="index.php?page=clientes" class="p-2 rounded-xl hover:bg-gray-100 transition-colors">
        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </a>
    <div>
        <h2 class="text-2xl font-display font-bold text-gray-900"><?= $esEdicion ? 'Editar' : 'Nuevo' ?> Cliente</h2>
        <p class="text-gray-500 mt-0.5 text-sm"><?= $esEdicion ? 'Modifica los datos del cliente' : 'Registra un nuevo cliente' ?></p>
    </div>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 lg:p-8 max-w-3xl animate-fadeInUp">
    <form method="POST" action="<?= $actionUrl ?>">
        <?php if ($esEdicion): ?>
            <input type="hidden" name="idCliente" value="<?= $cliente['idCliente'] ?>">
        <?php endif; ?>

        <!-- Sección: Datos personales -->
        <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Datos Personales</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Nombre</label>
                <input type="text" name="nombre" value="<?= htmlspecialchars($cliente['nombre'] ?? '') ?>"
                       class="form-input w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm" required>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Apellido Paterno</label>
                <input type="text" name="apellidoP" value="<?= htmlspecialchars($cliente['apellidoP'] ?? '') ?>"
                       class="form-input w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm" required>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Apellido Materno</label>
                <input type="text" name="apellidoM" value="<?= htmlspecialchars($cliente['apellidoM'] ?? '') ?>"
                       class="form-input w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm" required>
            </div>
        </div>

        <!-- Sección: Contacto -->
        <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Contacto</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Correo Electrónico</label>
                <input type="email" name="correo" value="<?= htmlspecialchars($cliente['correo'] ?? '') ?>"
                       class="form-input w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"
                       placeholder="correo@ejemplo.com" required>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Teléfono</label>
                <input type="tel" name="telefono" value="<?= htmlspecialchars($cliente['telefono'] ?? '') ?>"
                       class="form-input w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"
                       placeholder="951 123 4567">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Teléfono Adicional</label>
                <input type="tel" name="telefonoCliente" value="<?= htmlspecialchars($cliente['telefonoCliente'] ?? '') ?>"
                       class="form-input w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"
                       placeholder="Opcional">
            </div>
        </div>

        <!-- Sección: Dirección -->
        <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Dirección</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Calle</label>
                <input type="text" name="calle" value="<?= htmlspecialchars($cliente['calle'] ?? '') ?>"
                       class="form-input w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm" <?= $esEdicion ? '' : 'required' ?>>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Número</label>
                <input type="text" name="numero" value="<?= htmlspecialchars($cliente['numero'] ?? '') ?>"
                       class="form-input w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm" <?= $esEdicion ? '' : 'required' ?>>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Colonia</label>
                <select name="idColonia" class="form-input w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm" <?= $esEdicion ? '' : 'required' ?>>
                    <option value="">Seleccionar colonia...</option>
                    <?php foreach ($colonias as $col): ?>
                    <option value="<?= $col['idColonia'] ?>"
                        <?= ($cliente['idColonia'] ?? '') == $col['idColonia'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($col['colonia']) ?> (CP <?= $col['cp'] ?>) — <?= htmlspecialchars($col['municipio']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Botones -->
        <div class="flex items-center gap-3 pt-6 border-t border-gray-100">
            <button type="submit" class="btn-primary px-6 py-2.5 rounded-xl text-sm font-semibold inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <?= $esEdicion ? 'Guardar Cambios' : 'Registrar Cliente' ?>
            </button>
            <a href="index.php?page=clientes" class="btn-secondary px-6 py-2.5 rounded-xl text-sm font-semibold">Cancelar</a>
        </div>
    </form>
</div>
