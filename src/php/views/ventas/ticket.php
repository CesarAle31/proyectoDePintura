<?php
/**
 * ============================================================
 *  Ticket de Venta — Vista imprimible
 * ============================================================
 *  Muestra el detalle de una venta con formato de recibo.
 *  Incluye botón de impresión y opción de regresar.
 * ============================================================
 */
?>

<!-- Barra de acciones (NO se imprime) -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 no-print">
    <div class="flex items-center gap-4">
        <a href="index.php?page=ventas" class="p-2 rounded-xl hover:bg-gray-100 transition-colors">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h2 class="text-2xl font-display font-bold text-gray-900">Ticket de Venta</h2>
            <p class="text-gray-500 mt-0.5 text-sm">Folio: <strong class="text-brand-600">#<?= $venta['folio'] ?></strong></p>
        </div>
    </div>
    <div class="flex items-center gap-3">
        <button onclick="window.print()"
                class="btn-primary inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold no-print">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Imprimir
        </button>
        <a href="index.php?page=ventas"
           class="btn-secondary inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold no-print">
            Volver a Ventas
        </a>
    </div>
</div>

<!-- ════════ TICKET IMPRIMIBLE ════════ -->
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden ticket-container ticket-print">

        <!-- Encabezado del ticket -->
        <div class="bg-gradient-to-r from-brand-500 to-brand-600 text-white px-8 py-6 text-center">
            <div class="flex items-center justify-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center overflow-hidden">
                    <img src="src/assets/img/Captura de pantalla 2026-05-24 232344 (1).png" alt="IPESA Pinturas" class="w-full h-full object-contain">
                </div>
                <h1 class="text-2xl font-display font-bold tracking-tight">IPESA Pinturas</h1>
            </div>
            <p class="text-brand-100 text-sm">Pinturas y Acabados Profesionales</p>
            <p class="text-brand-200 text-xs mt-1">Oaxaca de Juárez, Oaxaca</p>
        </div>

        <!-- Info de la venta -->
        <div class="px-8 py-5 border-b border-dashed border-gray-200">
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-gray-400 text-xs font-semibold uppercase">Folio</span>
                    <p class="font-mono font-bold text-brand-600 text-lg">#<?= $venta['folio'] ?></p>
                </div>
                <div class="text-right">
                    <span class="text-gray-400 text-xs font-semibold uppercase">Fecha</span>
                    <p class="font-semibold text-gray-800"><?= date('d/m/Y', strtotime($venta['fecha'])) ?></p>
                </div>
                <div>
                    <span class="text-gray-400 text-xs font-semibold uppercase">Cliente</span>
                    <p class="font-medium text-gray-800"><?= htmlspecialchars($venta['cliente']) ?></p>
                </div>
                <div class="text-right">
                    <span class="text-gray-400 text-xs font-semibold uppercase">Atendió</span>
                    <p class="font-medium text-gray-800"><?= htmlspecialchars($venta['empleado']) ?></p>
                </div>
            </div>
        </div>

        <!-- Detalle de productos -->
        <div class="px-8 py-5">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Detalle de Productos</h3>
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b-2 border-gray-200">
                        <th class="text-left py-2 font-semibold text-gray-500 text-xs uppercase">Producto</th>
                        <th class="text-center py-2 font-semibold text-gray-500 text-xs uppercase">Cant.</th>
                        <th class="text-right py-2 font-semibold text-gray-500 text-xs uppercase">Precio</th>
                        <th class="text-right py-2 font-semibold text-gray-500 text-xs uppercase">Importe</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($detalles as $d): ?>
                    <tr class="border-b border-gray-100">
                        <td class="py-3">
                            <p class="font-medium text-gray-800"><?= htmlspecialchars($d['pintura']) ?></p>
                            <p class="text-xs text-gray-400"><?= htmlspecialchars($d['color']) ?> · <?= htmlspecialchars($d['presentacion']) ?> · <?= htmlspecialchars($d['capacidad']) ?></p>
                        </td>
                        <td class="py-3 text-center font-semibold text-gray-700"><?= $d['cantidad'] ?></td>
                        <td class="py-3 text-right text-gray-600">$<?= number_format($d['precio'], 2) ?></td>
                        <td class="py-3 text-right font-bold text-gray-900">$<?= number_format($d['importe'], 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Totales -->
        <div class="px-8 py-5 bg-gray-50 border-t border-dashed border-gray-200">
            <div class="flex justify-between items-center mb-2">
                <span class="text-sm text-gray-500">Artículos:</span>
                <span class="font-semibold text-gray-700"><?= count($detalles) ?> producto<?= count($detalles) !== 1 ? 's' : '' ?></span>
            </div>
            <div class="flex justify-between items-center mb-2">
                <span class="text-sm text-gray-500">Unidades:</span>
                <span class="font-semibold text-gray-700"><?= array_sum(array_column($detalles, 'cantidad')) ?></span>
            </div>
            <hr class="border-gray-200 my-3">
            <div class="flex justify-between items-center">
                <span class="text-lg font-bold text-gray-700">TOTAL:</span>
                <span class="text-2xl font-bold text-brand-600">$<?= number_format($venta['montoTotal'], 2) ?></span>
            </div>
        </div>

        <!-- Pie del ticket -->
        <div class="px-8 py-5 text-center border-t border-gray-100">
            <p class="text-sm text-gray-500">¡Gracias por su compra!</p>
            <p class="text-xs text-gray-400 mt-1">Este documento es un comprobante de venta</p>
            <div class="mt-3 flex justify-center">
                <div class="w-48 h-px bg-gradient-to-r from-transparent via-gray-300 to-transparent"></div>
            </div>
            <p class="text-xs text-gray-300 mt-2">IPESA Pinturas POS — <?= date('d/m/Y H:i') ?></p>
        </div>
    </div>
</div>
