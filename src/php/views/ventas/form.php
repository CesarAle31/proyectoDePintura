<?php
/**
 * ============================================================
 *  Ventas — Formulario POS (Punto de Venta)
 * ============================================================
 *  Interfaz tipo caja registradora: seleccionar productos,
 *  agregar al carrito, calcular totales, enviar vía AJAX.
 * ============================================================
 */
?>

<div class="flex items-center gap-4 mb-6">
    <a href="index.php?page=ventas" class="p-2 rounded-xl hover:bg-gray-100 transition-colors">
        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </a>
    <div>
        <h2 class="text-2xl font-display font-bold text-gray-900">Nueva Venta</h2>
        <p class="text-gray-500 mt-0.5 text-sm">Punto de venta — Folio: <strong class="text-brand-600">#<?= $nextFolio ?></strong></p>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    <!-- ═══ COLUMNA IZQUIERDA: Datos de venta + Productos ═══ -->
    <div class="xl:col-span-2 space-y-6">

        <!-- Datos de la venta -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Datos de la Venta</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5">Empleado</label>
                    <select id="posEmpleado" class="form-input w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm" required>
                        <option value="">Seleccionar...</option>
                        <?php foreach ($empleados as $e): ?>
                        <option value="<?= $e['idEmpleado'] ?>"><?= htmlspecialchars($e['nombreCompleto']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5">Cliente</label>
                    <select id="posCliente" class="form-input w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm" required>
                        <option value="">Seleccionar...</option>
                        <?php foreach ($clientes as $c): ?>
                        <option value="<?= $c['idCliente'] ?>"><?= htmlspecialchars($c['nombreCompleto']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5">Fecha</label>
                    <input type="text" value="<?= date('d/m/Y') ?>" class="form-input w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm bg-gray-50" readonly>
                </div>
            </div>
        </div>

        <!-- Agregar productos -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Agregar Productos</h3>
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-end">
                <div class="sm:col-span-5">
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5">Producto</label>
                    <select id="posProducto" class="form-input w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm">
                        <option value="">Seleccionar pintura...</option>
                        <?php foreach ($productos as $p): ?>
                        <option value="<?= $p['idPintura'] ?>"
                                data-nombre="<?= htmlspecialchars($p['nombre']) ?>"
                                data-color="<?= htmlspecialchars($p['color']) ?>"
                                data-precio="<?= $p['costo'] ?>"
                                data-stock="<?= $p['stock'] ?>"
                                data-presentacion="<?= htmlspecialchars($p['presentacion']) ?>">
                            <?= htmlspecialchars($p['nombre']) ?> — <?= htmlspecialchars($p['color']) ?> (<?= $p['presentacion'] ?>) — Stock: <?= $p['stock'] ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5">Precio</label>
                    <input type="number" id="posPrecio" step="0.01" min="0"
                           class="form-input w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm" placeholder="0.00">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5">Cantidad</label>
                    <input type="number" id="posCantidad" min="1" value="1"
                           class="form-input w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm">
                </div>
                <div class="sm:col-span-3">
                    <button onclick="agregarAlCarrito()"
                            class="w-full btn-primary px-4 py-2.5 rounded-xl text-sm font-semibold inline-flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Agregar
                    </button>
                </div>
            </div>
            <p id="infoStock" class="text-xs text-gray-400 mt-2 hidden">
                Stock disponible: <span id="stockDisp" class="font-semibold"></span> unidades
            </p>
        </div>

        <!-- Detalle del carrito -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
            <div class="p-5 border-b border-gray-100">
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Detalle de Venta</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="table-modern w-full" id="tablaCarrito">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Producto</th>
                            <th>Color</th>
                            <th>Precio</th>
                            <th>Cantidad</th>
                            <th>Importe</th>
                            <th class="text-center">Quitar</th>
                        </tr>
                    </thead>
                    <tbody id="carritoBody">
                        <tr id="carritoVacio">
                            <td colspan="7" class="text-center text-gray-400 py-8 text-sm">
                                Agrega productos para iniciar la venta
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ═══ COLUMNA DERECHA: Resumen / Totales ═══ -->
    <div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sticky top-24">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-6">Resumen de Venta</h3>

            <div class="space-y-4 mb-6">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">Folio:</span>
                    <span class="font-mono font-bold text-brand-600">#<?= $nextFolio ?></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">Productos:</span>
                    <span id="totalItems" class="font-semibold text-gray-800">0</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">Unidades:</span>
                    <span id="totalUnidades" class="font-semibold text-gray-800">0</span>
                </div>
                <hr class="border-gray-100">
                <div class="flex justify-between items-center">
                    <span class="text-lg font-semibold text-gray-700">Total:</span>
                    <span id="totalMonto" class="text-2xl font-bold text-brand-600">$0.00</span>
                </div>
            </div>

            <button onclick="procesarVenta()"
                    id="btnProcesar"
                    disabled
                    class="w-full bg-gradient-to-r from-emerald-500 to-emerald-600 text-white px-6 py-3 rounded-xl text-sm font-bold
                           shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/40
                           disabled:opacity-40 disabled:cursor-not-allowed disabled:shadow-none
                           transition-all inline-flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Procesar Venta
            </button>

            <button onclick="limpiarCarrito()"
                    class="w-full mt-3 btn-secondary px-6 py-2.5 rounded-xl text-sm font-semibold inline-flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                Limpiar
            </button>
        </div>
    </div>
</div>

<!-- ═══ JavaScript POS ═══ -->
<script>
const FOLIO = <?= $nextFolio ?>;
let carrito = [];

// Al cambiar producto, poner precio y mostrar stock
document.getElementById('posProducto').addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    if (this.value) {
        document.getElementById('posPrecio').value = opt.dataset.precio;
        document.getElementById('stockDisp').textContent = opt.dataset.stock;
        document.getElementById('infoStock').classList.remove('hidden');
    } else {
        document.getElementById('posPrecio').value = '';
        document.getElementById('infoStock').classList.add('hidden');
    }
});

