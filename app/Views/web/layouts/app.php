<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'KishalaTrans') ?></title>
    <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('img/favicon-32x32.png') ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('img/favicon-16x16.png') ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= base_url('img/apple-icon-180x180.png') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="preload" href="<?= base_url('fonts/material-symbols-outlined.ttf') ?>" as="font" type="font/ttf" crossorigin>
    <link rel="stylesheet" href="<?= base_url('css/material-symbols.css') ?>">
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        background: '#f6f7f9',
                        'on-background': '#111827',
                        surface: '#ffffff',
                        'surface-bright': '#ffffff',
                        'surface-dim': '#e5e7eb',
                        'surface-container-lowest': '#ffffff',
                        'surface-container-low': '#f8fafc',
                        'surface-container': '#f1f5f9',
                        'surface-container-high': '#e2e8f0',
                        'surface-container-highest': '#cbd5e1',
                        'surface-variant': '#e2e8f0',
                        'on-surface': '#111827',
                        'on-surface-variant': '#475569',
                        outline: '#64748b',
                        'outline-variant': '#d0d7de',
                        primary: '#1d4ed8',
                        'on-primary': '#ffffff',
                        'primary-container': '#dbeafe',
                        'on-primary-container': '#1e3a8a',
                        secondary: '#334155',
                        'on-secondary': '#ffffff',
                        'secondary-container': '#e2e8f0',
                        'on-secondary-container': '#1f2937',
                        tertiary: '#475569',
                        'on-tertiary': '#ffffff',
                        'tertiary-container': '#f1f5f9',
                        'on-tertiary-container': '#334155',
                        error: '#b91c1c',
                        'on-error': '#ffffff',
                        'error-container': '#fee2e2',
                        'on-error-container': '#991b1b',
                        'inverse-surface': '#111827',
                        'inverse-on-surface': '#f8fafc',
                        'inverse-primary': '#93c5fd',
                        'surface-tint': '#1d4ed8',
                        'primary-fixed': '#dbeafe',
                        'primary-fixed-dim': '#bfdbfe',
                        'on-primary-fixed': '#1e3a8a',
                        'on-primary-fixed-variant': '#1d4ed8',
                        'secondary-fixed': '#e2e8f0',
                        'secondary-fixed-dim': '#cbd5e1',
                        'on-secondary-fixed': '#111827',
                        'on-secondary-fixed-variant': '#334155',
                        'tertiary-fixed': '#e2e8f0',
                        'tertiary-fixed-dim': '#cbd5e1',
                        'on-tertiary-fixed': '#111827',
                        'on-tertiary-fixed-variant': '#334155'
                    },
                    borderRadius: {
                        DEFAULT: '0.375rem',
                        lg: '0.5rem',
                        xl: '0.75rem',
                        full: '9999px'
                    },
                    spacing: {
                        base: '4px',
                        xs: '4px',
                        sm: '8px',
                        md: '16px',
                        lg: '24px',
                        xl: '32px',
                        gutter: '16px',
                        margin: '24px'
                    },
                    fontFamily: {
                        body: ['Inter', 'sans-serif'],
                        label: ['Inter', 'sans-serif'],
                        display: ['Inter', 'sans-serif'],
                        headline: ['Inter', 'sans-serif'],
                        h1: ['Inter', 'sans-serif'],
                        h2: ['Inter', 'sans-serif'],
                        'body-md': ['Inter', 'sans-serif'],
                        'body-sm': ['Inter', 'sans-serif'],
                        'label-caps': ['Inter', 'sans-serif'],
                        'status-badge': ['Inter', 'sans-serif']
                    },
                    fontSize: {
                        h1: ['24px', { lineHeight: '32px', letterSpacing: '0', fontWeight: '700' }],
                        h2: ['18px', { lineHeight: '28px', letterSpacing: '0', fontWeight: '700' }],
                        'body-md': ['14px', { lineHeight: '20px', letterSpacing: '0', fontWeight: '400' }],
                        'body-sm': ['13px', { lineHeight: '18px', letterSpacing: '0', fontWeight: '400' }],
                        'label-caps': ['12px', { lineHeight: '16px', letterSpacing: '0', fontWeight: '700' }],
                        'status-badge': ['12px', { lineHeight: '16px', letterSpacing: '0', fontWeight: '700' }]
                    },
                    boxShadow: {
                        soft: '0 1px 2px rgba(15, 23, 42, 0.06)',
                        raised: '0 10px 28px rgba(15, 23, 42, 0.08)'
                    }
                }
            }
        };
    </script>
    <link rel="stylesheet" href="<?= base_url('css/design-system.css') ?>">
    <?= $this->renderSection('styles') ?>
