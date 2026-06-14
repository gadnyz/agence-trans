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
    </style>

    <?= $this->renderSection('styles') ?>
</head>
<body class="bg-surface text-on-surface antialiased overflow-x-hidden">

    <div class="flex min-h-screen">

        <?= $this->include('web/components/sidebar') ?>

        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

            <!-- Header -->
            <?= $this->include('web/components/header_top') ?>

            <!-- Flash messages -->
            <?= $this->include('web/components/flash_messages') ?>

            <!-- Contenu principal -->
            <main class="flex-1 p-gutter md:p-margin overflow-y-auto">
                <?= $this->renderSection('content') ?>
            </main>

        </div>
    </div>

    <?= $this->renderSection('scripts') ?>
    <script>
    // ── Sidebar collapse ──
    (function () {
        const sidebar   = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('toggle-sidebar');
        const KEY       = 'kt_sidebar_collapsed';
        if (!sidebar || !toggleBtn) return;
        if (localStorage.getItem(KEY) === '1') sidebar.classList.add('collapsed');
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            localStorage.setItem(KEY, sidebar.classList.contains('collapsed') ? '1' : '0');
        });
    })();
    </script>
</body>
</html>
