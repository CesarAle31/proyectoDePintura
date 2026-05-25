<?php
/**
 * ============================================================
 *  Productos / Pinturas — Formulario (crear / editar)
 * ============================================================
 */
$esEdicion = !empty($producto);
$actionUrl = "index.php?page=productos&action=" . ($esEdicion ? 'actualizar' : 'guardar');
?>

<!-- Encabezado -->
<div class="flex items-center gap-4 mb-6">
    <a href="index.php?page=productos" class="p-2 rounded-xl hover:bg-gray-100 transition-colors">
        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
    </a>
    <div>
        <h2 class="text-2xl font-display font-bold text-gray-900"><?= $esEdicion ? 'Editar' : 'Nueva' ?> Pintura</h2>
        <p class="text-gray-500 mt-0.5 text-sm"><?= $esEdicion ? 'Modifica los datos del producto' : 'Registra una nueva pintura al catálogo' ?></p>
    </div>
</div>

<!-- Formulario -->
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 lg:p-8 max-w-3xl animate-fadeInUp">
    <form method="POST" action="<?= $actionUrl ?>" id="formProducto">

        <!-- ID -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">ID Pintura</label>
                <input type="number" name="idPintura"
                       value="<?= $esEdicion ? $producto['idPintura'] : $nextId ?>"
                       <?= $esEdicion ? 'readonly' : '' ?>
                       class="form-input w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm <?= $esEdicion ? 'bg-gray-50 text-gray-400' : '' ?>"
                       required>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Nombre</label>
                <input type="text" name="nombre"
                       value="<?= htmlspecialchars($producto['nombre'] ?? '') ?>"
                       class="form-input w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"
                       placeholder="Ej: VINACRIL Mate" required>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Color</label>
                <input type="text" name="color"
                       value="<?= htmlspecialchars($producto['color'] ?? '') ?>"
                       class="form-input w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"
                       placeholder="Ej: Blanco, Rojo, Azul" required>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Capacidad</label>
                <input type="text" name="capacidad"
                       value="<?= htmlspecialchars($producto['capacidad'] ?? '') ?>"
                       class="form-input w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"
                       placeholder="Ej: 4 litros, 19 litros" required>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Presentación</label>
                <select name="presentacion" class="form-input w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm" required>
                    <option value="">Seleccionar...</option>
                    <?php
                    $presentaciones = ['Cubeta', 'Galón', 'Cuarto', 'Litro', 'Aerosol'];
                    foreach ($presentaciones as $pres):
                        $sel = ($producto['presentacion'] ?? '') === $pres ? 'selected' : '';
                    ?>
                    <option value="<?= $pres ?>" <?= $sel ?>><?= $pres ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Clasificación</label>
                <select name="claveClasificacion" class="form-input w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm" required>
                    <option value="">Seleccionar línea...</option>
                    <?php foreach ($clasificaciones as $c): ?>
                    <option value="<?= $c['claveClasificacion'] ?>"
                        <?= ($producto['claveClasificacion'] ?? '') == $c['claveClasificacion'] ? 'selected' : '' ?>>
                        <?= $c['claveClasificacion'] ?> — <?= htmlspecialchars($c['linea']) ?> (<?= htmlspecialchars($c['tipo']) ?>)
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Proveedor</label>
                <select name="idProveedor" class="form-input w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm" required>
                    <option value="">Seleccionar proveedor...</option>
                    <?php foreach ($proveedores as $pv): ?>
                    <option value="<?= $pv['idProveedor'] ?>"
                        <?= ($producto['idProveedor'] ?? '') == $pv['idProveedor'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($pv['razonSocial']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Costo ($)</label>
                <input type="number" name="costo" step="0.01" min="0"
                       value="<?= $producto['costo'] ?? '' ?>"
                       class="form-input w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"
                       placeholder="0.00" required>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Stock</label>
                <input type="number" name="stock" min="0"
                       value="<?= $producto['stock'] ?? 0 ?>"
                       class="form-input w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm"
                       required>
            </div>
        </div>

        <!-- Botones -->
        <div class="flex items-center gap-3 mt-8 pt-6 border-t border-gray-100">
            <button type="submit" class="btn-primary px-6 py-2.5 rounded-xl text-sm font-semibold inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <?= $esEdicion ? 'Guardar Cambios' : 'Registrar Pintura' ?>
            </button>
            <a href="index.php?page=productos" class="btn-secondary px-6 py-2.5 rounded-xl text-sm font-semibold">
                Cancelar
            </a>
        </div>
    </form>
</div>