</head>
<body class="bg-background text-on-surface font-body antialiased">
    <?php
    $currentPath = trim(uri_string(), '/');
    $isPlanning = $currentPath === 'planification' || str_starts_with($currentPath, 'planification/');
    $isReservations = $currentPath === 'reservations' || str_starts_with($currentPath, 'reservations/');
    $isReports = $currentPath === 'rapports' || str_starts_with($currentPath, 'rapports/');
    $user = session()->get('user') ?? [];
    $displayName = trim((string) (($user['prenom'] ?? '') ?: ($user['username'] ?? 'Admin')));
    ?>

    <?php if (session()->get('access_token')): ?>
        <header class="fixed top-0 left-0 w-full h-[48px] z-50 flex items-center px-gutter justify-between bg-surface/95 border-b border-outline-variant shadow-soft">
            <div class="flex items-center gap-md min-w-0">
                <button
                    id="mobile-menu-btn"
                    class="lg:hidden inline-flex h-9 w-9 items-center justify-center rounded-lg text-on-surface-variant hover:bg-surface-container focus-visible:ring-2 focus-visible:ring-primary/30"
                    type="button"
                    aria-label="Ouvrir le menu"
                    aria-controls="mobile-drawer"
                    aria-expanded="false"
                >
                    <span class="material-symbols-outlined text-[22px]">menu</span>
                </button>

                <a class="flex items-center gap-sm min-w-0" href="<?= base_url('reservations') ?>" aria-label="Accueil KishalaTrans">
                    <span class="hidden lg:grid h-8 w-8 place-items-center rounded-lg bg-primary-container text-primary">
                        <span class="material-symbols-outlined text-[20px]" aria-hidden="true">directions_bus</span>
                    </span>
                    <span class="font-h2 text-h2 font-bold text-on-surface truncate">KishalaTrans</span>
                </a>

                <nav class="hidden md:flex items-center ml-lg gap-xs h-[48px]" aria-label="Navigation principale">
                    <a class="<?= $isReservations ? 'text-primary bg-primary-container/70 font-bold' : 'text-on-surface-variant hover:bg-surface-container hover:text-on-surface' ?> h-9 px-sm rounded-lg flex items-center transition-colors" href="<?= base_url('reservations') ?>">Réservations</a>
                    <a class="<?= $isPlanning ? 'text-primary bg-primary-container/70 font-bold' : 'text-on-surface-variant hover:bg-surface-container hover:text-on-surface' ?> h-9 px-sm rounded-lg flex items-center transition-colors" href="<?= base_url('planification') ?>">Planification</a>
                    <a class="<?= $isReports ? 'text-primary bg-primary-container/70 font-bold' : 'text-on-surface-variant hover:bg-surface-container hover:text-on-surface' ?> h-9 px-sm rounded-lg flex items-center transition-colors" href="<?= base_url('rapports') ?>">Rapports</a>
                </nav>
            </div>

            <div class="flex items-center gap-sm">
                <div class="hidden sm:flex items-center gap-sm border-l border-outline-variant pl-md">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-secondary-container text-secondary font-bold text-body-sm">
                        <?= esc(strtoupper(substr($displayName, 0, 1))) ?>
                    </span>
                    <span class="text-body-sm font-medium text-on-surface-variant max-w-[140px] truncate"><?= esc($displayName) ?></span>
                </div>
                <a
                    href="<?= base_url('logout') ?>"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-on-surface-variant hover:bg-error-container hover:text-error transition-colors"
                    title="Déconnexion"
                    aria-label="Déconnexion"
                >
                    <span class="material-symbols-outlined text-[20px]" aria-hidden="true">logout</span>
                </a>
            </div>
        </header>
    <?php endif; ?>

    <div class="app-content-wrapper">
        <?php if (session()->getFlashdata('error')): ?>
            <div class="card mb-4 mx-gutter mt-gutter bg-red-50 border-red-200 text-red-800" role="alert">
                <?= esc(session()->getFlashdata('error')) ?>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('success')): ?>
            <div class="card mb-4 mx-gutter mt-gutter bg-emerald-50 border-emerald-200 text-emerald-800" role="status">
                <?= esc(session()->getFlashdata('success')) ?>
            </div>
        <?php endif; ?>

        <?= $this->renderSection('content') ?>
    </div>

    <div id="drawer-backdrop" class="fixed inset-0 z-[55] hidden bg-slate-950/35 lg:hidden" aria-hidden="true"></div>
    <aside
        id="mobile-drawer"
        class="fixed top-0 left-0 h-full z-[60] py-lg px-md w-80 max-w-[88vw] bg-surface border-r border-outline-variant shadow-raised transform -translate-x-full lg:hidden transition-transform duration-300"
        aria-label="Menu mobile"
        aria-hidden="true"
    >
        <div class="flex items-center justify-between mb-lg">
            <div class="flex items-center gap-md min-w-0">
                <span class="grid h-10 w-10 place-items-center rounded-xl bg-primary-container text-primary">
                    <span class="material-symbols-outlined text-[22px]" aria-hidden="true">directions_bus</span>
                </span>
                <div class="min-w-0">
                    <h3 class="font-h2 text-h2 font-black text-on-surface truncate">KishalaTrans</h3>
                    <p class="text-body-sm text-on-surface-variant">Agence de transport</p>
                </div>
            </div>
            <button
                id="close-drawer-btn"
                class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-on-surface-variant hover:bg-surface-container"
                type="button"
                aria-label="Fermer le menu"
            >
                <span class="material-symbols-outlined text-[22px]" aria-hidden="true">close</span>
            </button>
        </div>
        <nav class="space-y-sm" aria-label="Navigation mobile">
            <a class="flex items-center gap-md p-md <?= $isReservations ? 'bg-primary-container text-on-primary-container font-bold' : 'text-on-surface-variant hover:bg-surface-container hover:text-on-surface' ?> rounded-xl transition-colors" href="<?= base_url('reservations') ?>">
                <span class="material-symbols-outlined" aria-hidden="true">book_online</span>
                <span class="font-body-md">Réservations</span>
            </a>
            <a class="flex items-center gap-md p-md <?= $isPlanning ? 'bg-primary-container text-on-primary-container font-bold' : 'text-on-surface-variant hover:bg-surface-container hover:text-on-surface' ?> rounded-xl transition-colors" href="<?= base_url('planification') ?>">
                <span class="material-symbols-outlined" aria-hidden="true">event_available</span>
                <span class="font-body-md">Planification</span>
            </a>
            <a class="flex items-center gap-md p-md <?= $isReports ? 'bg-primary-container text-on-primary-container font-bold' : 'text-on-surface-variant hover:bg-surface-container hover:text-on-surface' ?> rounded-xl transition-colors" href="<?= base_url('rapports') ?>">
                <span class="material-symbols-outlined" aria-hidden="true">bar_chart</span>
                <span class="font-body-md">Rapports</span>
            </a>
        </nav>
    </aside>

    <?= $this->renderSection('scripts') ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const mobileBtn = document.getElementById('mobile-menu-btn');
            const drawer = document.getElementById('mobile-drawer');
            const closeBtn = document.getElementById('close-drawer-btn');
            const backdrop = document.getElementById('drawer-backdrop');

            const openDrawer = () => {
                if (!drawer) return;
                drawer.classList.remove('-translate-x-full');
                drawer.setAttribute('aria-hidden', 'false');
                mobileBtn?.setAttribute('aria-expanded', 'true');
                backdrop?.classList.remove('hidden');
                closeBtn?.focus();
            };

            const closeDrawer = () => {
                if (!drawer) return;
                drawer.classList.add('-translate-x-full');
                drawer.setAttribute('aria-hidden', 'true');
                mobileBtn?.setAttribute('aria-expanded', 'false');
                backdrop?.classList.add('hidden');
                mobileBtn?.focus();
            };

            mobileBtn?.addEventListener('click', openDrawer);
            closeBtn?.addEventListener('click', closeDrawer);
            backdrop?.addEventListener('click', closeDrawer);
            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && drawer && !drawer.classList.contains('-translate-x-full')) {
                    closeDrawer();
                }
            });
        });
    </script>
</body>
</html>
