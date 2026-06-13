<?php
/**
 * Sidebar dynamique — Liens filtrés selon le rôle de l'utilisateur connecté.
 *
 * Rôles : super_admin | admin | recept | driver
 */
$currentPath = trim(uri_string(), '/');
$user        = session()->get('user') ?? [];
$userRole    = $user['role']['code'] ?? '';

// Helper : classe active pour le lien courant
$isActive = fn($path) => ($currentPath === $path || str_starts_with($currentPath, $path))
    ? 'bg-primary-container text-on-primary-container font-bold'
    : 'text-on-surface-variant hover:bg-surface-container hover:text-on-surface';

// -------------------------------------------------------------------------
// Définition des menus par rôle
// -------------------------------------------------------------------------
$menus = [

    'super_admin' => [
        'label' => 'Super Admin',
        'links' => [
            ['path' => 'super-admin/dashboard',     'icon' => 'dashboard',      'label' => 'Dashboard'],
            ['path' => 'super-admin/reservation',   'icon' => 'book_online',    'label' => 'Réservations'],
            ['path' => 'super-admin/planification', 'icon' => 'calendar_today', 'label' => 'Planification'],
            ['path' => 'super-admin/rapports',      'icon' => 'bar_chart',      'label' => 'Rapports'],
            ['path' => 'super-admin/analyse',       'icon' => 'analytics',      'label' => 'Analyse'],
            ['path' => 'super-admin/parametres',    'icon' => 'settings',       'label' => 'Paramètres'],
        ],
    ],

    'admin' => [
        'label' => 'Admin',
        'links' => [
            ['path' => 'admin/dashboard',           'icon' => 'dashboard',      'label' => 'Dashboard'],
            ['path' => 'admin/reservation',         'icon' => 'book_online',    'label' => 'Réservations'],
            ['path' => 'admin/planification',       'icon' => 'calendar_today', 'label' => 'Planification'],
            ['path' => 'admin/rapports',            'icon' => 'bar_chart',      'label' => 'Rapports'],
        ],
    ],

    'recept' => [
        'label' => 'Guichet',
        'links' => [
            ['path' => 'recept/reservations',       'icon' => 'book_online',    'label' => 'Réservations'],
        ],
    ],

    'driver' => [
        'label' => 'Chauffeur',
        'links' => [
            ['path' => 'driver/planning',           'icon' => 'event_note',     'label' => 'Mon Planning'],
        ],
    ],

];

// Récupérer le menu du rôle courant (fallback vide si rôle inconnu)
$roleMenu = $menus[$userRole] ?? ['label' => '', 'links' => []];
$links    = $roleMenu['links'];
?>

<aside id="sidebar" class="sticky top-0 hidden md:flex flex-col h-screen p-md gap-sm bg-surface-container-lowest border-r border-outline-variant shadow-sm shrink-0 transition-all duration-300 w-72 [&.collapsed]:w-20">

    {{-- ── Logo & Toggle ── --}}
    <div class="flex items-center gap-md px-sm py-md mb-md">
        <div class="w-10 h-10 bg-primary rounded-lg flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined text-on-primary text-headline-md">directions_bus</span>
        </div>
        <div class="[.collapsed_&]:hidden whitespace-nowrap overflow-hidden">
            <h1 class="font-title-lg text-title-lg text-on-surface leading-tight">Kashala Trans</h1>
            <?php if (!empty($roleMenu['label'])): ?>
                <p class="text-label-sm text-outline"><?= esc($roleMenu['label']) ?></p>
            <?php endif; ?>
        </div>
        <button id="toggle-sidebar" class="ml-auto p-sm rounded-full hover:bg-surface-container" aria-label="Réduire le menu">
            <span class="material-symbols-outlined">menu_open</span>
        </button>
    </div>

    {{-- ── Navigation ── --}}
    <nav class="flex-1 space-y-1 overflow-y-auto hide-scrollbar" aria-label="Navigation principale">
        <?php foreach ($links as $link): ?>
            <a
                class="flex items-center gap-md px-md py-sm rounded-lg transition-all duration-150 <?= $isActive($link['path']) ?>"
                href="<?= base_url($link['path']) ?>"
                <?= ($currentPath === $link['path']) ? 'aria-current="page"' : '' ?>
            >
                <span class="material-symbols-outlined shrink-0"><?= $link['icon'] ?></span>
                <span class="font-label-lg text-label-lg [.collapsed_&]:hidden whitespace-nowrap"><?= esc($link['label']) ?></span>
            </a>
        <?php endforeach; ?>
    </nav>

    {{-- ── Déconnexion ── --}}
    <div class="pt-sm border-t border-outline-variant">
        <a
            href="<?= base_url('logout') ?>"
            class="flex items-center gap-md px-md py-sm rounded-lg transition-all duration-150 text-on-surface-variant hover:bg-error-container hover:text-error"
            title="Déconnexion"
        >
            <span class="material-symbols-outlined shrink-0">logout</span>
            <span class="font-label-lg text-label-lg [.collapsed_&]:hidden whitespace-nowrap">Déconnexion</span>
        </a>
    </div>

</aside>

<script>
(function () {
    const sidebar   = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('toggle-sidebar');
    const KEY       = 'sidebar_collapsed';

    if (!sidebar || !toggleBtn) return;

    // Restaurer l'état depuis localStorage
    if (localStorage.getItem(KEY) === '1') {
        sidebar.classList.add('collapsed');
    }

    toggleBtn.addEventListener('click', () => {
        sidebar.classList.toggle('collapsed');
        localStorage.setItem(KEY, sidebar.classList.contains('collapsed') ? '1' : '0');
    });
})();
</script>