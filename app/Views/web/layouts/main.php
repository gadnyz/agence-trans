<?php
/**
 * Layout principal partagé par tous les rôles.
 * Les layouts spécifiques (admin.php, recept.php, driver.php) étendent celui-ci
 * en surchargeant la section 'layout_body' pour injecter sidebar + header adaptés.
 *
 * Variables attendues :
 *   $title     (string) — titre de la page
 *   $pageTitle (string) — titre affiché dans le header
 */
$user        = session()->get('user') ?? [];
$userRole    = $user['role']['code'] ?? '';
$displayName = trim(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? '')) ?: ($user['username'] ?? 'Utilisateur');
$roleLabel   = $user['role']['libelle'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Kashala Trans') ?></title>

    <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('img/favicon-32x32.png') ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('img/favicon-16x16.png') ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= base_url('img/apple-icon-180x180.png') ?>">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet">

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        // Surfaces
                        'surface':                    '#f8f9fb',
                        'surface-dim':                '#d9dadc',
                        'surface-container-lowest':   '#ffffff',
                        'surface-container-low':      '#f2f4f6',
                        'surface-container':          '#edeef0',
                        'surface-container-high':     '#e7e8ea',
                        'surface-container-highest':  '#e1e2e4',
                        'on-surface':                 '#191c1e',
                        'on-surface-variant':         '#434655',
                        'inverse-surface':            '#2e3132',
                        'inverse-on-surface':         '#f0f1f3',
                        'outline':                    '#747686',
                        'outline-variant':            '#c4c5d7',
                        // Primary (bleu — surcharge possible par rôle via CSS var)
                        'primary':                    'var(--color-primary, #0037b0)',
                        'on-primary':                 '#ffffff',
                        'primary-container':          'var(--color-primary-container, #dde3ff)',
                        'on-primary-container':       'var(--color-on-primary-container, #001258)',
                        'primary-fixed':              'var(--color-primary-fixed, #dde3ff)',
                        'on-primary-fixed':           'var(--color-on-primary-fixed, #001258)',
                        'on-primary-fixed-variant':   'var(--color-on-primary-fixed-variant, #0037b0)',
                        // Secondary
                        'secondary':                  '#515f74',
                        'on-secondary':               '#ffffff',
                        'secondary-container':        '#d5e3fd',
                        'on-secondary-container':     '#57657b',
                        'secondary-fixed':            '#d5e3fd',
                        'on-secondary-fixed':         '#0d1d2d',
                        'on-secondary-fixed-variant': '#515f74',
                        // Tertiary
                        'tertiary':                   '#374559',
                        'on-tertiary':                '#ffffff',
                        'tertiary-container':         '#4f5d71',
                        'tertiary-fixed':             '#dce4f5',
                        'on-tertiary-fixed':          '#111c2b',
                        'on-tertiary-fixed-variant':  '#374559',
                        // Error
                        'error':                      '#ba1a1a',
                        'on-error':                   '#ffffff',
                        'error-container':            '#ffdad6',
                        'on-error-container':         '#410002',
                    },
                    spacing: {
                        xs: '4px', sm: '8px', md: '16px',
                        lg: '24px', xl: '32px',
                        gutter: '16px', margin: '24px',
                    },
                    borderRadius: { DEFAULT: '0.375rem', lg: '0.5rem', xl: '0.75rem', full: '9999px' },
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    fontSize: {
                        'label-sm':   ['11px', { lineHeight: '16px', fontWeight: '500' }],
                        'label-lg':   ['14px', { lineHeight: '20px', fontWeight: '500' }],
                        'body-sm':    ['13px', { lineHeight: '18px', fontWeight: '400' }],
                        'body-md':    ['14px', { lineHeight: '20px', fontWeight: '400' }],
                        'title-sm':   ['14px', { lineHeight: '20px', fontWeight: '600' }],
                        'title-md':   ['16px', { lineHeight: '24px', fontWeight: '600' }],
                        'title-lg':   ['18px', { lineHeight: '28px', fontWeight: '700' }],
                        'headline-md':['24px', { lineHeight: '32px', fontWeight: '700' }],
                        'headline-lg':['28px', { lineHeight: '36px', fontWeight: '700' }],
                    },
                }
            }
        };
    </script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            user-select: none;
        }
        /* Sidebar collapse */
        #sidebar.collapsed { width: 72px; }
        #sidebar.collapsed .sidebar-label { display: none; }
        #sidebar.collapsed .sidebar-logo-text { display: none; }
        /* Scrollbar thin */
        .hide-scrollbar::-webkit-scrollbar { width: 4px; }
        .hide-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .hide-scrollbar::-webkit-scrollbar-thumb { background: #c4c5d7; border-radius: 4px; }

        /* 📱 Responsive overrides for Android / Mobile Devices */
        @media (max-width: 767px) {

            /* ── 1. Global spacing: max 16px (px-4) everywhere ── */
            main.overflow-y-auto {
                padding: 0 !important;
            }

            /* Inner page wrappers from views (main.px-4 or div.px-4) */
            main.overflow-y-auto > main,
            main.overflow-y-auto > div {
                padding-left: 16px !important;
                padding-right: 16px !important;
                padding-top: 0 !important;
                padding-bottom: 24px !important;
            }

            /* ── 2. Sticky sub-bar headers with glassmorphism ── */
            .min-h-\[60px\],
            .h-\[60px\] {
                position: sticky !important;
                top: 0 !important;
                z-index: 30 !important;
                height: auto !important;
                min-height: unset !important;
                background: rgba(255, 255, 255, 0.92) !important;
                backdrop-filter: saturate(180%) blur(16px) !important;
                -webkit-backdrop-filter: saturate(180%) blur(16px) !important;
                border: none !important;
                border-bottom: 1px solid rgba(229, 231, 235, 0.7) !important;
                border-radius: 0 !important;
                margin-left: -16px !important;
                margin-right: -16px !important;
                margin-top: 0 !important;
                margin-bottom: 16px !important;
                padding: 12px 16px !important;
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 10px !important;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06), 0 1px 2px rgba(0, 0, 0, 0.04) !important;
                transition: box-shadow 0.2s ease !important;
            }

            /* Nested div containers inside sub-bar */
            .min-h-\[60px\] > div,
            .h-\[60px\] > div {
                width: 100% !important;
                justify-content: space-between !important;
                height: auto !important;
            }

            /* ── 3. Horizontal scroll-x for filter forms ── */
            .min-h-\[60px\] form,
            .h-\[60px\] form {
                display: flex !important;
                flex-direction: row !important;
                flex-wrap: nowrap !important;
                overflow-x: auto !important;
                overflow-y: hidden !important;
                -webkit-overflow-scrolling: touch;
                gap: 8px !important;
                width: 100% !important;
                padding: 6px 0 6px !important;
                align-items: center !important;
                -ms-overflow-style: none !important;
                scrollbar-width: none !important;
            }
            .min-h-\[60px\] form::-webkit-scrollbar,
            .h-\[60px\] form::-webkit-scrollbar {
                display: none !important;
            }
            /* Prevent form children from shrinking */
            .min-h-\[60px\] form > *,
            .h-\[60px\] form > * {
                flex-shrink: 0 !important;
            }
            /* Compact form inputs inside filter bars */
            .min-h-\[60px\] form input,
            .min-h-\[60px\] form select,
            .h-\[60px\] form input,
            .h-\[60px\] form select {
                min-width: 130px !important;
                max-width: 170px !important;
                font-size: 13px !important;
            }

            /* Direct action buttons (not inside forms) → full-width */
            .min-h-\[60px\] > button,
            .h-\[60px\] > button,
            .min-h-\[60px\] > a,
            .h-\[60px\] > a {
                width: 100% !important;
                justify-content: center !important;
                padding: 8px 16px !important;
                min-height: 40px !important;
            }

            /* ── 4. KPI card grids: horizontal scroll on mobile ── */
            section.grid[class*="grid-cols-"],
            section[class*="grid-cols-1"][class*="sm:grid-cols-2"] {
                display: flex !important;
                flex-wrap: nowrap !important;
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch;
                gap: 12px !important;
                padding-bottom: 4px !important;
                scroll-snap-type: x mandatory;
                -ms-overflow-style: none !important;
                scrollbar-width: none !important;
            }
            section.grid[class*="grid-cols-"]::-webkit-scrollbar,
            section[class*="grid-cols-1"][class*="sm:grid-cols-2"]::-webkit-scrollbar {
                display: none !important;
            }
            section.grid[class*="grid-cols-"] > div,
            section[class*="grid-cols-1"][class*="sm:grid-cols-2"] > div {
                min-width: 200px !important;
                max-width: 220px !important;
                flex-shrink: 0 !important;
                scroll-snap-align: start;
            }

            /* ── 5. Cards, sections, spacing ── */
            .card,
            .report-card {
                padding: 16px !important;
                margin-bottom: 16px !important;
                border-radius: 12px !important;
            }

            section.bg-white,
            div.bg-white.border.rounded-2xl,
            div.bg-white.border.border-gray-200.rounded-2xl {
                border-radius: 12px !important;
                overflow: hidden;
            }

            /* Spacing utilities override: clamp to 16px max */
            .mb-6 { margin-bottom: 16px !important; }
            .mt-6 { margin-top: 16px !important; }
            .py-6 { padding-top: 16px !important; padding-bottom: 16px !important; }
            .px-6 { padding-left: 16px !important; padding-right: 16px !important; }
            .p-6  { padding: 16px !important; }
            .p-5  { padding: 16px !important; }
            .gap-6 { gap: 16px !important; }
            .space-y-6 > * + * { margin-top: 16px !important; }

            /* ── 6. Table horizontal scroll ── */
            .overflow-x-auto {
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch;
                width: 100% !important;
            }
            table {
                font-size: 12px !important;
            }
            th, td {
                padding: 10px !important;
                white-space: nowrap !important;
            }

            /* Table section headers: compact on mobile */
            section .px-6.py-4,
            div .px-6.py-4 {
                padding: 12px 16px !important;
            }
            section .px-6.py-4 h3,
            div .px-6.py-4 h3 {
                font-size: 14px !important;
            }

            /* Pagination: compact */
            nav[aria-label="Pagination"] {
                padding: 12px 16px !important;
                flex-direction: column !important;
                gap: 12px !important;
            }

            /* ── 7. FullCalendar responsive ── */
            .fc .fc-toolbar {
                flex-direction: column !important;
                gap: 10px !important;
                align-items: stretch !important;
            }
            .fc .fc-toolbar-chunk {
                display: flex !important;
                justify-content: center !important;
                flex-wrap: wrap !important;
                gap: 8px !important;
            }
            .fc .fc-toolbar-title {
                text-align: center !important;
                font-size: 1.125rem !important;
            }
            .fc .p-6 {
                padding: 8px !important;
            }
        }
    </style>

    <?= $this->renderSection('styles') ?>
