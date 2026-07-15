<?php
$user = session()->get('user') ?? [];
$displayName = trim(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? '')) ?: ($user['username'] ?? 'Utilisateur');
$roleLabel = esc($user['role']['libelle'] ?? 'Fleet Manager');
?>

<header class="md:hidden sticky top-0 left-0 w-full h-14 bg-white border-b border-outline-variant/60 flex items-center px-4 justify-between z-40 shadow-sm shrink-0">
    <div class="flex items-center gap-3 min-w-0">
        <button
            id="mobile-menu-btn"
            class="inline-flex h-9 w-9 items-center justify-center rounded-xl text-on-surface-variant hover:bg-surface-container active:scale-95 transition-all focus:outline-none"
            type="button"
            aria-label="Ouvrir le menu"
            aria-controls="mobile-drawer"
            aria-expanded="false"
        >
            <span class="material-symbols-outlined text-[22px]">menu</span>
        </button>
        <div class="flex items-center gap-2 min-w-0">
            <span class="grid h-8 w-8 place-items-center rounded-lg bg-primary/10 text-primary shrink-0">
                <span class="material-symbols-outlined text-[18px] font-bold">directions_bus</span>
            </span>
            <span class="font-bold text-sm text-gray-900 tracking-tight truncate">KASHALA Trans</span>
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
            class="hidden absolute right-0 top-11 w-56 bg-white border border-outline-variant/60 rounded-2xl shadow-xl py-2 z-50 origin-top-right transition-all duration-150 ease-out transform scale-95 opacity-0"
            role="menu"
            aria-label="Options utilisateur"
        >
            <div class="px-4 py-2.5 border-b border-gray-100">
                <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wider">Mon Compte</p>
                <p class="text-sm font-bold text-gray-900 mt-0.5 truncate"><?= esc($displayName) ?></p>
                <p class="text-[11px] text-gray-500 font-medium truncate"><?= $roleLabel ?></p>
            </div>
            <div class="py-1">
                <a href="<?= base_url('logout') ?>" class="flex items-center gap-3 px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors font-medium" role="menuitem">
                    <span class="material-symbols-outlined text-[18px]">logout</span>
                    <span>Déconnexion</span>
                </a>
            </div>
        </div>
    </div>
</header>