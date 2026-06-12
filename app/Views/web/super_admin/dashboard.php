<!DOCTYPE html>

<html class="light" lang="fr">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&amp;display=swap" rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <script
        id="tailwind-config">tailwind.config = { darkMode: "class", theme: { extend: { colors: { "surface-container-lowest": "#ffffff", secondary: "#5c5f60", "on-tertiary": "#ffffff", "tertiary-container": "#ffffff", "surface-tint": "#5d5f5f", "primary-container": "#ffffff", "surface-container-highest": "#e1e2e4", "on-primary": "#ffffff", background: "#f8f9fb", "secondary-container": "#e1e3e4", "on-tertiary-fixed-variant": "#454747", "on-secondary-fixed": "#191c1d", "on-secondary-fixed-variant": "#454748", "surface-bright": "#f8f9fb", "on-tertiary-container": "#747676", "secondary-fixed-dim": "#c5c7c8", "on-tertiary-fixed": "#1a1c1c", "on-primary-fixed": "#1a1c1c", "surface-container-low": "#f3f4f6", surface: "#f8f9fb", "on-primary-fixed-variant": "#454747", outline: "#747878", "inverse-primary": "#c6c6c7", error: "#ba1a1a", "outline-variant": "#c4c7c8", "surface-variant": "#e1e2e4", "surface-container-high": "#e7e8ea", "primary-fixed": "#e2e2e2", "secondary-fixed": "#e1e3e4", "surface-container": "#edeef0", "error-container": "#ffdad6", "on-primary-container": "#747676", "on-error": "#ffffff", tertiary: "#5d5f5f", "surface-dim": "#d9dadc", "on-secondary-container": "#626566", "tertiary-fixed-dim": "#c6c6c7", "primary-fixed-dim": "#c6c6c7", "on-background": "#191c1e", "on-surface": "#191c1e", "inverse-surface": "#2e3132", primary: "#5d5f5f", "on-surface-variant": "#444748", "on-error-container": "#93000a", "inverse-on-surface": "#f0f1f3", "tertiary-fixed": "#e2e2e2", "on-secondary": "#ffffff" }, borderRadius: { DEFAULT: "0.125rem", lg: "0.25rem", xl: "0.5rem", full: "0.75rem" }, spacing: { base: "4px", margin: "24px", xs: "4px", gutter: "16px", sm: "8px", lg: "24px", xl: "32px", md: "16px" }, fontFamily: { "status-badge": ["Inter"], "body-sm": ["Inter"], "label-caps": ["Inter"], h1: ["Inter"], h2: ["Inter"], "body-md": ["Inter"], headline: ["Inter"], display: ["Inter"], body: ["Inter"], label: ["Inter"] }, fontSize: { "status-badge": ["12px", { lineHeight: "12px", fontWeight: "500" }], "body-sm": ["13px", { lineHeight: "18px", fontWeight: "400" }], "label-caps": ["12px", { lineHeight: "16px", letterSpacing: "0.05em", fontWeight: "600" }], h1: ["24px", { lineHeight: "32px", letterSpacing: "-0.02em", fontWeight: "600" }], h2: ["18px", { lineHeight: "28px", fontWeight: "600" }], "body-md": ["14px", { lineHeight: "20px", fontWeight: "400" }] } } } };</script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        body {
            background-color: #f3f4f6;
            /* Style Guidance L0 Background */
        }
    </style>
</head>

