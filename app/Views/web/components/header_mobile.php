<?php
$user = session()->get('user') ?? [];
$displayName = trim(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? '')) ?: ($user['username'] ?? 'Utilisateur');
$roleLabel = esc($user['role']['libelle'] ?? 'Fleet Manager');
?>

<header class="md:hidden sticky top-0 left-0 w-full mb-3.5 h-16 bg-white border-b border-outline-variant/60 flex items-center px-4 justify-between z-40 shadow-sm shrink-0">
    <div class="flex items-center gap-1 min-w-0">
        <button
            id="mobile-menu-btn"
            class="inline-flex h-9 w-9 items-center justify-center rounded-xl text-on-surface-variant hover:bg-surface-container active:scale-95 transition-all focus:outline-none"
            type="button"
            aria-label="Ouvrir le menu"
            aria-controls="mobile-drawer"
            aria-expanded="false"
        >
            <span class="material-symbols-outlined -m-2 text-[23px]">menu</span>
        </button>

        <div class="flex items-center gap-1">
            <div class="w-9 h-9 bg-primary rounded-xl flex items-center justify-center shrink-0 shadow-md">
                <span class="material-symbols-outlined text-on-primary text-[18px]">directions_bus</span>
            </div>
            <div>
                <h2 class="font-bold text-sm text-gray-900 leading-tight">Kashala Trans</h2>
                <?php if (!empty($roleMenu['label'])): ?>
                    <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wider"><?= esc($roleMenu['label']) ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="relative flex items-center shrink-0">
        <button
            id="mobile-profile-btn"
            class="w-9 h-9 rounded-full bg-primary-container text-primary flex items-center justify-center font-bold text-sm border border-primary/20 shadow-sm hover:scale-105 active:scale-95 transition-all focus:outline-none"
            aria-label="Menu profil"
            aria-haspopup="true"
            aria-expanded="false"
        >
            <?= esc(strtoupper(substr($displayName, 0, 1))) ?>
        </button>
        
        <!-- Profile Dropdown Menu -->
<div
    id="mobile-profile-dropdown"
    class="hidden absolute right-0 top-11 w-64 bg-white border border-gray-200/80 rounded-2xl shadow-xl py-2 z-50 origin-top-right transition-all duration-150 ease-out transform scale-95 opacity-0"
    role="menu"
    aria-label="Options utilisateur"
>
    <!-- En-tête du profil -->
    <div class="px-4 py-3 border-b border-gray-100">
        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Mon Compte</p>
        <p class="text-sm font-bold text-gray-900 mt-0.5 truncate" title="<?= esc($displayName) ?>">
            <?= esc($displayName) ?>
        </p>
        <div class="flex items-center gap-1.5 mt-1">
            <!-- <span class="inline-block w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span> -->
            <p class="text-[11px] text-gray-500 font-semibold truncate uppercase tracking-wide bg-gray-50 px-1.5 py-0.5 rounded border border-gray-100">
                <?= esc($roleLabel) ?>
            </p>
        </div>
    </div>

    <!-- Liens de navigation -->
    <div class="px-2 py-1.5 space-y-0.5 border-b border-gray-100">
        <a 
            href="<?= base_url('profile') ?>" 
            class="flex items-center gap-3 px-3 py-2 text-sm text-gray-700 font-medium rounded-xl hover:bg-gray-50 hover:text-blue-600 transition-colors group" 
            role="menuitem"
        >
            <span class="material-symbols-outlined text-[20px] text-gray-400 group-hover:text-blue-500 transition-colors">
                person
            </span>
            <span>Mon Profil</span>
        </a>

        <!-- <a 
            href="<?= base_url('settings') ?>" 
            class="flex items-center gap-3 px-3 py-2 text-sm text-gray-700 font-medium rounded-xl hover:bg-gray-50 hover:text-blue-600 transition-colors group" 
            role="menuitem"
        >
            <span class="material-symbols-outlined text-[20px] text-gray-400 group-hover:text-blue-500 transition-colors">
                settings
            </span>
            <span>Paramètres</span>
        </a> -->
    </div>

    <!-- Zone de déconnexion -->
    <div class="px-2 pt-1.5 pb-0.5">
        <a 
            href="<?= base_url('logout') ?>" 
            class="flex items-center gap-3 px-3 py-2 text-sm text-red-600 font-semibold rounded-xl hover:bg-red-50 transition-colors group" 
            role="menuitem"
        >
            <span class="material-symbols-outlined text-[20px] text-red-500 group-hover:scale-105 transition-transform">
                logout
            </span>
            <span>Déconnexion</span>
        </a>
    </div>
</div>
    </div>
</header>