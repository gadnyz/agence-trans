<?= $this->extend($layout ?? 'web/layouts/super_admin') ?>

<?= $this->section('content') ?>
<div class="space-y-lg">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-sm mb-lg">
        <div>
            <h2 class="font-headline-lg text-headline-lg text-on-surface">Paramètres</h2>
            <p class="text-body-lg text-outline">Gestion globale de l'agence de transport</p>
        </div>
        <div class="flex gap-sm">
            <button class="bg-primary text-on-primary px-lg py-sm rounded-lg font-label-lg hover:brightness-110 active:scale-95 transition-all flex items-center gap-sm shadow-sm">
                <span class="material-symbols-outlined text-[20px]">add</span>
                Ajouter un utilisateur
            </button>
        </div>
    </div>

    <!-- Sub-navigation Tabs -->
    <div class="border-b border-outline-variant flex gap-md mb-lg">
        <a class="py-md text-primary font-bold border-b-2 border-primary px-sm transition-all" href="#">Utilisateurs</a>
        <a class="py-md text-on-surface-variant hover:text-primary px-sm transition-all" href="#">Trajets</a>
        <a class="py-md text-on-surface-variant hover:text-primary px-sm transition-all" href="#">Flotte de Bus</a>
    </div>
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
</div>
<?= $this->endSection() ?>