<?php
/**
 * Mobile drawer menu dynamically matching the active user role's links.
 */
$currentPath = trim(uri_string(), '/');
$user        = session()->get('user') ?? [];
$userRole    = $user['role']['code'] ?? '';

// Helper : active class for links
$isActiveMobile = fn($path) => ($currentPath === $path || str_starts_with($currentPath, $path))
    ? 'bg-primary-container text-on-primary-container font-semibold shadow-sm'
    : 'text-on-surface-variant hover:bg-surface-container hover:text-on-surface';

// Same menus definition as sidebar
$menus = [
    'super_admin' => [
        'label' => 'Super Admin',
        'links' => [
            ['path' => 'super-admin/dashboard',     'icon' => 'dashboard',      'label' => 'Dashboard'],
            ['path' => 'super-admin/reservation',   'icon' => 'book_online',    'label' => 'Réservations'],
            ['path' => 'super-admin/planification', 'icon' => 'calendar_today', 'label' => 'Planification'],
            ['path' => 'super-admin/analyse',       'icon' => 'analytics',      'label' => 'Analyse'],
            ['path' => 'admin/bus',                 'icon' => 'directions_bus', 'label' => 'Bus'],
            ['path' => 'admin/chauffeurs',          'icon' => 'badge',          'label' => 'Chauffeurs'],
            ['path' => 'admin/trajets',             'icon' => 'route',          'label' => 'Trajets'],
            ['path' => 'super-admin/parametres',    'icon' => 'settings',       'label' => 'Paramètres'],
        ],
    ],
    'admin' => [
        'label' => 'Admin',
        'links' => [
            ['path' => 'admin/rapports',            'icon' => 'bar_chart',      'label' => 'Rapports'],
            ['path' => 'admin/planification',       'icon' => 'calendar_today', 'label' => 'Planification'],
            ['path' => 'admin/reservation',         'icon' => 'book_online',    'label' => 'Réservations'],
            ['path' => 'admin/bus',                 'icon' => 'directions_bus', 'label' => 'Bus'],
            ['path' => 'admin/chauffeurs',          'icon' => 'badge',          'label' => 'Chauffeurs'],
            ['path' => 'admin/trajets',             'icon' => 'route',          'label' => 'Trajets'],
        ],
    ],
    'recept' => [
        'label' => 'Guichet',
        'links' => [
            ['path' => 'recept/dashboard',      'icon' => 'dashboard',        'label' => 'Tableau de bord'],
            ['path' => 'recept/reservations',   'icon' => 'confirmation_number', 'label' => 'Réservations'],
            ['path' => 'recept/paiements',      'icon' => 'payments',         'label' => 'Paiements'],
            ['path' => 'recept/programmes',     'icon' => 'directions_bus',   'label' => 'Programmes'],
            ['path' => 'recept/flotte',         'icon' => 'manage_search',    'label' => 'Flotte & Réseau'],
        ],
    ],
    'driver' => [
        'label' => 'Chauffeur',
        'links' => [
            ['path' => 'driver/planning',           'icon' => 'event_note',     'label' => 'Mon Planning'],
        ],
    ],
];

$roleMenu = $menus[$userRole] ?? ['label' => '', 'links' => []];
$links    = $roleMenu['links'];
?>

<!-- Backdrop overlay for mobile drawer -->
<div id="mobile-drawer-backdrop" class="fixed inset-0 bg-slate-950/40 z-[90] hidden opacity-0 transition-opacity duration-300 md:hidden" aria-hidden="true"></div>

<!-- Sliding drawer menu panel -->
<aside
    id="mobile-drawer"
    class="fixed top-0 left-0 bottom-0 h-full z-[100] w-72 max-w-[85vw] bg-white border-r border-gray-100 shadow-2xl flex flex-col transform -translate-x-full transition-transform duration-300 ease-in-out md:hidden"
    aria-label="Menu mobile"
    aria-hidden="true"
>
    <!-- Drawer Header -->
    <div class="flex items-center justify-between px-4 h-16 border-b border-gray-100 shrink-0">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-primary rounded-xl flex items-center justify-center shrink-0 shadow-md">
                <span class="material-symbols-outlined text-on-primary text-[18px]">directions_bus</span>
            </div>
            <div>
                <h2 class="font-bold text-sm text-gray-900 leading-tight">KishalaTrans</h2>
                <?php if (!empty($roleMenu['label'])): ?>
                    <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wider"><?= esc($roleMenu['label']) ?></p>
                <?php endif; ?>
            </div>
        </div>
        <button
            id="mobile-drawer-close"
            class="w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center text-gray-500 transition-colors focus:outline-none focus:ring-2 focus:ring-primary/20"
            type="button"
            aria-label="Fermer le menu"
        >
            <span class="material-symbols-outlined text-[20px]">close</span>
        </button>
    </div>

    <!-- Drawer Navigation Links -->
    <nav class="flex-1 p-3 space-y-1 overflow-y-auto" aria-label="Navigation mobile">
        <?php foreach ($links as $link): ?>
            <a
                class="flex items-center rounded-xl transition-all duration-200 h-10 px-3 gap-4 <?= $isActiveMobile($link['path']) ?>"
                href="<?= base_url($link['path']) ?>"
                <?= ($currentPath === $link['path']) ? 'aria-current="page"' : '' ?>
            >
                <div class="w-6 h-6 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[20px]"><?= $link['icon'] ?></span>
                </div>
                <span class="text-sm font-medium"><?= esc($link['label']) ?></span>
            </a>
        <?php endforeach; ?>
    </nav>

    <!-- Drawer Footer (Logout) -->
    <!-- <div class="p-3 border-t border-gray-100 shrink-0">
        <a
            href="<?= base_url('logout') ?>"
            class="flex items-center rounded-xl transition-all duration-200 h-10 px-3 gap-4 text-on-surface-variant hover:bg-error-container hover:text-error"
            title="Déconnexion"
        >
            <div class="w-6 h-6 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[18px]">logout</span>
            </div>
            <span class="text-sm font-medium">Déconnexion</span>
        </a>
    </div> -->
</aside>
