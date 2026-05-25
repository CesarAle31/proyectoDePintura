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
        <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center">
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
            </svg>
        </div>
        <span class="font-display font-bold text-sm">ColorMax</span>
    </div>
    <div class="w-10"></div>
</header>
