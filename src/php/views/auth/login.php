<?php
/**
 * Vista de login — página independiente, sin layout principal.
 * Variables disponibles: $error (string|null), $usuarioPrevio (string).
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión — Ipesa Pinturas</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            400: '#fb7185',
                            500: '#e51e25',
                            600: '#be1219',
                            700: '#9f1239',
                        },
                        slate_dark: {
                            800: '#1a1f2e',
                            900: '#111525',
                            950: '#0c0f1a',
                        }
                    },
                    fontFamily: {
                        sans: ['DM Sans', 'sans-serif'],
                        display: ['Playfair Display', 'serif'],
                    }
                }
            }
        }
    </script>
</head>
<body class="min-h-screen font-sans antialiased bg-gradient-to-br from-slate_dark-900 via-slate_dark-800 to-slate_dark-950 flex items-center justify-center px-4">

    <div class="w-full max-w-md">

        <!-- Logo y marca -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-28 h-28 rounded-2xl bg-white shadow-xl shadow-brand-500/30 mb-4 p-2">
                <img src="src/assets/img/Captura de pantalla 2026-05-24 232344 (1).png" alt="IPESA Pinturas" class="w-full h-full object-contain rounded-xl">
            </div>
            <h1 class="font-display text-3xl font-bold text-white tracking-tight">IPESA Pinturas</h1>
            <p class="text-sm text-gray-400 mt-1 uppercase tracking-[0.25em]">Punto de Venta</p>
        </div>

        <!-- Tarjeta del formulario -->
        <div class="bg-white rounded-2xl shadow-2xl p-8">
            <h2 class="text-xl font-semibold text-slate_dark-900 mb-1">Iniciar sesión</h2>
            <p class="text-sm text-gray-500 mb-6">Ingresa con tus credenciales para continuar.</p>

            <?php if (!empty($error)): ?>
                <div class="mb-5 p-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm flex items-start gap-2">
                    <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span><?= htmlspecialchars(urldecode($error)) ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="index.php?page=login&action=authenticate" class="space-y-4" autocomplete="off">
                <div>
                    <label for="usuario" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                        Usuario
                    </label>
                    <div class="relative">
                        <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <input type="text" id="usuario" name="usuario" required autofocus
                               value="<?= htmlspecialchars($usuarioPrevio ?? '') ?>"
                               class="w-full pl-10 pr-3 py-2.5 rounded-lg border border-gray-300 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition"
                               placeholder="ej. cesar">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                        Contraseña
                    </label>
                    <div class="relative">
                        <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 11c0-1.104.896-2 2-2s2 .896 2 2-2 4-2 4m-6-4a6 6 0 1112 0c0 1.657-.672 3.157-1.757 4.243l-7.486 7.486A2 2 0 014 21v-3.586a2 2 0 01.586-1.414L8 12"/>
                        </svg>
                        <input type="password" id="password" name="password" required
                               class="w-full pl-10 pr-3 py-2.5 rounded-lg border border-gray-300 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition"
                               placeholder="••••••••">
                    </div>
                </div>

                <button type="submit"
                        class="w-full py-2.5 rounded-lg bg-gradient-to-r from-brand-500 to-brand-600 hover:from-brand-600 hover:to-brand-700 text-white font-semibold shadow-lg shadow-brand-500/25 hover:shadow-brand-500/40 transition mt-2">
                    Entrar
                </button>
            </form>
        </div>

        <p class="text-center text-xs text-gray-500 mt-6">
            &copy; <?= date('Y') ?> IPESA Pinturas — Sistema de Punto de Venta
        </p>
    </div>

</body>
</html>
