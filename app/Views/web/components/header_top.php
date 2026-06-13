<?php
$user = session()->get('user') ?? [];
$displayName = trim((string) (($user['prenom'] ?? '') ?: ($user['username'] ?? 'Admin')));
?>

<header class="h-[72px] bg-surface-container-lowest border-b border-outline-variant flex items-center justify-between px-md lg:px-xl sticky top-0 z-30 shadow-sm">
    
    <div class="flex items-center gap-md">
        <button id="mobile-menu-btn" class="lg:hidden p-xs text-on-surface-variant hover:bg-surface-container rounded-lg transition-colors" type="button">
            <span class="material-symbols-outlined text-[24px]">menu</span>
        </button>
        <h1 class="text-xl font-bold text-on-surface tracking-tight hidden sm:block"><?= esc($pageTitle ?? 'Dispatch Hub') ?></h1>
    </div>
    
    <div class="flex items-center gap-lg">
        
        <div class="hidden md:flex relative w-64 lg:w-80">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[20px]">search</span>
            <input type="text" placeholder="Rechercher (bus, trajet, agent)..." class="w-full pl-10 pr-4 py-2 bg-surface-container-low border border-outline-variant rounded-full text-sm focus:border-primary focus:ring-1 focus:ring-primary focus:bg-surface-container-lowest outline-none transition-all placeholder:text-on-surface-variant">
        </div>

        <div class="flex items-center gap-sm">
            <button class="p-2 text-on-surface-variant hover:bg-surface-container hover:text-primary rounded-full relative transition-colors">
                <span class="material-symbols-outlined">notifications</span>
                <span class="absolute top-1.5 right-2 w-2 h-2 bg-error border-2 border-surface-container-lowest rounded-full"></span>
            </button>
            
            <div class="flex items-center gap-sm ml-sm sm:border-l sm:border-outline-variant sm:pl-md cursor-pointer hover:opacity-80 transition-opacity">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-semibold text-on-surface leading-tight"><?= esc($displayName) ?></p>
                    <p class="text-xs text-on-surface-variant"><?= esc($user['role']['libelle'] ?? 'Super Admin') ?></p>
                </div>
                <div class="w-10 h-10 rounded-full bg-primary-container text-primary flex items-center justify-center font-bold border border-primary/10 shadow-sm">
                    <?= esc(strtoupper(substr($displayName, 0, 1))) ?>
                </div>
            </div>
        </div>
    </div>
</header>