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
        id="tailwind-config">tailwind.config = { darkMode: "class", theme: { extend: { colors: { "on-error": "#ffffff", "surface-container-highest": "#e1e2e4", "on-primary-container": "#747676", "on-primary-fixed": "#1a1c1c", "surface-tint": "#5d5f5f", "error-container": "#ffdad6", surface: "#f8f9fb", "on-primary": "#ffffff", "tertiary-container": "#ffffff", "on-secondary-fixed": "#191c1d", "on-primary-fixed-variant": "#454747", "surface-variant": "#e1e2e4", secondary: "#5c5f60", "on-tertiary-container": "#747676", "surface-container-lowest": "#ffffff", "on-secondary-container": "#626566", "surface-bright": "#f8f9fb", "primary-container": "#ffffff", "inverse-on-surface": "#f0f1f3", "on-secondary-fixed-variant": "#454748", background: "#f8f9fb", "surface-container-low": "#f3f4f6", "surface-dim": "#d9dadc", "primary-fixed": "#e2e2e2", "on-tertiary-fixed-variant": "#454747", error: "#ba1a1a", "tertiary-fixed": "#e2e2e2", "surface-container-high": "#e7e8ea", "surface-container": "#edeef0", "on-tertiary": "#ffffff", "tertiary-fixed-dim": "#c6c6c7", "secondary-fixed": "#e1e3e4", "on-secondary": "#ffffff", "on-error-container": "#93000a", "on-surface": "#191c1e", "inverse-primary": "#c6c6c7", primary: "#5d5f5f", outline: "#747878", "secondary-fixed-dim": "#c5c7c8", "on-tertiary-fixed": "#1a1c1c", "outline-variant": "#c4c7c8", "inverse-surface": "#2e3132", tertiary: "#5d5f5f", "secondary-container": "#e1e3e4", "primary-fixed-dim": "#c6c6c7", "on-surface-variant": "#444748", "on-background": "#191c1e" }, borderRadius: { DEFAULT: "0.125rem", lg: "0.25rem", xl: "0.5rem", full: "0.75rem" }, spacing: { xs: "4px", xl: "32px", lg: "24px", gutter: "16px", sm: "8px", margin: "24px", md: "16px", base: "4px" }, fontFamily: { "body-md": ["Inter"], h1: ["Inter"], "label-caps": ["Inter"], "body-sm": ["Inter"], h2: ["Inter"], "status-badge": ["Inter"], headline: ["Inter"], display: ["Inter"], body: ["Inter"], label: ["Inter"] }, fontSize: { "body-md": ["14px", { lineHeight: "20px", fontWeight: "400" }], h1: ["24px", { lineHeight: "32px", letterSpacing: "-0.02em", fontWeight: "600" }], "label-caps": ["12px", { lineHeight: "16px", letterSpacing: "0.05em", fontWeight: "600" }], "body-sm": ["13px", { lineHeight: "18px", fontWeight: "400" }], h2: ["18px", { lineHeight: "28px", fontWeight: "600" }], "status-badge": ["12px", { lineHeight: "12px", fontWeight: "500" }] } } } };</script>
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
    <!-- Shared TopAppBar (Small) -->
    <header
        class="bg-surface dark:bg-inverse-surface border-b border-outline-variant dark:border-outline shadow-sm fixed top-0 left-0 w-full h-[48px] z-50 flex items-center px-gutter justify-between">
        <div class="flex items-center gap-md">
            <span class="material-symbols-outlined text-primary dark:text-inverse-primary cursor-pointer">apps</span>
            <span class="font-h2 text-h2 font-bold text-on-surface dark:text-inverse-on-surface">KASHALA Trans</span>
        </div>
        <div class="flex items-center gap-md">
            <span
                class="material-symbols-outlined text-on-surface-variant hover:bg-surface-container-high transition-colors cursor-pointer p-xs rounded">notifications</span>
            <span
                class="material-symbols-outlined text-on-surface-variant hover:bg-surface-container-high transition-colors cursor-pointer p-xs rounded">settings</span>
            <div class="flex items-center gap-sm ml-sm">
                <span class="font-body-md text-on-surface">Admin</span>
                <div
                    class="w-8 h-8 rounded-full bg-secondary-container flex items-center justify-center overflow-hidden">
                    <img alt="Admin Avatar" class="w-full h-full object-cover"
                        data-alt="A professional corporate headshot of a logistics manager in a modern office environment. The lighting is bright and clean with a minimalist light-grey background. The style is sharp and high-definition, reflecting a professional business application interface. Colors are neutral with soft shadows."
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuC4kaxL-Wx7bCw7HVN6TqunQ-ltAdNsZsTIPrc5FnduTmzndKfQEr2O_tGptab5kldBa9j1VLyr88Ei-k7HQheXR32KQT-3DJw7FGDIyvOSCEgMvRL5o5L3enKKHP97FugjWILeC0nJmzojLVeuRd2p0KQJxVu92hUxC6gdgX3ug52Q6YHAGa5A0R-uzjxYajOQre2oGXfyA0U1OnIygOGEedUdSx2fl6HzsZ10YjiP_lx3h74674LLz0tqssgABjDiEZ9FeUSocdlk" />
                </div>
            </div>
        </div>
    </header>
    <!-- Shared TopAppBar (Medium) -->
    <div
        class="bg-surface-container-low dark:bg-surface-dim border-b border-outline-variant dark:border-outline fixed top-[48px] left-0 w-full h-[56px] z-40 flex items-center px-gutter justify-between">
        <div class="flex items-center gap-md">
            <span
                class="material-symbols-outlined text-primary dark:text-inverse-primary scale-95 duration-75 cursor-pointer">arrow_back</span>
            <h1 class="font-h2 text-h2 text-on-surface">Analyse</h1>
        </div>
        <div class="flex items-center gap-sm">
            <button
                class="bg-surface px-md py-xs border border-outline-variant rounded hover:bg-surface-container-highest transition-colors flex items-center gap-xs font-body-md text-on-surface">
                <span class="material-symbols-outlined text-[18px]">download</span>
                Exporter Excel/PDF
            </button>
            <span
                class="material-symbols-outlined text-primary scale-95 duration-75 cursor-pointer p-xs">filter_list</span>
        </div>
    </div>
    <!-- Navigation Drawer -->
    <!-- Main Content Canvas -->
    <main class="pt-[124px] px-lg pb-lg min-h-screen">
        <!-- Horizontal Filter Bar -->
        <section
            class="bg-surface-container-lowest border border-outline-variant rounded shadow-[0_1px_3px_rgba(0,0,0,0.05)] p-md mb-lg flex flex-wrap items-center gap-lg">
            <div class="flex-1 min-w-[200px]">
                <label class="font-label-caps text-label-caps text-on-surface-variant block mb-base">Période</label>
                <div class="relative">
                    <select
                        class="w-full bg-surface border border-outline-variant rounded px-sm py-xs font-body-sm focus:border-primary focus:ring-1 focus:ring-primary appearance-none">
                        <option>Derniers 30 jours</option>
                        <option>Ce mois</option>
                        <option>Cette année</option>
                        <option>Personnalisé</option>
                    </select>
                    <span
                        class="material-symbols-outlined absolute right-2 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none">expand_more</span>
                </div>
            </div>
            <div class="flex-1 min-w-[150px]">
                <label class="font-label-caps text-label-caps text-on-surface-variant block mb-base">Site</label>
                <select
                    class="w-full bg-surface border border-outline-variant rounded px-sm py-xs font-body-sm focus:border-primary focus:ring-1 focus:ring-primary">
                    <option>Tous les sites</option>
                    <option>Kinshasa - Gombe</option>
                    <option>Lubumbashi - Centre</option>
                </select>
            </div>
            <div class="flex-1 min-w-[150px]">
                <label class="font-label-caps text-label-caps text-on-surface-variant block mb-base">Bus</label>
                <select
                    class="w-full bg-surface border border-outline-variant rounded px-sm py-xs font-body-sm focus:border-primary focus:ring-1 focus:ring-primary">
                    <option>Tous les bus</option>
                    <option>King Long - 45 Places</option>
                    <option>Coaster - 22 Places</option>
                </select>
            </div>
            <div class="flex-1 min-w-[150px]">
                <label class="font-label-caps text-label-caps text-on-surface-variant block mb-base">Statut</label>
                <select
                    class="w-full bg-surface border border-outline-variant rounded px-sm py-xs font-body-sm focus:border-primary focus:ring-1 focus:ring-primary">
                    <option>Validé</option>
                    <option>En attente</option>
                    <option>Annulé</option>
                </select>
            </div>
            <div class="flex items-end h-full">
                <button
                    class="bg-[#2563EB] text-white px-lg py-xs rounded font-body-md hover:opacity-90 transition-opacity">
                    Appliquer
                </button>
            </div>
        </section>
        <!-- Main Results Area -->
        <section
            class="bg-surface-container-lowest border border-outline-variant rounded shadow-[0_1px_3px_rgba(0,0,0,0.05)] overflow-hidden">
            <!-- View Tabs -->
            <div class="flex border-b border-outline-variant">
                <button
                    class="px-lg py-md border-b-2 border-[#2563EB] text-[#2563EB] font-bold flex items-center gap-xs">
                    <span class="material-symbols-outlined text-[20px]">bar_chart</span>
                    Graphique
                </button>
                <button
                    class="px-lg py-md text-on-surface-variant hover:bg-surface-container-high flex items-center gap-xs">
                    <span class="material-symbols-outlined text-[20px]">table_chart</span>
                    Tableau
                </button>
                <button
                    class="px-lg py-md text-on-surface-variant hover:bg-surface-container-high flex items-center gap-xs">
                    <span class="material-symbols-outlined text-[20px]">insights</span>
                    KPI
                </button>
            </div>
            <div class="p-lg">
                <!-- Bar Chart Section -->
                <div class="mb-xl">
                    <div class="flex items-center justify-between mb-lg">
                        <h3 class="font-h2 text-h2 text-on-surface">Recettes par destination</h3>
                        <div class="flex gap-sm">
                            <div class="flex items-center gap-xs">
                                <div class="w-3 h-3 bg-[#2563EB] rounded-full"></div>
                                <span class="text-body-sm text-on-surface-variant">Revenu total</span>
                            </div>
                        </div>
                    </div>
                    <!-- Simulated Bar Chart -->
                    <div class="h-64 flex items-end gap-lg border-b border-outline-variant pb-base pt-lg">
                        <div class="flex-1 flex flex-col items-center gap-sm">
                            <div class="w-full bg-[#2563EB] opacity-90 rounded-t h-[85%] relative group">
                                <div
                                    class="absolute -top-8 left-1/2 -translate-x-1/2 bg-inverse-surface text-white text-[10px] px-sm py-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                                    85,400$</div>
                            </div>
                            <span
                                class="font-label-caps text-label-caps text-on-surface-variant text-center">Kinshasa</span>
                        </div>
                        <div class="flex-1 flex flex-col items-center gap-sm">
                            <div class="w-full bg-[#2563EB] opacity-70 rounded-t h-[60%] relative group">
                                <div
                                    class="absolute -top-8 left-1/2 -translate-x-1/2 bg-inverse-surface text-white text-[10px] px-sm py-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                                    60,200$</div>
                            </div>
                            <span
                                class="font-label-caps text-label-caps text-on-surface-variant text-center">Lubumbashi</span>
                        </div>
                        <div class="flex-1 flex flex-col items-center gap-sm">
                            <div class="w-full bg-[#2563EB] opacity-50 rounded-t h-[45%] relative group">
                                <div
                                    class="absolute -top-8 left-1/2 -translate-x-1/2 bg-inverse-surface text-white text-[10px] px-sm py-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                                    45,100$</div>
                            </div>
                            <span
                                class="font-label-caps text-label-caps text-on-surface-variant text-center">Matadi</span>
                        </div>
                        <div class="flex-1 flex flex-col items-center gap-sm">
                            <div class="w-full bg-[#2563EB] opacity-80 rounded-t h-[75%] relative group">
                                <div
                                    class="absolute -top-8 left-1/2 -translate-x-1/2 bg-inverse-surface text-white text-[10px] px-sm py-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                                    75,800$</div>
                            </div>
                            <span
                                class="font-label-caps text-label-caps text-on-surface-variant text-center">Goma</span>
                        </div>
                        <div class="flex-1 flex flex-col items-center gap-sm">
                            <div class="w-full bg-[#2563EB] opacity-40 rounded-t h-[30%] relative group">
                                <div
                                    class="absolute -top-8 left-1/2 -translate-x-1/2 bg-inverse-surface text-white text-[10px] px-sm py-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                                    30,500$</div>
                            </div>
                            <span
                                class="font-label-caps text-label-caps text-on-surface-variant text-center">Kikwit</span>
                        </div>
                    </div>
                </div>
                <!-- Pivot Table Section -->
                <div>
                    <h3 class="font-h2 text-h2 text-on-surface mb-lg">Analyse Croisée : Site vs Statut</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr
                                    class="bg-surface-container text-on-surface-variant font-label-caps text-label-caps">
                                    <th class="p-md border-b border-outline-variant">SITE / STATUT</th>
                                    <th class="p-md border-b border-outline-variant text-right">VALIDÉ</th>
                                    <th class="p-md border-b border-outline-variant text-right">EN ATTENTE</th>
                                    <th class="p-md border-b border-outline-variant text-right">TOTAL</th>
                                </tr>
                            </thead>
                            <tbody class="font-body-md">
                                <tr class="hover:bg-surface-container-low transition-colors">
                                    <td class="p-md border-b border-outline-variant font-bold">Kinshasa - Gombe</td>
                                    <td class="p-md border-b border-outline-variant text-right">45,600 $</td>
                                    <td class="p-md border-b border-outline-variant text-right">12,400 $</td>
                                    <td class="p-md border-b border-outline-variant text-right font-bold">58,000 $</td>
                                </tr>
                                <tr class="hover:bg-surface-container-low transition-colors">
                                    <td class="p-md border-b border-outline-variant font-bold">Lubumbashi</td>
                                    <td class="p-md border-b border-outline-variant text-right">32,200 $</td>
                                    <td class="p-md border-b border-outline-variant text-right">5,100 $</td>
                                    <td class="p-md border-b border-outline-variant text-right font-bold">37,300 $</td>
                                </tr>
                                <tr class="hover:bg-surface-container-low transition-colors">
                                    <td class="p-md border-b border-outline-variant font-bold">Matadi</td>
                                    <td class="p-md border-b border-outline-variant text-right">18,900 $</td>
                                    <td class="p-md border-b border-outline-variant text-right">2,300 $</td>
                                    <td class="p-md border-b border-outline-variant text-right font-bold">21,200 $</td>
                                </tr>
                                <tr class="bg-surface-container-high font-bold">
                                    <td class="p-md">GRAND TOTAL</td>
                                    <td class="p-md text-right text-[#2563EB]">96,700 $</td>
                                    <td class="p-md text-right">19,800 $</td>
                                    <td class="p-md text-right">116,500 $</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <!-- FAB Suppression Rule Applied (Not rendered on analysis/details screens) -->
</body>

</html>