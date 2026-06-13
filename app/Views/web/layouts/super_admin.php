<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Kashala Trans — Super Admin') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        background: '#f6f7f9',
                        'on-background': '#111827',
                        surface: '#ffffff',
                        'surface-container-low': '#f8fafc',
                        'surface-container': '#f1f5f9',
                        'on-surface': '#111827',
                        'on-surface-variant': '#475569',
                        'outline-variant': '#d0d7de',
                        primary: '#1d4ed8',
                        'primary-container': '#dbeafe',
                        'on-primary-container': '#1e3a8a',
                        secondary: '#334155',
                        'secondary-container': '#e2e8f0',
                        error: '#b91c1c',
                        'error-container': '#fee2e2',
                        'on-error-container': '#991b1b',
                    },
                    spacing: { md: '16px', lg: '24px', gutter: '16px', margin: '24px' }
                }
            }
        };
    </script>
    <?= $this->renderSection('styles') ?>
</head>
<body class="bg-background text-on-surface font-sans antialiased">

    <?= $this->include('web/components/header_mobile') ?>

    <?= $this->include('web/components/sidebar') ?>

    <div id="drawer-backdrop" class="fixed inset-0 z-40 hidden bg-slate-950/35 lg:hidden" aria-hidden="true"></div>

    <div class="lg:ml-64 pt-[48px] lg:pt-0 min-h-screen flex flex-col">
        
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
            const closeBtn = document.getElementById('close-drawer-btn');
            const backdrop = document.getElementById('drawer-backdrop');

            const toggleDrawer = (open) => {
                if (!sidebar) return;
                sidebar.classList.toggle('-translate-x-full', !open);
                mobileBtn?.setAttribute('aria-expanded', open ? 'true' : 'false');
                backdrop?.classList.toggle('hidden', !open);
                if (open) closeBtn?.focus();
            };

            mobileBtn?.addEventListener('click', () => toggleDrawer(true));
            closeBtn?.addEventListener('click', () => toggleDrawer(false));
            backdrop?.addEventListener('click', () => toggleDrawer(false));
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && !sidebar?.classList.contains('-translate-x-full')) toggleDrawer(false);
            });
        });
    </script>
</body>
</html>