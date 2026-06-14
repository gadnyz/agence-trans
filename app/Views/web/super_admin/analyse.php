<?= $this->extend($layout ?? 'web/layouts/super_admin') ?>

<?= $this->section('content') ?>
<div class="space-y-lg">
    <div class="flex justify-between items-end mb-lg">
        <div>
            <h2 class="font-headline-lg text-headline-lg text-on-surface">Analyse</h2>
            <p class="text-body-lg text-outline">Analyses croisées et graphiques de performance</p>
        </div>
        <div class="flex gap-sm">
            <button class="bg-surface-container-lowest px-md py-sm border border-outline-variant rounded-lg hover:bg-surface-container-high transition-colors flex items-center gap-xs font-body-md text-on-surface">
                <span class="material-symbols-outlined text-[18px]">download</span>
                Exporter Excel/PDF
            </button>
        </div>
    </div>
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
    </div>
</div>
<?= $this->endSection() ?>