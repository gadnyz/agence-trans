<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Kashala Trans Management') ?></title>
    <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('img/favicon-32x32.png') ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('img/favicon-16x16.png') ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= base_url('img/apple-icon-180x180.png') ?>">
    <!-- Inter Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <!-- Tailwind CDN for existing views -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">tailwind.config = {darkMode: "class", theme: {extend: {colors: {"on-error": "#ffffff", "surface-container-highest": "#e1e2e4", "on-primary-container": "#747676", "on-primary-fixed": "#1a1c1c", "surface-tint": "#5d5f5f", "error-container": "#ffdad6", surface: "#f8f9fb", "on-primary": "#ffffff", "tertiary-container": "#ffffff", "on-secondary-fixed": "#191c1d", "on-primary-fixed-variant": "#454747", "surface-variant": "#e1e2e4", secondary: "#5c5f60", "on-tertiary-container": "#747676", "surface-container-lowest": "#ffffff", "on-secondary-container": "#626566", "surface-bright": "#f8f9fb", "primary-container": "#ffffff", "inverse-on-surface": "#f0f1f3", "on-secondary-fixed-variant": "#454748", background: "#f8f9fb", "surface-container-low": "#f3f4f6", "surface-dim": "#d9dadc", "primary-fixed": "#e2e2e2", "on-tertiary-fixed-variant": "#454747", error: "#ba1a1a", "tertiary-fixed": "#e2e2e2", "surface-container-high": "#e7e8ea", "surface-container": "#edeef0", "on-tertiary": "#ffffff", "tertiary-fixed-dim": "#c6c6c7", "secondary-fixed": "#e1e3e4", "on-secondary": "#ffffff", "on-error-container": "#93000a", "on-surface": "#191c1e", "inverse-primary": "#c6c6c7", primary: "#5d5f5f", outline: "#747878", "secondary-fixed-dim": "#c5c7c8", "on-tertiary-fixed": "#1a1c1c", "outline-variant": "#c4c7c8", "inverse-surface": "#2e3132", tertiary: "#5d5f5f", "secondary-container": "#e1e3e4", "primary-fixed-dim": "#c6c6c7", "on-surface-variant": "#444748", "on-background": "#191c1e"}, borderRadius: {DEFAULT: "0.125rem", lg: "0.25rem", xl: "0.5rem", full: "0.75rem"}, spacing: {xs: "4px", xl: "32px", lg: "24px", gutter: "16px", sm: "8px", margin: "24px", md: "16px", base: "4px"}, fontFamily: {"body-md": ["Inter"], h1: ["Inter"], "label-caps": ["Inter"], "body-sm": ["Inter"], h2: ["Inter"], "status-badge": ["Inter"], headline: ["Inter"], display: ["Inter"], body: ["Inter"], label: ["Inter"]}, fontSize: {"body-md": ["14px", {lineHeight: "20px", fontWeight: "400"}], h1: ["24px", {lineHeight: "32px", letterSpacing: "-0.02em", fontWeight: "600"}], "label-caps": ["12px", {lineHeight: "16px", letterSpacing: "0.05em", fontWeight: "600"}], "body-sm": ["13px", {lineHeight: "18px", fontWeight: "400"}], h2: ["18px", {lineHeight: "28px", fontWeight: "600"}], "status-badge": ["12px", {lineHeight: "12px", fontWeight: "500"}]}}}};</script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <!-- Design System CSS -->
    <link rel="stylesheet" href="<?= base_url('css/design-system.css') ?>">
    <?= $this->renderSection('styles') ?>
