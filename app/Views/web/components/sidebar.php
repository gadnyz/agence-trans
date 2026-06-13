<?php
$currentPath = trim(uri_string(), '/');
$isActive = fn($path) => ($currentPath === $path || str_starts_with($currentPath, $path)) 
    ? 'bg-primary-container text-on-primary-container font-bold' 
    : 'text-on-surface-variant hover:bg-surface-container hover:text-on-surface';
?>

<!-- <aside id="sidebar" class="sticky top-0 hidden md:flex flex-col h-screen p-md gap-sm bg-surface-container-lowest border-r border-outline-variant shadow-sm shrink-0 transition-all duration-300 w-72 group-[.collapsed]:w-20"> -->
    <aside id="sidebar" class="sticky top-0 hidden md:flex flex-col h-screen p-md gap-sm bg-surface-container-lowest border-r border-outline-variant shadow-sm shrink-0 transition-all duration-300 w-72 [&.collapsed]:w-20">
    <div class="flex items-center gap-md px-sm py-md mb-md">
        <div class="w-10 h-10 bg-primary rounded-lg flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined text-on-primary text-headline-md">directions_bus</span>
        </div>
        <div class="group-[.collapsed]:hidden whitespace-nowrap overflow-hidden">
            <h1 class="font-title-lg text-title-lg text-on-surface leading-tight">Kashala Trans</h1>
        </div>
        <button id="toggle-sidebar" class="ml-auto p-sm rounded-full hover:bg-surface-container">
            <span class="material-symbols-outlined">menu_open</span>
        </button>
    </div>

    <nav class="flex-1 space-y-1 overflow-y-auto hide-scrollbar">
        <?php 
        $links = [
            ['path' => 'super-admin/dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard'],
            ['path' => 'super-admin/reservations', 'icon' => 'book_online', 'label' => 'Reservations'],
            ['path' => 'super-admin/buses', 'icon' => 'directions_bus', 'label' => 'Buses'],
            ['path' => 'super-admin/drivers', 'icon' => 'person', 'label' => 'Drivers'],
            ['path' => 'super-admin/trips', 'icon' => 'route', 'label' => 'Trips'],
            ['path' => 'super-admin/planification', 'icon' => 'calendar_today', 'label' => 'Schedules'],
            ['path' => 'super-admin/clients', 'icon' => 'groups', 'label' => 'Clients'],
            ['path' => 'super-admin/payments', 'icon' => 'payments', 'label' => 'Payments'],
            ['path' => 'super-admin/rapports', 'icon' => 'analytics', 'label' => 'Financial Analysis'],
        ];
        foreach ($links as $link): ?>
            <a class="flex items-center gap-md px-md py-sm rounded-lg transition-all duration-150 <?= $isActive($link['path']) ?>" href="<?= base_url($link['path']) ?>">
                <span class="material-symbols-outlined shrink-0"><?= $link['icon'] ?></span>
                <span class="font-label-lg text-label-lg group-[.collapsed]:hidden whitespace-nowrap"><?= $link['label'] ?></span>
            </a>
        <?php endforeach; ?>
    </nav>

    <script>
(function() {
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('toggle-sidebar');
    
    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            
            // Debug pour voir si la classe est bien ajoutée
            console.log('Sidebar collapsed:', sidebar.classList.contains('collapsed'));
        });
    }
})();
</script>
</aside>

<!-- <script>
    document.getElementById('toggle-sidebar').addEventListener('click', () => {
        document.getElementById('sidebar').classList.toggle('collapsed');
    });
</script> -->