<?php
/**
 * Header superior - visible en móvil, título + botón hamburguesa.
 */
?>
<header class="lg:hidden fixed top-0 left-0 right-0 z-20 bg-white/80 backdrop-blur-xl border-b border-gray-200/50 px-4 py-3 flex items-center justify-between">
    <button onclick="toggleSidebar()" class="p-2 rounded-lg hover:bg-gray-100 transition-colors">
        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>
    <div class="flex items-center gap-2">
        <div class="w-8 h-8 rounded-lg bg-white border border-gray-200 flex items-center justify-center overflow-hidden shadow-sm">
            <img src="src/assets/img/Captura de pantalla 2026-05-24 232344 (1).png" alt="IPESA Pinturas" class="w-full h-full object-contain">
        </div>
        <span class="font-display font-bold text-sm">IPESA Pinturas</span>
    </div>
    <div class="w-10"></div>
</header>
