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
        @media (max-w: 767px) {
            /* 1. Reduce outer page margins and paddings */
            main.overflow-y-auto {
                padding: 10px !important;
            }
            
            /* Remove excessive padding inside subviews to prevent double padding */
            main.overflow-y-auto > main,
            main.overflow-y-auto > div {
                padding-left: 0 !important;
                padding-right: 0 !important;
                padding-bottom: 20px !important;
            }

            /* 2. Style page title headers for mobile */
            main.overflow-y-auto div[class*="h-[60px]"],
            main.overflow-y-auto div[class*="min-h-[60px]"] {
                height: auto !important;
                min-height: unset !important;
                padding: 12px 14px !important;
                margin-bottom: 12px !important;
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 10px !important;
            }

            /* Target nested sub-containers of headers */
            main.overflow-y-auto div[class*="h-[60px]"] > div,
            main.overflow-y-auto div[class*="min-h-[60px]"] > div {
                width: 100% !important;
                justify-content: space-between !important;
                height: auto !important;
            }

            /* Force buttons inside headers to be full-width on mobile */
            main.overflow-y-auto div[class*="h-[60px]"] button,
            main.overflow-y-auto div[class*="min-h-[60px]"] button,
            main.overflow-y-auto div[class*="h-[60px]"] a[class*="btn"],
            main.overflow-y-auto div[class*="min-h-[60px]"] a[class*="btn"] {
                width: 100% !important;
                justify-content: center !important;
                padding-top: 8px !important;
                padding-bottom: 8px !important;
                min-height: 40px !important;
            }

            /* 3. Optimize cards, filters, and spacing on mobile */
            .card, 
            section.bg-white,
            div.bg-white.border.border-gray-200.rounded-2xl {
                padding: 12px !important;
                margin-bottom: 12px !important;
                border-radius: 12px !important;
            }

            /* Adjust spacing inside grid filters */
            section[class*="grid-cols-"],
            div[class*="grid-cols-"] {
                gap: 8px !important;
            }

            /* Remove massive vertical spaces */
            .mb-6 { margin-bottom: 12px !important; }
            .mt-6 { margin-top: 12px !important; }
            .py-6 { padding-top: 12px !important; padding-bottom: 12px !important; }
            .px-6 { padding-left: 12px !important; padding-right: 12px !important; }
            .p-6 { padding: 12px !important; }
            .p-5 { padding: 12px !important; }

            /* 4. Fix table overflows and text cuts */
            table {
                font-size: 12px !important;
            }
            
            th, td {
                padding-left: 10px !important;
                padding-right: 10px !important;
                padding-top: 12px !important;
                padding-bottom: 12px !important;
                white-space: nowrap !important; /* Keep table cells clean and scrollable */
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