<body class="font-body-md text-on-surface">
    <!-- TopAppBar Small (JSON derived) -->
    <header
        class="bg-surface border-b border-outline-variant shadow-sm fixed top-0 left-0 w-full h-[48px] z-50 flex items-center px-gutter justify-between">
        <div class="flex items-center gap-md">
            <span class="material-symbols-outlined text-primary cursor-pointer">apps</span>
            <h1 class="font-h2 text-h2 font-bold text-on-surface">KASHALA Trans</h1>
        </div>
        <div class="flex items-center gap-md">
            <span
                class="material-symbols-outlined text-on-surface-variant hover:bg-surface-container-high transition-colors cursor-pointer p-xs rounded-full">notifications</span>
            <span
                class="material-symbols-outlined text-on-surface-variant hover:bg-surface-container-high transition-colors cursor-pointer p-xs rounded-full">settings</span>
            <div class="flex items-center gap-sm ml-sm cursor-pointer active:opacity-80">
                <span class="font-body-md text-body-md text-on-surface-variant">Admin</span>
                <div
                    class="w-8 h-8 rounded-full bg-surface-container-highest flex items-center justify-center overflow-hidden border border-outline-variant">
                    <img alt="Admin Avatar" class="w-full h-full object-cover"
                        data-alt="A professional corporate headshot of a middle-aged male administrator with a confident expression, set against a soft-focus office background. The lighting is bright and even, matching a high-key light mode UI aesthetic. The overall color palette is composed of soft grays and crisp whites, reflecting a modern and clean business environment."
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuCH045NtEtP6PIFo4NOMSMj0O6kHfqkqHYNqQ26VCTpw8jw3ML5nePzjXhIc5qKfWSNVjwaOshSE8dUiDn76Ur0--gjYqA0Cbc5OH-qj4f8hefX68Cv8fsjA1hhvYxDopDhjxBkqFqupG_nemu_a9pvHcPhD7DldayCBx1Z9BjrC2anRIqCPYBr6OWOiQsnylQdxlOxDjmhAd8Q-p6BCnO07xt9KsGE2soa349AH81W8MZHcN6uEEXyJjM3i7GfGB098uwsT6eGs0y2" />
                </div>
            </div>
        </div>
    </header>
    <!-- TopAppBar Medium (Secondary Nav Area) -->
    <nav
        class="bg-surface-container-low border-b border-outline-variant fixed top-[48px] left-0 w-full h-[56px] z-40 flex items-center px-gutter justify-between">
        <div class="flex items-center h-full">
            <div class="flex items-center gap-sm mr-lg">
                <span class="material-symbols-outlined text-primary">arrow_back</span>
                <span class="font-h2 text-h2 text-on-surface">Dashboard</span>
            </div>
            <!-- Sub-navigation Tabs -->
            <div class="flex items-center h-full gap-lg ml-md">
                <a class="h-full flex items-center text-primary font-bold border-b-2 border-primary px-sm transition-all"
                    href="#">Utilisateurs</a>
                <a class="h-full flex items-center text-secondary hover:text-primary px-sm transition-all"
                    href="#">Trajets</a>
                <a class="h-full flex items-center text-secondary hover:text-primary px-sm transition-all"
                    href="#">Flotte de Bus</a>
            </div>
        </div>
        <div class="flex items-center gap-md">
            <button
                class="bg-[#2563EB] text-white px-md py-xs rounded-lg font-medium flex items-center gap-xs hover:opacity-90 active:scale-95 transition-all">
                <span class="material-symbols-outlined text-[20px]">add</span>
                Ajouter un utilisateur
            </button>
            <span
                class="material-symbols-outlined text-on-surface-variant cursor-pointer hover:bg-surface-container-highest p-xs rounded-full duration-75">filter_list</span>
        </div>
    </nav>
    <!-- Main Canvas Area -->
    <main class="mt-[104px] p-margin max-w-7xl mx-auto">
        <!-- Search & Filter Bar (Utilitarian) -->
        <div
            class="mb-lg flex justify-between items-center bg-white p-md rounded-xl border border-outline-variant shadow-sm">
            <div class="relative flex-1 max-w-md">
                <span
                    class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant">search</span>
                <input
                    class="w-full pl-10 pr-md py-xs bg-surface-container-low border border-outline-variant rounded-lg focus:ring-2 focus:ring-[#2563EB] focus:border-transparent outline-none text-body-md transition-all"
                    placeholder="Rechercher un utilisateur..." type="text" />
            </div>
            <div class="flex gap-sm">
                <button
                    class="px-md py-xs bg-white border border-outline-variant text-secondary rounded-lg text-body-md font-medium hover:bg-surface-container-low transition-colors">Exporter</button>
                <button
                    class="px-md py-xs bg-white border border-outline-variant text-secondary rounded-lg text-body-md font-medium hover:bg-surface-container-low transition-colors">Imprimer</button>
            </div>
        </div>
        <!-- CRUD Table Container (L1 Level) -->
        <div class="bg-white border border-outline-variant rounded-xl shadow-sm overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-low border-b border-outline-variant">
                        <th
                            class="px-gutter py-md font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">
                            Nom de l'utilisateur</th>
                        <th
                            class="px-gutter py-md font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">
                            Rôle</th>
                        <th
                            class="px-gutter py-md font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">
                            Site d'affectation</th>
                        <th
                            class="px-gutter py-md font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">
                            Statut</th>
                        <th
                            class="px-gutter py-md font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider text-right">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                    <!-- Row 1 -->
                    <tr class="hover:bg-surface-container-lowest transition-colors group">
                        <td class="px-gutter py-lg">
                            <div class="flex items-center gap-md">
                                <div
                                    class="w-10 h-10 rounded-full bg-[#E5E7EB] flex items-center justify-center text-[#4B5563] font-bold">
                                    MK</div>
                                <div>
                                    <div class="font-medium text-on-surface">Marcel Kashala</div>
                                    <div class="text-body-sm text-on-surface-variant">m.kashala@kashala.cd</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-gutter py-lg">
                            <span
                                class="px-sm py-xs bg-primary-container text-primary rounded-md font-status-badge text-status-badge">Admin</span>
                        </td>
                        <td class="px-gutter py-lg text-on-surface-variant">Lubumbashi HQ</td>
                        <td class="px-gutter py-lg">
                            <div class="flex items-center gap-xs text-[#16A34A] font-status-badge text-status-badge">
                                <div class="w-2 h-2 rounded-full bg-[#16A34A]"></div>
                                Actif
                            </div>
                        </td>
                        <td class="px-gutter py-lg text-right">
                            <div class="flex justify-end gap-md">
                                <button
                                    class="text-secondary hover:text-[#2563EB] transition-colors flex items-center gap-xs">
                                    <span class="material-symbols-outlined text-[18px]">edit</span>
                                    <span class="text-body-sm font-medium">Modifier</span>
                                </button>
                                <button
                                    class="text-secondary hover:text-error transition-colors flex items-center gap-xs">
                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                    <span class="text-body-sm font-medium">Supprimer</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <!-- Row 2 -->
                    <tr class="hover:bg-surface-container-lowest transition-colors">
                        <td class="px-gutter py-lg">
                            <div class="flex items-center gap-md">
                                <div
                                    class="w-10 h-10 rounded-full bg-[#E5E7EB] flex items-center justify-center text-[#4B5563] font-bold">
                                    JN</div>
                                <div>
                                    <div class="font-medium text-on-surface">Jean Ngoy</div>
                                    <div class="text-body-sm text-on-surface-variant">j.ngoy@kashala.cd</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-gutter py-lg">
                            <span
                                class="px-sm py-xs bg-secondary-container text-secondary rounded-md font-status-badge text-status-badge">Réceptionniste</span>
                        </td>
                        <td class="px-gutter py-lg text-on-surface-variant">Mokambo Transit</td>
                        <td class="px-gutter py-lg">
                            <div class="flex items-center gap-xs text-[#16A34A] font-status-badge text-status-badge">
                                <div class="w-2 h-2 rounded-full bg-[#16A34A]"></div>
                                Actif
                            </div>
                        </td>
                        <td class="px-gutter py-lg text-right">
                            <div class="flex justify-end gap-md">
                                <button
                                    class="text-secondary hover:text-[#2563EB] transition-colors flex items-center gap-xs">
                                    <span class="material-symbols-outlined text-[18px]">edit</span>
                                    <span class="text-body-sm font-medium">Modifier</span>
                                </button>
                                <button
                                    class="text-secondary hover:text-error transition-colors flex items-center gap-xs">
                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                    <span class="text-body-sm font-medium">Supprimer</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <!-- Row 3 -->
                    <tr class="hover:bg-surface-container-lowest transition-colors">
                        <td class="px-gutter py-lg">
                            <div class="flex items-center gap-md">
                                <div
                                    class="w-10 h-10 rounded-full bg-[#E5E7EB] flex items-center justify-center text-[#4B5563] font-bold">
                                    AM</div>
                                <div>
                                    <div class="font-medium text-on-surface">Alice Mwamba</div>
                                    <div class="text-body-sm text-on-surface-variant">a.mwamba@kashala.cd</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-gutter py-lg">
                            <span
                                class="px-sm py-xs bg-secondary-container text-secondary rounded-md font-status-badge text-status-badge">Réceptionniste</span>
                        </td>
                        <td class="px-gutter py-lg text-on-surface-variant">Lubumbashi Station</td>
                        <td class="px-gutter py-lg">
                            <div
                                class="flex items-center gap-xs text-on-surface-variant font-status-badge text-status-badge">
                                <div class="w-2 h-2 rounded-full bg-outline"></div>
                                Inactif
                            </div>
                        </td>
                        <td class="px-gutter py-lg text-right">
                            <div class="flex justify-end gap-md">
                                <button
                                    class="text-secondary hover:text-[#2563EB] transition-colors flex items-center gap-xs">
                                    <span class="material-symbols-outlined text-[18px]">edit</span>
                                    <span class="text-body-sm font-medium">Modifier</span>
                                </button>
                                <button
                                    class="text-secondary hover:text-error transition-colors flex items-center gap-xs">
                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                    <span class="text-body-sm font-medium">Supprimer</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <!-- Pagination Footer -->
            <div
                class="bg-surface-container-low px-gutter py-md border-t border-outline-variant flex justify-between items-center">
                <span class="text-body-sm text-on-surface-variant">Affichage de 1-3 sur 12 utilisateurs</span>
                <div class="flex gap-xs">
                    <button
                        class="w-8 h-8 flex items-center justify-center rounded border border-outline-variant bg-white text-secondary hover:bg-surface-container-lowest">
                        <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                    </button>
                    <button
                        class="w-8 h-8 flex items-center justify-center rounded border border-[#2563EB] bg-[#2563EB] text-white font-medium text-body-sm">1</button>
                    <button
                        class="w-8 h-8 flex items-center justify-center rounded border border-outline-variant bg-white text-secondary hover:bg-surface-container-lowest">2</button>
                    <button
                        class="w-8 h-8 flex items-center justify-center rounded border border-outline-variant bg-white text-secondary hover:bg-surface-container-lowest">
                        <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                    </button>
                </div>
            </div>
        </div>
        <!-- Information Bento Cards (Contextual Help) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-lg mt-xl">
            <div class="bg-white p-lg rounded-xl border border-outline-variant shadow-sm flex flex-col gap-sm">
                <div class="w-10 h-10 rounded-lg bg-primary-container flex items-center justify-center">
                    <span class="material-symbols-outlined text-primary">security</span>
                </div>
                <h3 class="font-h2 text-h2 text-on-surface mt-xs">Contrôle d'accès</h3>
                <p class="text-body-md text-on-surface-variant">Les administrateurs peuvent modifier toutes les
                    configurations, tandis que les réceptionnistes sont limités à la vente de tickets.</p>
            </div>
            <div class="bg-white p-lg rounded-xl border border-outline-variant shadow-sm flex flex-col gap-sm">
                <div class="w-10 h-10 rounded-lg bg-secondary-container flex items-center justify-center">
                    <span class="material-symbols-outlined text-secondary">location_on</span>
                </div>
                <h3 class="font-h2 text-h2 text-on-surface mt-xs">Sites Multiples</h3>
                <p class="text-body-md text-on-surface-variant">Affectez vos agents à des points de vente spécifiques
                    pour un suivi précis des revenus par site.</p>
            </div>
            <div class="bg-white p-lg rounded-xl border border-outline-variant shadow-sm flex flex-col gap-sm">
                <div
                    class="w-10 h-10 rounded-lg bg-tertiary-container border border-outline-variant flex items-center justify-center">
                    <span class="material-symbols-outlined text-on-tertiary-container">history</span>
                </div>
                <h3 class="font-h2 text-h2 text-on-surface mt-xs">Historique d'activité</h3>
                <p class="text-body-md text-on-surface-variant">Consultez les dernières actions effectuées par chaque
                    utilisateur pour maintenir l'intégrité de vos données de transport.</p>
            </div>
        </div>
    </main>
    <!-- FAB for quick action (Conditional - Hidden on settings per guidance but shown here as a demo component anchor if needed, however per prompt instruction we are following the Trello/Odoo structure which usually prefers the top buttons) -->
</body>

</html>