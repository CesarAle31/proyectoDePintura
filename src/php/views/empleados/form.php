<?php
/**
 * ============================================================
 *  Empleados — Formulario crear / editar
 * ============================================================
 */
$esEdicion    = !empty($empleado);
$tieneUsuario = !empty($empleado['usuario']);
$actionUrl    = 'index.php?page=empleados&action=' . ($esEdicion ? 'actualizar' : 'guardar');
?>

<div class="flex items-center gap-4 mb-6">
    <a href="index.php?page=empleados" class="p-2 rounded-xl hover:bg-gray-100 transition-colors" title="Volver">
        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
    </a>
    <div>
        <h2 class="text-2xl font-display font-bold text-gray-900">
            <?= $esEdicion ? 'Editar' : 'Nuevo' ?> Empleado
        </h2>
        <p class="text-gray-500 mt-0.5 text-sm">
            <?= $esEdicion ? 'Modifica los datos del empleado' : 'Registra un nuevo miembro del personal' ?>
        </p>
    </div>
</div>

<form method="POST" action="<?= $actionUrl ?>" novalidate id="formEmpleado">

    <?php if ($esEdicion): ?>
        <input type="hidden" name="idEmpleado" value="<?= (int) $empleado['idEmpleado'] ?>">
    <?php endif; ?>

    <!-- ══════════════════════════════════
         SECCIÓN 1 — Datos personales
    ═══════════════════════════════════ -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 lg:p-8 max-w-2xl mb-5 animate-fadeInUp">

        <div class="flex items-center gap-2 mb-5">
            <div class="w-7 h-7 rounded-lg bg-brand-50 flex items-center justify-center">
                <svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Datos Personales</h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

            <!-- ID Empleado -->
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                    ID Empleado <span class="text-brand-500">*</span>
                </label>
                <?php if ($esEdicion): ?>
                    <input type="number"
                           value="<?= (int) $empleado['idEmpleado'] ?>"
                           readonly
                           class="form-input w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm bg-gray-50 text-gray-400 cursor-not-allowed">
                <?php else: ?>
                    <input type="number" name="idEmpleado"
                           value="<?= $nextId ?>"
                           min="1" required
                           class="form-input w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm">
                    <p class="text-xs text-gray-400 mt-1">Puedes cambiarlo; no debe existir previamente.</p>
                <?php endif; ?>
            </div>

            <!-- Nombre -->
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                    Nombre <span class="text-brand-500">*</span>
                </label>
                <input type="text" name="nombre"
                       value="<?= htmlspecialchars($empleado['nombre'] ?? '') ?>"
                       required maxlength="50"
                       pattern="[A-Za-záéíóúÁÉÍÓÚñÑ\s]+"
                       title="Solo letras y espacios"
                       placeholder="Ej. Juan"
                       class="form-input w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm">
            </div>

            <!-- Apellido Paterno -->
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                    Apellido Paterno <span class="text-brand-500">*</span>
                </label>
                <input type="text" name="apellidoP"
                       value="<?= htmlspecialchars($empleado['apellidoP'] ?? '') ?>"
                       required maxlength="50"
                       pattern="[A-Za-záéíóúÁÉÍÓÚñÑ\s]+"
                       title="Solo letras y espacios"
                       placeholder="Ej. García"
                       class="form-input w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm">
            </div>

            <!-- Apellido Materno -->
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                    Apellido Materno <span class="text-brand-500">*</span>
                </label>
                <input type="text" name="apellidoM"
                       value="<?= htmlspecialchars($empleado['apellidoM'] ?? '') ?>"
                       required maxlength="50"
                       pattern="[A-Za-záéíóúÁÉÍÓÚñÑ\s]+"
                       title="Solo letras y espacios"
                       placeholder="Ej. López"
                       class="form-input w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm">
            </div>

            <!-- Teléfono -->
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                    Teléfono <span class="text-brand-500">*</span>
                </label>
                <input type="tel" name="telefonoEmpleado"
                       value="<?= htmlspecialchars($empleado['telefonoEmpleado'] ?? '') ?>"
                       required maxlength="10" pattern="[0-9]{10}"
                       title="Exactamente 10 dígitos numéricos"
                       placeholder="Ej. 9511234567"
                       class="form-input w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm">
                <p class="text-xs text-gray-400 mt-1">Exactamente 10 dígitos, sin espacios ni guiones.</p>
            </div>

        </div>
    </div>

    <!-- ══════════════════════════════════
         SECCIÓN 2 — Acceso al sistema
    ═══════════════════════════════════ -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 lg:p-8 max-w-2xl mb-5">

        <div class="flex items-center gap-3 mb-1">
            <div class="w-7 h-7 rounded-lg bg-purple-50 flex items-center justify-center">
                <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
            </div>
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Acceso al Sistema</h3>

            <?php if (!$tieneUsuario): ?>
                <!-- Toggle visible solo cuando no hay usuario previo -->
                <label class="ml-auto flex items-center gap-2 cursor-pointer select-none">
                    <div class="relative">
                        <input type="checkbox" id="toggleAcceso" class="sr-only peer">
                        <div class="w-9 h-5 bg-gray-200 rounded-full peer peer-checked:bg-brand-500 transition-colors"></div>
                        <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform peer-checked:translate-x-4"></div>
                    </div>
                    <span class="text-sm text-gray-600">Crear acceso</span>
                </label>
            <?php else: ?>
                <span class="ml-auto inline-flex items-center gap-1 text-xs font-medium text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    Cuenta activa
                </span>
            <?php endif; ?>
        </div>

        <?php if (!$tieneUsuario): ?>
            <p id="textoSinAcceso" class="text-sm text-gray-400 mb-4">
                Este empleado no tendrá acceso al sistema. Activa el toggle para crear credenciales.
            </p>
        <?php else: ?>
            <p class="text-sm text-gray-400 mb-4">
                Modifica las credenciales. Deja la contraseña en blanco para conservarla.
            </p>
        <?php endif; ?>

        <!-- Campos de acceso — visibles/ocultos según toggle -->
        <div id="seccionAcceso" class="<?= $tieneUsuario ? '' : 'hidden' ?> grid grid-cols-1 md:grid-cols-2 gap-5">

            <!-- Usuario -->
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                    Usuario <span class="text-brand-500">*</span>
                </label>
                <input type="text" name="usuario" id="inputUsuario"
                       value="<?= htmlspecialchars($empleado['usuario'] ?? '') ?>"
                       maxlength="60"
                       <?= $tieneUsuario ? 'required' : '' ?>
                       placeholder="Ej. jgarcia"
                       autocomplete="off"
                       class="form-input w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm">
            </div>

            <!-- Rol -->
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                    Rol <span class="text-brand-500">*</span>
                </label>
                <select name="idRol" id="inputRol"
                        <?= $tieneUsuario ? 'required' : '' ?>
                        class="form-input w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm bg-white">
                    <option value="">— Seleccionar rol —</option>
                    <?php foreach ($roles as $r): ?>
                        <option value="<?= $r['idRol'] ?>"
                            <?= ($empleado['idRol'] ?? '') == $r['idRol'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($r['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Contraseña -->
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                    Contraseña
                    <span class="text-brand-500" id="labelPassObligatorio">
                        <?= $tieneUsuario ? '(opcional — en blanco para conservar)' : '*' ?>
                    </span>
                </label>
                <div class="relative">
                    <input type="password" name="password" id="inputPassword"
                           maxlength="100"
                           <?= (!$esEdicion && !$tieneUsuario) ? '' : '' ?>
                           placeholder="<?= $tieneUsuario ? 'Dejar en blanco para no cambiarla' : 'Mínimo 6 caracteres' ?>"
                           autocomplete="new-password"
                           class="form-input w-full px-4 pr-12 py-2.5 rounded-xl border border-gray-200 text-sm">
                    <button type="button" id="togglePass"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                        <svg id="iconoOjo" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
            </div>

        </div>
    </div>

    <!-- Botones de acción -->
    <div class="max-w-2xl flex items-center gap-3">
        <button type="submit" class="btn-primary px-6 py-2.5 rounded-xl text-sm font-semibold inline-flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <?= $esEdicion ? 'Guardar Cambios' : 'Registrar Empleado' ?>
        </button>
        <a href="index.php?page=empleados" class="btn-secondary px-6 py-2.5 rounded-xl text-sm font-semibold">
            Cancelar
        </a>
    </div>

</form>

<script>
(function () {
    // ── Toggle de acceso al sistema ──────────────────────────
    const toggle      = document.getElementById('toggleAcceso');
    const seccion     = document.getElementById('seccionAcceso');
    const textoSin    = document.getElementById('textoSinAcceso');
    const inputUsr    = document.getElementById('inputUsuario');
    const inputRol    = document.getElementById('inputRol');
    const inputPass   = document.getElementById('inputPassword');

    if (toggle) {
        toggle.addEventListener('change', function () {
            const activo = this.checked;

            seccion.classList.toggle('hidden', !activo);
            if (textoSin) textoSin.classList.toggle('hidden', activo);

            // Activar/desactivar required dinámicamente
            inputUsr.required  = activo;
            inputRol.required  = activo;
            // La contraseña solo es obligatoria en creación nueva
            <?php if (!$esEdicion): ?>
            inputPass.required = activo;
            <?php endif; ?>
        });
    }

    // ── Mostrar / ocultar contraseña ─────────────────────────
    const btnTogglePass = document.getElementById('togglePass');
    if (btnTogglePass && inputPass) {
        btnTogglePass.addEventListener('click', function () {
            const esTexto = inputPass.type === 'text';
            inputPass.type = esTexto ? 'password' : 'text';
            document.getElementById('iconoOjo').innerHTML = esTexto
                ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>'
                : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>';
        });
    }

    // ── Validación custom antes de enviar ────────────────────
    document.getElementById('formEmpleado').addEventListener('submit', function (e) {
        // Verificar teléfono exactamente 10 dígitos
        const tel = document.querySelector('[name="telefonoEmpleado"]');
        if (tel && !/^[0-9]{10}$/.test(tel.value.trim())) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Teléfono inválido',
                text: 'El teléfono debe tener exactamente 10 dígitos numéricos.',
                confirmButtonColor: '#e51e25',
            });
            tel.focus();
            return;
        }

        // Si la sección de acceso está activa, validar campos
        const accesoActivo = toggle ? toggle.checked : <?= $tieneUsuario ? 'true' : 'false' ?>;
        if (accesoActivo) {
            const usr = inputUsr ? inputUsr.value.trim() : '';
            const rol = inputRol ? inputRol.value : '';
            const pass = inputPass ? inputPass.value.trim() : '';

            if (!usr) {
                e.preventDefault();
                Swal.fire({ icon: 'warning', title: 'Usuario requerido', text: 'El nombre de usuario es obligatorio para crear acceso.', confirmButtonColor: '#e51e25' });
                if (inputUsr) inputUsr.focus();
                return;
            }
            if (!rol) {
                e.preventDefault();
                Swal.fire({ icon: 'warning', title: 'Rol requerido', text: 'Debes seleccionar un rol.', confirmButtonColor: '#e51e25' });
                if (inputRol) inputRol.focus();
                return;
            }
            <?php if (!$tieneUsuario): ?>
            if (!pass) {
                e.preventDefault();
                Swal.fire({ icon: 'warning', title: 'Contraseña requerida', text: 'La contraseña es obligatoria para crear acceso al sistema.', confirmButtonColor: '#e51e25' });
                if (inputPass) inputPass.focus();
                return;
            }
            <?php endif; ?>
        }
    });
})();
</script>
