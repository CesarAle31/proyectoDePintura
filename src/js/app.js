/**
 * ============================================================
 *  app.js — JavaScript principal ColorMax POS
 * ============================================================
 *  Controla: sidebar, alertas SweetAlert2, notificaciones
 *  de éxito/error vía URL params, búsqueda global.
 * ============================================================
 */

/* ─── Sidebar Toggle (Móvil) ─── */
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    sidebar.classList.toggle('-translate-x-full');
    overlay.classList.toggle('active');
}

// Cerrar sidebar al hacer click en overlay
document.addEventListener('DOMContentLoaded', () => {
    const overlay = document.getElementById('sidebarOverlay');
    if (overlay) {
        overlay.addEventListener('click', toggleSidebar);
    }

    // ─── Notificaciones por URL params ───
    const params = new URLSearchParams(window.location.search);
    const msg = params.get('msg');
    const error = params.get('error');

    if (msg) {
        const mensajes = {
            'creado':      '¡Registro creado exitosamente!',
            'actualizado': '¡Registro actualizado correctamente!',
            'eliminado':   'Registro eliminado correctamente.',
            'venta_ok':    '¡Venta registrada exitosamente!',
        };
        Swal.fire({
            icon: 'success',
            title: '¡Listo!',
            text: mensajes[msg] || 'Operación realizada correctamente.',
            timer: 2500,
            showConfirmButton: false,
            toast: true,
            position: 'top-end',
            customClass: { popup: 'rounded-xl' }
        });
        // Limpiar URL
        const url = new URL(window.location);
        url.searchParams.delete('msg');
        window.history.replaceState({}, '', url);
    }

    if (error && error !== 'no_encontrado') {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: decodeURIComponent(error),
            confirmButtonColor: '#ed7425',
            customClass: { popup: 'rounded-xl' }
        });
        const url = new URL(window.location);
        url.searchParams.delete('error');
        window.history.replaceState({}, '', url);
    }
});

/* ─── Confirmar eliminación con SweetAlert2 ─── */
function confirmarEliminar(url, nombre = 'este registro') {
    Swal.fire({
        title: '¿Eliminar registro?',
        html: `Se eliminará <strong>${nombre}</strong>.<br>Esta acción no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        customClass: {
            popup: 'rounded-2xl',
            confirmButton: 'rounded-lg',
            cancelButton: 'rounded-lg',
        }
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
}

/* ─── Búsqueda en tablas (filtro local) ─── */
function filtrarTabla(inputId, tablaId) {
    const input = document.getElementById(inputId);
    const tabla = document.getElementById(tablaId);
    if (!input || !tabla) return;

    input.addEventListener('input', function () {
        const filtro = this.value.toLowerCase();
        const filas = tabla.querySelectorAll('tbody tr');
        let visibles = 0;

        filas.forEach(fila => {
            const texto = fila.textContent.toLowerCase();
            const mostrar = texto.includes(filtro);
            fila.style.display = mostrar ? '' : 'none';
            if (mostrar) visibles++;
        });

        // Mostrar/ocultar mensaje vacío
        const emptyMsg = tabla.querySelector('.empty-row');
        if (emptyMsg) {
            emptyMsg.style.display = visibles === 0 ? '' : 'none';
        }
    });
}

/* ─── Formato de moneda ─── */
function formatMoney(num) {
    return '$' + parseFloat(num).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

/* ─── Toast rápido ─── */
function showToast(msg, icon = 'success') {
    Swal.fire({
        icon: icon,
        title: msg,
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 2000,
        customClass: { popup: 'rounded-xl' }
    });
}
