<?php
$currentPath = trim(uri_string(), '/');
$isPlanning = $currentPath === 'planification' || str_starts_with($currentPath, 'planification/');
$isReservations = $currentPath === 'reservations' || str_starts_with($currentPath, 'reservations/');
$isReports = $currentPath === 'rapports' || str_starts_with($currentPath, 'rapports/');
?>

<aside id="main-sidebar" class="fixed inset-y-0 left-0 w-[72px] bg-secondary flex flex-col items-center py-lg z-50 transition-transform duration-300 lg:translate-x-0 -translate-x-full shadow-lg lg:shadow-none" aria-label="Menu principal">
    
    <a href="<?= base_url('reservations') ?>" class="w-12 h-12 bg-surface-container-lowest rounded-xl flex items-center justify-center shadow-sm mb-xl hover:scale-105 transition-transform" title="Accueil">
        <span class="material-symbols-outlined text-primary text-[24px]">directions_bus</span>
    </a>

    <nav class="flex flex-col gap-sm w-full px-sm flex-1">
        
        <a href="<?= base_url('reservations') ?>" class="w-full aspect-square flex flex-col items-center justify-center rounded-lg transition-colors <?= $isReservations ? 'bg-primary text-on-primary shadow-md' : 'text-outline-variant hover:bg-tertiary-container hover:text-on-secondary' ?>" title="Réservations">
            <span class="material-symbols-outlined text-[24px]">book_online</span>
        </a>
        
        <a href="<?= base_url('planification') ?>" class="w-full aspect-square flex flex-col items-center justify-center rounded-lg transition-colors <?= $isPlanning ? 'bg-primary text-on-primary shadow-md' : 'text-outline-variant hover:bg-tertiary-container hover:text-on-secondary' ?>" title="Planification">
            <span class="material-symbols-outlined text-[24px]">event_available</span>
        </a>
        
        <a href="<?= base_url('rapports') ?>" class="w-full aspect-square flex flex-col items-center justify-center rounded-lg transition-colors <?= $isReports ? 'bg-primary text-on-primary shadow-md' : 'text-outline-variant hover:bg-tertiary-container hover:text-on-secondary' ?>" title="Tableau de bord">
            <span class="material-symbols-outlined text-[24px]">bar_chart</span>
        </a>
        
    </nav>

    <div class="mt-auto w-full px-sm">
        <a href="<?= base_url('logout') ?>" class="w-full aspect-square flex flex-col items-center justify-center rounded-lg text-outline-variant hover:bg-error/20 hover:text-error transition-colors" title="Déconnexion">
            <span class="material-symbols-outlined text-[24px]">logout</span>
        </a>
    </div>
</aside>