function agregarAlCarrito() {
    const sel = document.getElementById('posProducto');
    const opt = sel.options[sel.selectedIndex];
    const precio = parseFloat(document.getElementById('posPrecio').value);
    const cantidad = parseInt(document.getElementById('posCantidad').value);

    if (!sel.value || !precio || !cantidad) {
        showToast('Completa producto, precio y cantidad', 'warning');
        return;
    }

    const stockDisp = parseInt(opt.dataset.stock);
    // Verificar stock considerando lo ya en carrito
    const yaEnCarrito = carrito.filter(i => i.idPintura == sel.value).reduce((s, i) => s + i.cantidad, 0);
    if (yaEnCarrito + cantidad > stockDisp) {
        showToast(`Stock insuficiente. Disponible: ${stockDisp - yaEnCarrito}`, 'error');
        return;
    }

    // Verificar si ya existe en carrito
    const existente = carrito.find(i => i.idPintura == sel.value && i.precio == precio);
    if (existente) {
        existente.cantidad += cantidad;
        existente.importe = existente.cantidad * existente.precio;
    } else {
        carrito.push({
            idPintura: parseInt(sel.value),
            nombre: opt.dataset.nombre,
            color: opt.dataset.color,
            presentacion: opt.dataset.presentacion,
            precio: precio,
            cantidad: cantidad,
            importe: precio * cantidad,
            stockMax: stockDisp,
        });
    }

    renderCarrito();
    // Reset
    sel.value = '';
    document.getElementById('posPrecio').value = '';
    document.getElementById('posCantidad').value = 1;
    document.getElementById('infoStock').classList.add('hidden');
}

function quitarDelCarrito(idx) {
    carrito.splice(idx, 1);
    renderCarrito();
}

