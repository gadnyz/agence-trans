<?php
$user = session()->get('user') ?? [];

$prenom = $user['prenom'] ?? '';
$nom = $user['nom'] ?? '';

$displayName = trim($prenom . ' ' . $nom);
if (empty($displayName)) {
    $displayName = $user['username'] ?? 'Admin';
}

$roleLabel = esc($user['role']['libelle'] ?? 'Fleet Manager');
?>

<header class="flex justify-end items-center h-16 px-md lg:px-margin-desktop w-full bg-surface-container-lowest border-b border-outline-variant shrink-0 z-20 sticky top-0">
    
    <!-- <div class="flex items-center flex-1 max-w-xl">
        <div class="relative w-full max-w-md">
            <span class="material-symbols-outlined absolute left-md top-1/2 -translate-y-1/2 text-outline">search</span>
            <input 
                type="text" 
                placeholder="Search fleet, drivers, or routes..." 
                class="w-full pl-xl pr-md py-sm bg-surface border border-outline-variant rounded-full text-body-md focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all"
            />
        </div>
    </div> -->

    <div class="flex items-center gap-lg">
        
        <!-- <nav class="hidden lg:flex items-center gap-md font-body-md text-body-md">
            <a class="text-secondary hover:text-primary transition-colors" href="#">Support</a>
            <a class="text-secondary hover:text-primary transition-colors" href="#">FAQ</a>
            <a class="text-secondary hover:text-primary transition-colors" href="#">Feedback</a>
        </nav> -->

        <!-- <div class="h-6 w-px bg-outline-variant mx-sm"></div> -->

        <div class="flex items-center gap-md">
            <!-- <button class="p-xs rounded-full hover:bg-surface-container-high transition-colors relative">
                <span class="material-symbols-outlined text-outline">notifications</span>
                <span class="absolute top-0 right-0 w-2 h-2 bg-error rounded-full">3</span>
            </button>
            
            <button class="p-xs rounded-full hover:bg-surface-container-high transition-colors">
                <span class="material-symbols-outlined text-outline">settings</span>
            </button> -->

            <div class="flex items-center gap-sm pl-sm cursor-pointer group">
                <div class="text-right hidden sm:block">
                    <p class="font-label-lg text-label-lg text-on-surface"><?= esc($prenom . " " . $nom) ?></p>
                    <p class="text-label-sm text-outline"><?= $roleLabel ?></p>
                </div>
                <div class="w-10 h-10 rounded-full bg-primary-container text-primary flex items-center justify-center font-bold border border-outline-variant shadow-sm overflow-hidden">
                    <?= esc(strtoupper(substr($displayName, 0, 1))) ?>
                </div>
            </div>
        </div>
    </div>
</header>