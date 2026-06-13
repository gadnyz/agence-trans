<?php
$currentPath = trim(uri_string(), '/');
$isPlanning = $currentPath === 'planification' || str_starts_with($currentPath, 'planification/');
$isReservations = $currentPath === 'reservations' || str_starts_with($currentPath, 'reservations/');
$isReports = $currentPath === 'rapports' || str_starts_with($currentPath, 'rapports/');

$user = session()->get('user') ?? [];
$displayName = trim((string) (($user['prenom'] ?? '') ?: ($user['username'] ?? 'Admin')));
?>

<aside id="main-sidebar" class="fixed top-0 left-0 h-full w-64 bg-surface border-r border-outline-variant flex flex-col z-50 transition-transform duration-300 lg:translate-x-0 -translate-x-full" aria-label="Menu principal">
    <div class="h-[48px] flex items-center px-gutter border-b border-outline-variant justify-between lg:justify-start">
        <a class="flex items-center gap-sm min-w-0" href="<?= base_url('reservations') ?>">
            <span class="grid h-8 w-8 place-items-center rounded-lg bg-primary-container text-primary">
                <span class="material-symbols-outlined text-[20px]">directions_bus</span>
            </span>
            <span class="font-h2 text-h2 font-bold text-on-surface truncate">KASHALA Trans</span>
        </a>
        <button id="close-drawer-btn" class="lg:hidden inline-flex h-8 w-8 items-center justify-center rounded-lg text-on-surface-variant hover:bg-surface-container" type="button" aria-label="Fermer le menu">
            <span class="material-symbols-outlined text-[22px]">close</span>
        </button>
    </div>

    <nav class="flex-1 p-sm space-y-xs overflow-y-auto">
        <a class="flex items-center gap-md p-md rounded-lg transition-colors <?= $isReservations ? 'bg-primary-container/70 text-primary font-bold' : 'text-on-surface-variant hover:bg-surface-container hover:text-on-surface' ?>" href="<?= base_url('reservations') ?>">
            <span class="material-symbols-outlined text-[22px]">book_online</span>
            <span class="font-body-md">Réservations</span>
        </a>
        <a class="flex items-center gap-md p-md rounded-lg transition-colors <?= $isPlanning ? 'bg-primary-container/70 text-primary font-bold' : 'text-on-surface-variant hover:bg-surface-container hover:text-on-surface' ?>" href="<?= base_url('super-admin/planification') ?>">
            <span class="material-symbols-outlined text-[22px]">event_available</span>
            <span class="font-body-md">Planification</span>
        </a>
        <a class="flex items-center gap-md p-md rounded-lg transition-colors <?= $isReports ? 'bg-primary-container/70 text-primary font-bold' : 'text-on-surface-variant hover:bg-surface-container hover:text-on-surface' ?>" href="<?= base_url('rapports') ?>">
            <span class="material-symbols-outlined text-[22px]">bar_chart</span>
            <span class="font-body-md">Rapports</span>
        </a>
    </nav>

    <div class="p-sm border-t border-outline-variant">
        <div class="flex items-center gap-sm p-sm rounded-lg bg-surface-container-low mb-sm">
            <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-secondary-container text-secondary font-bold text-body-md">
                <?= esc(strtoupper(substr($displayName, 0, 1))) ?>
            </span>
            <div class="min-w-0">
                <p class="text-body-md font-semibold text-on-surface truncate"><?= esc($displayName) ?></p>
                <p class="text-body-sm text-on-surface-variant truncate"><?= esc($user['role']['libelle'] ?? 'Super Admin') ?></p>
            </div>
        </div>
        <a class="flex w-full items-center justify-center gap-xs h-10 rounded-lg text-error hover:bg-error-container/50 transition-colors" href="<?= base_url('logout') ?>">
            <span class="material-symbols-outlined text-[20px]">logout</span>
            <span class="font-body-md font-medium">Déconnexion</span>
        </a>
    </div>
</aside>