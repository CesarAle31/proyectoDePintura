<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo ?? 'ColorMax POS') ?> — ColorMax POS</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50:  '#fef7ee',
                            100: '#fdecd8',
                            200: '#fad5b0',
                            300: '#f6b77e',
                            400: '#f1914a',
                            500: '#ed7425',
                            600: '#de5a1b',
                            700: '#b84318',
                            800: '#93361b',
                            900: '#772f19',
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

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="src/css/estilos.css">
</head>
<body class="bg-gray-50 font-sans text-gray-800 antialiased">

    <div class="flex min-h-screen">

        <!-- ══════ SIDEBAR ══════ -->
        <?php require __DIR__ . '/sidebar.php'; ?>

        <!-- ══════ CONTENIDO PRINCIPAL ══════ -->
        <div class="flex-1 flex flex-col ml-0 lg:ml-64 transition-all duration-300">

            <!-- Header -->
            <?php require __DIR__ . '/header.php'; ?>

            <!-- Área de contenido -->
            <main class="flex-1 p-4 lg:p-8 pt-20 lg:pt-8">

                <!-- Mensajes flash -->
                <?php if (isset($_GET['msg'])): ?>
                <div id="flash-msg" class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 flex items-center gap-3 animate-slide-down">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="font-medium">
                        <?php
                            $msgs = ['creado'=>'Registro creado correctamente','actualizado'=>'Registro actualizado','eliminado'=>'Registro eliminado'];
                            echo $msgs[$_GET['msg']] ?? 'Operación exitosa';
                        ?>
                    </span>
                </div>
                <?php endif; ?>

                <?php if (isset($_GET['error'])): ?>
                <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 flex items-center gap-3 animate-slide-down">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="font-medium"><?= htmlspecialchars(urldecode($_GET['error'])) ?></span>
                </div>
                <?php endif; ?>

                <!-- Vista actual -->
                <?php
                    if (isset($contenidoVista) && file_exists($contenidoVista)) {
                        require $contenidoVista;
                    } else {
                        echo '<p class="text-gray-500">Vista no encontrada.</p>';
                    }
                ?>
            </main>

            <!-- Footer -->
            <footer class="p-4 text-center text-xs text-gray-400 border-t border-gray-100">
                ColorMax POS &copy; <?= date('Y') ?> — Sistema de Punto de Venta para Pinturas
            </footer>
        </div>
    </div>

    <!-- Scripts -->
    <script src="src/js/app.js"></script>

    <script>
        // Auto-ocultar mensajes flash
        const flash = document.getElementById('flash-msg');
        if (flash) setTimeout(() => flash.style.display = 'none', 4000);
    </script>
</body>
</html>