</head>
<body class="bg-surface text-on-surface antialiased overflow-x-hidden">

    <div class="flex min-h-screen">

        <?= $this->include('web/components/sidebar') ?>
        <?= $this->include('web/components/drawer_mobile') ?>

        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

            <!-- Header Mobile -->
            <?= $this->include('web/components/header_mobile') ?>

            <!-- Header Desktop -->
            <?= $this->include('web/components/header_top') ?>

            <!-- Flash messages -->
            <?= $this->include('web/components/flash_messages') ?>

            <!-- Contenu principal -->
            <main class="flex-1 p-gutter md:p-margin overflow-y-auto">
                <?= $this->renderSection('content') ?>
            </main>

        </div>
    </div>

    <!-- Scripts for mobile navigation and profile menu toggles -->
    <script>
    (function () {
        // Mobile navigation drawer controls
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileDrawer = document.getElementById('mobile-drawer');
        const mobileDrawerClose = document.getElementById('mobile-drawer-close');
        const mobileDrawerBackdrop = document.getElementById('mobile-drawer-backdrop');

        function openDrawer() {
            if (!mobileDrawer) return;
            mobileDrawer.classList.remove('-translate-x-full');
            mobileDrawer.setAttribute('aria-hidden', 'false');
            mobileMenuBtn?.setAttribute('aria-expanded', 'true');
            if (mobileDrawerBackdrop) {
                mobileDrawerBackdrop.classList.remove('hidden');
                setTimeout(() => {
                    mobileDrawerBackdrop.classList.remove('opacity-0');
                    mobileDrawerBackdrop.classList.add('opacity-100');
                }, 10);
            }
        }

        function closeDrawer() {
            if (!mobileDrawer) return;
            mobileDrawer.classList.add('-translate-x-full');
            mobileDrawer.setAttribute('aria-hidden', 'true');
            mobileMenuBtn?.setAttribute('aria-expanded', 'false');
            if (mobileDrawerBackdrop) {
                mobileDrawerBackdrop.classList.remove('opacity-100');
                mobileDrawerBackdrop.classList.add('opacity-0');
                mobileDrawerBackdrop.addEventListener('transitionend', function handler() {
                    mobileDrawerBackdrop.classList.add('hidden');
                    mobileDrawerBackdrop.removeEventListener('transitionend', handler);
                }, { once: true });
            }
        }

        mobileMenuBtn?.addEventListener('click', openDrawer);
        mobileDrawerClose?.addEventListener('click', closeDrawer);
        mobileDrawerBackdrop?.addEventListener('click', closeDrawer);

        // Close drawer on Esc key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && mobileDrawer && !mobileDrawer.classList.contains('-translate-x-full')) {
                closeDrawer();
            }
        });

        // Mobile profile dropdown controls
        const profileBtn = document.getElementById('mobile-profile-btn');
        const profileDropdown = document.getElementById('mobile-profile-dropdown');

        if (profileBtn && profileDropdown) {
            profileBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                const isHidden = profileDropdown.classList.contains('hidden');
                if (isHidden) {
                    // Open
                    profileDropdown.classList.remove('hidden');
                    profileBtn.setAttribute('aria-expanded', 'true');
                    setTimeout(() => {
                        profileDropdown.classList.remove('scale-95', 'opacity-0');
                        profileDropdown.classList.add('scale-100', 'opacity-100');
                    }, 10);
                } else {
                    // Close
                    closeProfileDropdown();
                }
            });

            function closeProfileDropdown() {
                profileDropdown.classList.remove('scale-100', 'opacity-100');
                profileDropdown.classList.add('scale-95', 'opacity-0');
                profileBtn.setAttribute('aria-expanded', 'false');
                profileDropdown.addEventListener('transitionend', function handler() {
                    profileDropdown.classList.add('hidden');
                    profileDropdown.removeEventListener('transitionend', handler);
                }, { once: true });
            }

            // Close when clicking outside
            document.addEventListener('click', (e) => {
                if (!profileDropdown.classList.contains('hidden') && !profileBtn.contains(e.target) && !profileDropdown.contains(e.target)) {
                    closeProfileDropdown();
                }
            });
        }
    })();
    </script>

    <?= $this->renderSection('scripts') ?>
</body>
</html>