function renderCarrito() {
    const body = document.getElementById('carritoBody');
    const vacio = document.getElementById('carritoVacio');

    if (carrito.length === 0) {
        body.innerHTML = '';
        body.appendChild(createEmptyRow());
        actualizarTotales();
        return;
    }

    body.innerHTML = '';
    carrito.forEach((item, i) => {
        const tr = document.createElement('tr');
        tr.className = 'table-row';
        tr.innerHTML = `
            <td class="font-mono text-xs text-gray-400">${i + 1}</td>
            <td class="font-medium text-gray-800 text-sm">${item.nombre} (${item.presentacion})</td>
            <td class="text-sm">${item.color}</td>
            <td class="text-sm">$${item.precio.toFixed(2)}</td>
            <td class="text-sm font-semibold">${item.cantidad}</td>
            <td class="text-sm font-bold text-gray-900">$${item.importe.toFixed(2)}</td>
            <td class="text-center">
                <button onclick="quitarDelCarrito(${i})" class="p-1.5 rounded-lg hover:bg-red-50 text-red-400 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </td>
        `;
        body.appendChild(tr);
    });

    actualizarTotales();
}

function createEmptyRow() {
    const tr = document.createElement('tr');
    tr.id = 'carritoVacio';
    tr.innerHTML = '<td colspan="7" class="text-center text-gray-400 py-8 text-sm">Agrega productos para iniciar la venta</td>';
    return tr;
}

function actualizarTotales() {
    const items = carrito.length;
    const unidades = carrito.reduce((s, i) => s + i.cantidad, 0);
    const total = carrito.reduce((s, i) => s + i.importe, 0);

    document.getElementById('totalItems').textContent = items;
    document.getElementById('totalUnidades').textContent = unidades;
    document.getElementById('totalMonto').textContent = '$' + total.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    document.getElementById('btnProcesar').disabled = items === 0;
}

function limpiarCarrito() {
    if (carrito.length === 0) return;
    Swal.fire({
        title: '¿Limpiar carrito?',
        text: 'Se quitarán todos los productos agregados.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Sí, limpiar',
        cancelButtonText: 'Cancelar',
        customClass: { popup: 'rounded-2xl' }
    }).then(r => {
        if (r.isConfirmed) {
            carrito = [];
            renderCarrito();
        }
    });
}

function procesarVenta() {
    const empleado = document.getElementById('posEmpleado').value;
    const cliente = document.getElementById('posCliente').value;

    if (!empleado || !cliente) {
        showToast('Selecciona empleado y cliente', 'warning');
        return;
    }
    if (carrito.length === 0) {
        showToast('Agrega al menos un producto', 'warning');
        return;
    }

    const total = carrito.reduce((s, i) => s + i.importe, 0);

    Swal.fire({
        title: '¿Confirmar venta?',
        html: `<p class="text-gray-600">Total: <strong class="text-xl">$${total.toFixed(2)}</strong></p><p class="text-sm text-gray-400 mt-1">Folio: #${FOLIO}</p>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Confirmar',
        cancelButtonText: 'Revisar',
        customClass: { popup: 'rounded-2xl' }
    }).then(r => {
        if (r.isConfirmed) enviarVenta(empleado, cliente, total);
    });
}

async function enviarVenta(empleado, cliente, total) {
    const payload = {
        folio: FOLIO,
        idEmpleado: parseInt(empleado),
        idCliente: parseInt(cliente),
        montoTotal: total,
        detalles: carrito.map(i => ({
            idPintura: i.idPintura,
            cantidad: i.cantidad,
            precio: i.precio,
            importe: i.importe,
        })),
    };

    try {
        const res = await fetch('index.php?page=ventas&action=guardar', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload),
        });
        const data = await res.json();

        if (data.ok) {
            Swal.fire({
                icon: 'success',
                title: '¡Venta registrada!',
                html: `<p>Folio: <strong>#${data.folio}</strong></p>`,
                confirmButtonColor: '#ed7425',
                confirmButtonText: 'Ver Ticket',
                showCancelButton: true,
                cancelButtonText: 'Ir a Ventas',
                customClass: { popup: 'rounded-2xl' }
            }).then(r => {
                if (r.isConfirmed) {
                    window.location.href = `index.php?page=ventas&action=ticket&folio=${data.folio}`;
                } else {
                    window.location.href = 'index.php?page=ventas&msg=venta_ok';
                }
            });
        } else {
            showToast(data.error || 'Error al procesar', 'error');
        }
    } catch (err) {
        showToast('Error de conexión: ' + err.message, 'error');
    }
}
</script>
