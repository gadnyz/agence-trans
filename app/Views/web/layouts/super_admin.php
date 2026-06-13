<!DOCTYPE html>
<html lang="fr" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Kashala Trans — Dispatch Hub') ?></title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "surface": "#f8f9fb",
                        "surface-dim": "#d9dadc",
                        "surface-bright": "#f8f9fb",
                        "surface-container-lowest": "#ffffff",
                        "surface-container-low": "#f2f4f6",
                        "surface-container": "#edeef0",
                        "surface-container-high": "#e7e8ea",
                        "surface-container-highest": "#e1e2e4",
                        "on-surface": "#191c1e",
                        "on-surface-variant": "#434655",
                        "inverse-surface": "#2e3132",
                        "inverse-on-surface": "#f0f1f3",
                        "outline": "#747686",
                        "outline-variant": "#c4c5d7",
                        "surface-tint": "#2151da",
                        "primary": "#0037b0",
                        "on-primary": "#ffffff",
                        "primary-container": "#1d4ed8",
                        "on-primary-container": "#cad3ff",
                        "inverse-primary": "#b7c4ff",
                        "secondary": "#515f74",
                        "on-secondary": "#ffffff",
                        "secondary-container": "#d5e3fd",
                        "on-secondary-container": "#57657b",
                        "tertiary": "#374559",
                        "on-tertiary": "#ffffff",
                        "tertiary-container": "#4f5d71",
                        "error": "#ba1a1a",
                        "on-error": "#ffffff",
                        "error-container": "#ffdad6",
                        "on-error-container": "#410002"
                    },
                    spacing: { base: "4px", xs: "4px", sm: "8px", md: "16px", lg: "24px", xl: "32px", gutter: "16px", margin: "24px" },
                    borderRadius: { DEFAULT: "0.375rem", lg: "0.5rem", xl: "0.75rem", full: "9999px" },
                    fontFamily: { sans: ["Inter", "sans-serif"] }
                }
            }
        };
    </script>
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
    </style>
    <?= $this->renderSection('styles') ?>
</head>
<body class="bg-surface text-on-surface antialiased overflow-x-hidden">

    <?= $this->include('web/components/sidebar') ?>

    <div id="drawer-backdrop" class="fixed inset-0 z-40 hidden bg-on-surface/40 backdrop-blur-sm lg:hidden transition-opacity" aria-hidden="true"></div>

    <div class="lg:pl-[72px] flex flex-col min-h-screen">
        
        <?= $this->include('web/components/header_top') ?>

        <?= $this->include('web/components/flash_messages') ?>

        <main class="flex-1 p-margin">
            <?= $this->renderSection('content') ?>
        </main>
    </div>

    <?= $this->renderSection('scripts') ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const mobileBtn = document.getElementById('mobile-menu-btn');
            const sidebar = document.getElementById('main-sidebar');
            const backdrop = document.getElementById('drawer-backdrop');

            const toggleDrawer = (open) => {
                if (!sidebar) return;
                sidebar.classList.toggle('-translate-x-full', !open);
                mobileBtn?.setAttribute('aria-expanded', open ? 'true' : 'false');
                backdrop?.classList.toggle('hidden', !open);
            };

            mobileBtn?.addEventListener('click', () => toggleDrawer(true));
            backdrop?.addEventListener('click', () => toggleDrawer(false));
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && !sidebar?.classList.contains('-translate-x-full')) toggleDrawer(false);
            });
        });
    </script>
</body>
</html>