</head>
<body>
    <?php
    $currentPath = trim(uri_string(), '/');
    $isPlanning = $currentPath === 'planification' || str_starts_with($currentPath, 'planification/');
    $isReservations = $currentPath === 'reservations' || str_starts_with($currentPath, 'reservations/');
    $isReports = $currentPath === 'rapports' || str_starts_with($currentPath, 'rapports/');
    ?>
    
    <?php if (session()->get('access_token')): ?>
        <!-- TopAppBar Small -->
        <header class="fixed top-0 left-0 w-full h-[48px] z-50 flex items-center px-gutter justify-between bg-surface border-b border-outline-variant shadow-sm">
            <div class="flex items-center gap-md">
                <div id="mobile-menu-btn" class="cursor-pointer active:opacity-80 hover:bg-surface-container-high p-xs rounded lg:hidden">
                    <span class="material-symbols-outlined text-primary" data-icon="menu">menu</span>
                </div>
                <img class="hidden lg:block h-8 w-8 object-contain" src="<?= base_url('img/bus.png') ?>" alt="KASHALA Trans">
                <h1 class="font-h2 text-h2 font-bold text-on-surface ml-xs lg:ml-0">KASHALA Trans</h1>
                
                <!-- Desktop Navigation -->
                <nav class="hidden md:flex items-center ml-lg gap-md h-[48px]">
                     <a class="<?= $isReservations ? 'text-primary font-bold border-b-2 border-primary' : 'text-on-surface-variant hover:bg-surface-container-high' ?> h-full flex items-center px-sm transition-colors" href="<?= base_url('reservations') ?>">Réservations</a>
                    <a class="<?= $isPlanning ? 'text-primary font-bold border-b-2 border-primary' : 'text-on-surface-variant hover:bg-surface-container-high' ?> h-full flex items-center px-sm transition-colors" href="<?= base_url('planification') ?>">Planification</a>
                    <a class="<?= $isReports ? 'text-primary font-bold border-b-2 border-primary' : 'text-on-surface-variant hover:bg-surface-container-high' ?> h-full flex items-center px-sm transition-colors" href="<?= base_url('rapports') ?>">Rapports</a>
                </nav>
            </div>
            
            <div class="flex items-center gap-md">
                <div class="flex items-center gap-xs ml-sm sm:border-l sm:border-outline-variant pl-md">
                    <span class="text-body-sm font-medium hidden sm:block"><?= esc(session()->get('user')['prenom'] ?? 'Admin') ?></span>
                    <a href="<?= base_url('logout') ?>" class="text-on-surface-variant ml-xs hover:text-error" title="Déconnexion"><span class="material-symbols-outlined text-[20px]">logout</span></a>
                </div>
            </div>
        </header>
    <?php endif; ?>

    <div class="app-content-wrapper">
        <!-- Messages flash -->
        <?php if (session()->getFlashdata('error')): ?>
            <div class="card mb-4 mx-gutter mt-gutter" style="background-color: #fef2f2; border-color: #f87171; color: #991b1b; padding: 12px;">
                <?= esc(session()->getFlashdata('error')) ?>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('success')): ?>
            <div class="card mb-4 mx-gutter mt-gutter" style="background-color: #ecfdf5; border-color: #34d399; color: #065f46; padding: 12px;">
                <?= esc(session()->getFlashdata('success')) ?>
            </div>
        <?php endif; ?>

        <!-- Contenu de la vue spécifique -->
        <?= $this->renderSection('content') ?>
    </div>

    <!-- Menu mobile (Drawer) -->
    <aside id="mobile-drawer"
        class="fixed top-0 left-0 h-full z-[60] py-lg px-md w-80 bg-surface-container-lowest border-r border-outline-variant shadow-xl transform -translate-x-full lg:hidden transition-transform duration-300">
        <div class="flex items-center justify-between mb-lg">
            <div class="flex items-center gap-md">
                <img class="w-10 h-10 object-contain" src="<?= base_url('img/bus.png') ?>" alt="KASHALA Trans">
                <div>
                    <h3 class="font-h2 text-h2 font-black text-primary">KASHALA Trans</h3>
                    <p class="text-body-sm text-on-surface-variant">Agence de transport</p>
                </div>
            </div>
            <button id="close-drawer-btn" class="material-symbols-outlined text-on-surface-variant p-2 hover:bg-surface-container-highest rounded-full">close</button>
        </div>
        <nav class="space-y-sm">
              <a class="flex items-center gap-md p-md <?= $isReservations ? 'bg-secondary-container text-on-secondary-container' : 'text-on-surface-variant hover:bg-surface-container' ?> rounded-xl transition-colors" href="<?= base_url('reservations') ?>">
                <span class="material-symbols-outlined" data-icon="book_online">book_online</span>
                <span class="font-body-md font-medium">Réservations</span>
            </a>
            <a class="flex items-center gap-md p-md <?= $isPlanning ? 'bg-secondary-container text-on-secondary-container' : 'text-on-surface-variant hover:bg-surface-container' ?> rounded-xl transition-colors" href="<?= base_url('planification') ?>">
                <span class="material-symbols-outlined" data-icon="event_available">event_available</span>
                <span class="font-body-md font-medium">Planification</span>
            </a>
            <a class="flex items-center gap-md p-md <?= $isReports ? 'bg-secondary-container text-on-secondary-container' : 'text-on-surface-variant hover:bg-surface-container' ?> rounded-xl transition-colors" href="<?= base_url('rapports') ?>">
                <span class="material-symbols-outlined" data-icon="bar_chart">bar_chart</span>
                <span class="font-body-md font-medium">Rapports</span>
            </a>
        </nav>
    </aside>

    <?= $this->renderSection('scripts') ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const mobileBtn = document.getElementById('mobile-menu-btn');
            const drawer = document.getElementById('mobile-drawer');
            const closeBtn = document.getElementById('close-drawer-btn');
            
            if(mobileBtn && drawer) {
                mobileBtn.addEventListener('click', () => {
                    drawer.classList.remove('-translate-x-full');
                });
            }
            if(closeBtn && drawer) {
                closeBtn.addEventListener('click', () => {
                    drawer.classList.add('-translate-x-full');
                });
            }
        });
    </script>
</body>
</html>
