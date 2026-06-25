<?php
/**
 * Sidebar dynamique — Liens filtrés selon le rôle de l'utilisateur connecté.
 * Rôles : super_admin | admin | recept | driver
 */
$currentPath = trim(uri_string(), '/');
$user        = session()->get('user') ?? [];
$userRole    = $user['role']['code'] ?? '';

// Helper : classe active pour le lien courant
$isActive = fn($path) => ($currentPath === $path || str_starts_with($currentPath, $path))
    ? 'bg-primary-container text-on-primary-container font-semibold shadow-sm'
    : 'text-on-surface-variant hover:bg-surface-container hover:text-on-surface';

// Définition des menus par rôle
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
            ['path' => 'admin/rapports',            'icon' => 'bar_chart',      'label' => 'Rapports'],
            ['path' => 'admin/planification',       'icon' => 'calendar_today', 'label' => 'Planification'],
            ['path' => 'admin/reservation',         'icon' => 'book_online',    'label' => 'Réservations'],
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

<aside id="sidebar" class="sticky top-0 hidden md:flex flex-col h-screen p-3 bg-surface-container-lowest border-r border-outline-variant shadow-sm shrink-0 transition-all duration-300 ease-in-out w-72 [&.collapsed]:w-20">
    
    <div class="flex items-center gap-3 px-2 py-3 mb-4 overflow-hidden h-16 shrink-0">
        <div class="w-10 h-10 bg-primary -ml-1 rounded-xl flex items-center justify-center shrink-0 shadow-md">
            <span class="material-symbols-outlined text-on-primary text-headline-sm">directions_bus</span>
        </div>
        <div class="[.collapsed_&]:opacity-0 [.collapsed_&]:w-0 transition-all duration-200 ease-in-out whitespace-nowrap overflow-hidden">
            <h1 class="font-bold text-title-md text-on-surface leading-tight">Kashala Trans</h1>
            <?php if (!empty($roleMenu['label'])): ?>
                <p class="text-xs text-outline font-medium"><?= esc($roleMenu['label']) ?></p>
            <?php endif; ?>
        </div>
    </div>

    <nav class="flex-1 space-y-1 overflow-y-auto overflow-x-hidden" aria-label="Navigation principale">
        <?php foreach ($links as $link): ?>
            <a
                class="flex items-center rounded-xl transition-all duration-200 h-12 px-3 gap-4 [.collapsed_&]:gap-0 [.collapsed_&]:justify-center <?= $isActive($link['path']) ?>"
                href="<?= base_url($link['path']) ?>"
                title="<?= esc($link['label']) ?>"
                <?= ($currentPath === $link['path']) ? 'aria-current="page"' : '' ?>
            >
                <div class="w-6 h-6 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined"><?= $link['icon'] ?></span>
                </div>
                <span class="text-sm font-medium transition-all duration-200 ease-in-out whitespace-nowrap overflow-hidden [.collapsed_&]:opacity-0 [.collapsed_&]:w-0"><?= esc($link['label']) ?></span>
            </a>
        <?php endforeach; ?>
    </nav>

    <button 
        id="toggle-sidebar" 
        class="flex items-center rounded-xl transition-all duration-200 h-12 px-3 gap-4 [.collapsed_&]:gap-0 [.collapsed_&]:justify-center text-on-surface-variant hover:bg-surface-container" 
        aria-label="Réduire le menu"
    >
        <div class="w-6 h-6 flex items-center justify-center shrink-0">
            <span id="toggle-icon" class="material-symbols-outlined">menu_open</span>
        </div>
        <span class="text-sm font-medium transition-all duration-200 ease-in-out whitespace-nowrap overflow-hidden [.collapsed_&]:opacity-0 [.collapsed_&]:w-0">Réduire le menu</span>
    </button>
    
    <div class="pt-3 mt-auto border-t border-outline-variant flex flex-col gap-1 shrink-0">
        <a
            href="<?= base_url('logout') ?>"
            class="flex items-center rounded-xl transition-all duration-200 h-12 px-3 gap-4 [.collapsed_&]:gap-0 [.collapsed_&]:justify-center text-on-surface-variant hover:bg-error-container hover:text-error"
            title="Déconnexion"
        >
            <div class="w-6 h-6 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined">logout</span>
            </div>
            <span class="text-sm font-medium transition-all duration-200 ease-in-out whitespace-nowrap overflow-hidden [.collapsed_&]:opacity-0 [.collapsed_&]:w-0">Déconnexion</span>
        </a>

    </div>

</aside>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const sidebar    = document.getElementById('sidebar');
    const toggleBtn  = document.getElementById('toggle-sidebar');
    const toggleIcon = document.getElementById('toggle-icon');
    const KEY        = 'sidebar_collapsed';

    if (!sidebar || !toggleBtn || !toggleIcon) return;

    // Fonction pour mettre à jour l'icône selon l'état
    function updateIcon(isCollapsed) {
        toggleIcon.textContent = isCollapsed ? 'arrow_forward_ios' : 'menu_open';
    }

    // Restaurer l'état initial depuis le localStorage
    const isCollapsed = localStorage.getItem(KEY) === '1';
    if (isCollapsed) {
        sidebar.classList.add('collapsed');
    }
    updateIcon(isCollapsed);

    // Événement Clic
    toggleBtn.addEventListener('click', function(e) {
        e.preventDefault();
        const collapsed = sidebar.classList.toggle('collapsed');
        localStorage.setItem(KEY, collapsed ? '1' : '0');
        updateIcon(collapsed);
    });
});
</script>