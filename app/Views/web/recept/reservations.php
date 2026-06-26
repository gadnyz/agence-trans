<?= $this->extend($layout ?? 'web/layouts/recept') ?>

<?= $this->section('content') ?>

<?php
$user        = session()->get('user') ?? [];
$displayName = trim(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? '')) ?: ($user['username'] ?? 'Guichetier');
$today       = date('d/m/Y');
$todayIso    = date('Y-m-d');
?>

<div class="px-4 pb-12 max-w-[1600px] mx-auto space-y-6">

    <!-- ── En-tête ── -->
    <div class="w-full min-h-[60px] py-3 px-6 rounded-2xl flex flex-col lg:flex-row lg:items-center lg:justify-between bg-white border border-gray-200 shadow-sm gap-4">
        <div class="flex items-center gap-3">
            <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-blue-50 text-blue-600">
                <span class="material-symbols-outlined text-[20px]">confirmation_number</span>
            </div>
            <div>
                <h2 class="text-lg font-semibold text-gray-900 tracking-tight">Gestion des Réservations</h2>
                <p class="text-xs text-gray-500"><?= esc($today) ?> — <?= esc($displayName) ?> (Réceptionniste)</p>
            </div>
        </div>
        <button
            id="btn-nouvelle-reservation"
            class="h-9 px-4 bg-blue-600 text-white rounded-xl text-sm font-medium hover:bg-blue-700 transition-all flex items-center justify-center gap-2 shadow-sm"
        >
            <span class="material-symbols-outlined text-[18px]">add</span>
            Nouvelle réservation
        </button>
    </div>

    <!-- ── Flash/Alert global ── -->
    <div id="page-alert" class="hidden rounded-xl p-3 text-sm font-medium border transition-all"></div>

    <!-- ── Recherche Voyage / Programme ── -->
    <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
        <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
            <span class="material-symbols-outlined text-blue-500 text-[20px]">travel_explore</span>
            Rechercher un programme de voyage
        </h3>
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">search</span>
                <input
                    id="search-programme"
                    type="text"
                    placeholder="Trajet, ville de départ ou d'arrivée..."
                    class="w-full pl-10 pr-3 h-9 bg-white border border-gray-300 rounded-xl text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
                />
            </div>
            <input
                type="date"
                id="filter-date"
                value="<?= $todayIso ?>"
                class="h-9 px-3 bg-white border border-gray-300 rounded-xl text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none"
            />
            <button
                id="btn-search-programme"
                class="h-9 px-4 bg-blue-600 text-white rounded-xl text-sm font-medium hover:bg-blue-700 transition-all flex items-center justify-center gap-2 shadow-sm"
            >
                <span class="material-symbols-outlined text-[18px]">search</span>
                Rechercher
            </button>
        </div>
    </div>

    <!-- Liste des voyages programmés -->
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="flex justify-between items-center px-6 py-4 border-b border-gray-100 bg-gray-50/50">
            <h3 class="text-base font-semibold text-gray-900">Voyages programmés disponibles</h3>
            <span id="programmes-count" class="text-xs font-semibold text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full border border-gray-200">0 voyage(s)</span>
        </div>
        <div id="programmes-list" class="divide-y divide-gray-100">
            <div class="flex flex-col items-center justify-center py-12 text-gray-400 gap-3">
                <span class="material-symbols-outlined text-[48px] text-gray-300">directions_bus</span>
                <div class="text-center">
                    <p class="text-sm font-medium text-gray-500">Recherchez un programme ci-dessus</p>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Recherche réservations ── -->
    <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
        <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
            <span class="material-symbols-outlined text-blue-500 text-[20px]">manage_search</span>
            Filtrer les réservations
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div class="relative md:col-span-2">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">search</span>
                <input
                    id="search-res-text"
                    type="text"
                    placeholder="Référence, Nom client, Téléphone..."
                    class="w-full pl-10 pr-3 h-9 bg-white border border-gray-300 rounded-xl text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
                />
            </div>
            <select id="filter-res-status" class="h-9 px-3 bg-white border border-gray-300 rounded-xl text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none">
                <option value="">Tous les statuts</option>
                <option value="EN ATTENTE">En attente</option>
                <option value="CONFIRME">Confirmé</option>
                <option value="ANNULE">Annulé</option>
            </select>
            <button
                id="btn-search-res"
                class="h-9 px-4 bg-blue-600 text-white rounded-xl text-sm font-medium hover:bg-blue-700 transition-all flex items-center justify-center gap-2 shadow-sm"
            >
                <span class="material-symbols-outlined text-[18px]">filter_list</span>
                Filtrer
            </button>
        </div>
    </div>

    <!-- ── Liste des réservations ── -->
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="flex justify-between items-center px-6 py-4 border-b border-gray-100 bg-gray-50/50">
            <h3 class="text-base font-semibold text-gray-900">Liste des réservations</h3>
            <button
                id="btn-refresh-reservations"
                class="h-9 px-4 rounded-xl bg-white border border-gray-300 text-gray-700 flex items-center gap-2 hover:bg-gray-50 transition-all shadow-sm text-sm font-medium"
            >
                <span class="material-symbols-outlined text-[16px]">refresh</span>
                Actualiser
            </button>
        </div>
        <div id="reservations-table-container" class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="text-gray-600 uppercase text-[11px] font-semibold bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">Référence</th>
                        <th class="px-6 py-4">Client</th>
                        <th class="px-6 py-4">Trajet</th>
                        <th class="px-6 py-4">Date voyage</th>
                        <th class="px-6 py-4">Places</th>
                        <th class="px-6 py-4">Statut</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="reservations-table-body" class="divide-y divide-gray-100">
                    <tr>
                        <td colspan="7" class="p-xl text-center text-outline">Aucune réservation chargée.</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div id="reservations-pagination" class="flex justify-between items-center px-6 py-3 border-t border-gray-100 bg-gray-50/50"></div>
    </div>

</div>

<!-- ═══════════════════════════════════════════════════════════════
     MODAL 1 — Nouvelle réservation
     ═══════════════════════════════════════════════════════════════ -->
<div id="modal-reservation" class="fixed inset-0 z-50 hidden bg-gray-900/40 backdrop-blur-sm overflow-y-auto" role="dialog" aria-modal="true" aria-labelledby="modal-res-title">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 sm:p-0">
        <div class="relative bg-white rounded-2xl text-left overflow-hidden shadow-2xl border border-gray-200 sm:my-8 sm:max-w-2xl w-full">

            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[18px]">confirmation_number</span>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900" id="modal-res-title">Nouvelle réservation</h3>
                </div>
                <button id="btn-close-modal" class="text-gray-400 hover:text-gray-700 hover:bg-gray-100 p-1.5 rounded-xl transition-colors">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <div id="modal-errors" class="hidden mx-6 mt-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl text-sm font-medium"></div>

            <div class="px-6 py-6 space-y-4">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Voyage / Programme <span class="text-red-500">*</span></label>
                    <select id="modal-id-programme" class="w-full rounded-xl border border-gray-300 h-9 px-3 bg-white text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                        <option value="">Sélectionner un programme</option>
                    </select>
                </div>

                <div class="relative">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Client <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">person_search</span>
                        <input
                            id="modal-client-search"
                            type="text"
                            placeholder="Chercher par nom ou téléphone..."
                            class="w-full pl-10 pr-3 h-9 bg-white border border-gray-300 rounded-xl text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
                            autocomplete="off"
                        >
                    </div>
                    <div id="modal-client-suggestions" class="absolute z-20 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg hidden max-h-48 overflow-y-auto"></div>
                    <input type="hidden" id="modal-id-client">
                    <p id="modal-client-selected" class="mt-1 text-xs text-blue-600 font-semibold hidden"></p>

                    <button
                        id="btn-nouveau-client"
                        type="button"
                        class="mt-1 inline-flex items-center gap-1 text-xs font-semibold text-blue-600 hover:text-blue-700 hover:underline"
                    >
                        <span class="material-symbols-outlined text-[14px]">person_add</span>
                        Créer un nouveau client
                    </button>
                </div>

                <div id="nouveau-client-form" class="hidden bg-gray-50 rounded-2xl p-4 space-y-3 border border-gray-200">
                    <p class="text-sm font-semibold text-gray-900">Nouveau client</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Nom complet <span class="text-red-500">*</span></label>
                            <input id="nouveau-client-nom" type="text" placeholder="Ex: Kabila Augustin" class="w-full rounded-xl border border-gray-300 h-9 px-3 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Téléphone <span class="text-red-500">*</span></label>
                            <input id="nouveau-client-telephone" type="tel" placeholder="Ex: +243 81 234 5678" class="w-full rounded-xl border border-gray-300 h-9 px-3 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white">
                        </div>
                    </div>
                    <button
                        id="btn-creer-client"
                        type="button"
                        class="h-9 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold shadow-sm transition-all inline-flex items-center gap-1"
                    >
                        <span class="material-symbols-outlined text-[16px]">save</span>
                        Créer le client
                    </button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nombre de places <span class="text-red-500">*</span></label>
                        <input id="modal-nombre-places" type="number" min="1" value="1" class="w-full rounded-xl border border-gray-300 h-9 px-3 bg-white text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Lieu de descente / Arrêt</label>
                        <select id="modal-id-lieu" class="w-full rounded-xl border border-gray-300 h-9 px-3 bg-white text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none">
                            <option value="">Départ principal</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 border-t border-gray-100 pt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Mode de paiement</label>
                        <select id="modal-id-mode-paiement" class="w-full rounded-xl border border-gray-300 h-9 px-3 bg-white text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none">
                            <option value="">Sans paiement immédiat (Réservé)</option>
                            <?php foreach ($modes_paiement ?? [] as $mp): ?>
                                <option value="<?= esc($mp['id_mode_paiement']) ?>"><?= esc($mp['libelle']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Montant payé</label>
                        <input id="modal-montant" type="number" min="0" step="0.01" placeholder="0.00" class="w-full rounded-xl border border-gray-300 h-9 px-3 bg-white text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none">
                    </div>
                </div>

                <div id="modal-ref-paiement-container" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Référence de transaction / Paiement <span class="text-red-500">*</span></label>
                    <input id="modal-ref-paiement" type="text" placeholder="Ex: MP-2309489-CDF ou N° Bordereau..." class="w-full rounded-xl border border-gray-300 h-9 px-3 bg-white text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none">
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100 flex justify-end gap-2">
                <button id="btn-annuler-modal" type="button" class="h-9 px-4 bg-white border border-gray-300 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-50 transition-all">
                    Annuler
                </button>
                <button id="btn-enregistrer-reservation" type="button" class="inline-flex items-center gap-2 h-9 px-4 bg-blue-600 text-white rounded-xl text-sm font-medium hover:bg-blue-700 transition-all shadow-sm">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    Enregistrer
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL 2 — Détail Réservation -->
<div id="modal-detail" class="fixed inset-0 z-50 hidden bg-gray-900/40 backdrop-blur-sm overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 py-4">
        <div class="relative bg-white rounded-2xl text-left overflow-hidden shadow-2xl border border-gray-200 sm:max-w-xl w-full">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-blue-600 text-[24px]">info</span>
                    <h3 class="text-base font-semibold text-gray-900">Détails de la réservation</h3>
                </div>
                <button onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-700 p-1.5 rounded-xl transition-colors">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>
            <div class="px-6 py-6 space-y-4 text-sm text-gray-600" id="detail-modal-body"></div>
            <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100 flex justify-end gap-2">
                <button onclick="closeDetailModal()" class="h-9 px-4 bg-white border border-gray-300 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-50 transition-all">
                    Fermer
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL 3 — Modifier Réservation -->
<div id="modal-edit" class="fixed inset-0 z-50 hidden bg-gray-900/40 backdrop-blur-sm overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 py-4">
        <div class="relative bg-white rounded-2xl text-left overflow-hidden shadow-2xl border border-gray-200 sm:max-w-md w-full">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h3 class="text-base font-semibold text-gray-900">Modifier la réservation</h3>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-700 p-1.5 rounded-xl transition-colors">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>
            <div id="modal-edit-errors" class="hidden mx-6 mt-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl text-sm font-medium"></div>
            <div class="px-6 py-6 space-y-4">
                <input type="hidden" id="edit-id-reservation">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nombre de places <span class="text-red-500">*</span></label>
                    <input id="edit-nombre-places" type="number" min="1" class="w-full rounded-xl border border-gray-300 h-9 px-3 bg-white text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Lieu de descente / Arrêt</label>
                    <select id="edit-id-lieu" class="w-full rounded-xl border border-gray-300 h-9 px-3 bg-white text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        <option value="">Départ principal</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Statut de la réservation <span class="text-red-500">*</span></label>
                    <select id="edit-id-statut" class="w-full rounded-xl border border-gray-300 h-9 px-3 bg-white text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none"></select>
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100 flex justify-end gap-2">
                <button onclick="closeEditModal()" class="h-9 px-4 bg-white border border-gray-300 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-50 transition-all">Annuler</button>
                <button id="btn-save-edit" class="h-9 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-medium transition-all shadow-sm">Enregistrer</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL 4 — Enregistrer un Paiement -->
<div id="modal-payment" class="fixed inset-0 z-50 hidden bg-gray-900/40 backdrop-blur-sm overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 py-4">
        <div class="relative bg-white rounded-2xl text-left overflow-hidden shadow-2xl border border-gray-200 sm:max-w-md w-full">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h3 class="text-base font-semibold text-gray-900">Enregistrer un Paiement</h3>
                <button onclick="closePaymentModal()" class="text-gray-400 hover:text-gray-700 p-1.5 rounded-xl transition-colors">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>
            <div id="modal-payment-errors" class="hidden mx-6 mt-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl text-sm font-medium"></div>
            <div class="px-6 py-6 space-y-4">
                <input type="hidden" id="pay-id-reservation">
                <div class="p-4 bg-blue-50 rounded-xl text-blue-800 text-sm font-semibold border border-blue-100">
                    Référence : <span id="pay-reservation-ref"></span><br>
                    Montant dû : <span id="pay-reservation-du"></span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Mode de paiement <span class="text-red-500">*</span></label>
                    <select id="pay-id-mode-paiement" class="w-full rounded-xl border border-gray-300 h-9 px-3 bg-white text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        <?php foreach ($modes_paiement ?? [] as $mp): ?>
                            <option value="<?= esc($mp['id_mode_paiement']) ?>"><?= esc($mp['libelle']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Montant payé <span class="text-red-500">*</span></label>
                    <input id="pay-montant" type="number" min="0" step="0.01" class="w-full rounded-xl border border-gray-300 h-9 px-3 bg-white text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none">
                </div>
                <div id="pay-ref-container">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Référence de transaction <span id="pay-ref-required-star" class="text-red-500">*</span></label>
                    <input id="pay-reference" type="text" placeholder="ID M-Pesa, Airtel Money, N° Bordereau..." class="w-full rounded-xl border border-gray-300 h-9 px-3 bg-white text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none">
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100 flex justify-end gap-2">
                <button onclick="closePaymentModal()" class="h-9 px-4 bg-white border border-gray-300 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-50 transition-all">Annuler</button>
                <button id="btn-save-payment" class="h-9 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-medium transition-all shadow-sm">Confirmer le paiement</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL 5 — Confirmer Annulation -->
<div id="modal-cancel" class="fixed inset-0 z-50 hidden bg-gray-900/40 backdrop-blur-sm overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 py-4">
        <div class="relative bg-white rounded-2xl text-left overflow-hidden shadow-2xl border border-gray-200 sm:max-w-md w-full">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                <h3 class="text-base font-semibold text-gray-900">Annuler la réservation</h3>
            </div>
            <div class="px-6 py-6 space-y-4">
                <input type="hidden" id="cancel-id-reservation">
                <p class="text-sm text-gray-600">Voulez-vous vraiment annuler la réservation <strong id="cancel-reservation-ref"></strong> ? Cette action libérera les places correspondantes.</p>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Motif d'annulation</label>
                    <textarea id="cancel-motif" rows="3" placeholder="Indiquez le motif de l'annulation..." class="w-full rounded-xl border border-gray-300 p-3 bg-white text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none"></textarea>
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100 flex justify-end gap-2">
                <button onclick="closeCancelModal()" class="h-9 px-4 bg-white border border-gray-300 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-50 transition-all">Garder</button>
                <button id="btn-confirm-cancel" class="h-9 px-4 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm font-medium transition-all shadow-sm">Annuler la réservation</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL 6 — Confirmer Suppression -->
<div id="modal-delete" class="fixed inset-0 z-50 hidden bg-gray-900/40 backdrop-blur-sm overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 py-4">
        <div class="relative bg-white rounded-2xl text-left overflow-hidden shadow-2xl border border-gray-200 sm:max-w-md w-full">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                <h3 class="text-base font-semibold text-red-600">Supprimer la réservation</h3>
            </div>
            <div class="px-6 py-6 space-y-4">
                <input type="hidden" id="delete-id-reservation">
                <p class="text-sm text-gray-600">Êtes-vous absolument sûr de vouloir supprimer définitivement la réservation <strong id="delete-reservation-ref"></strong> de la base de données ?</p>
                <p class="text-xs text-red-700 bg-red-50 p-3 border border-red-200 rounded-xl">Cette action est irréversible et supprimera également les paiements rattachés.</p>
            </div>
            <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100 flex justify-end gap-2">
                <button onclick="closeDeleteModal()" class="h-9 px-4 bg-white border border-gray-300 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-50 transition-all">Annuler</button>
                <button id="btn-confirm-delete" class="h-9 px-4 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm font-medium transition-all shadow-sm">Supprimer définitivement</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
const API_TOKEN = <?= json_encode((string)($api_token ?? ''), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
const BASE_URL  = '<?= base_url() ?>';
const TODAY     = '<?= $todayIso ?>';

let CACHE_LOCATIONS = {};
let CACHE_SCHEDULES = {};
let CACHE_STATUSES  = [];

async function apiFetch(url, options = {}) {
    let token = (typeof API_TOKEN !== 'undefined' && API_TOKEN) ? API_TOKEN : localStorage.getItem('access_token');
    const headers = { 'Accept': 'application/json', 'Authorization': `Bearer ${token}`, ...(options.headers || {}) };
    const response = await fetch(url, { ...options, headers });
    if (response.status === 401) window.location.href = BASE_URL;
    return response;
}

function showPageAlert(message, type = 'success') {
    const el = document.getElementById('page-alert');
    el.className = 'rounded-xl p-md text-sm font-medium border transition-all ' + (
        type === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-red-50 border-red-200 text-red-800'
    );
    el.textContent = message;
    el.classList.remove('hidden');
    setTimeout(() => el.classList.add('hidden'), 5000);
}

function esc(str) {
    const d = document.createElement('div');
    d.textContent = str ?? '';
    return d.innerHTML;
}

// ── INITIALISATION ──────────────────────────────────────────────────────────
async function initializeApp() {
    try {
        const resLieux = await apiFetch(`${BASE_URL}api/lieux?per_page=100`);
        const jsonLieux = await resLieux.json();
        (jsonLieux.data?.items ?? []).forEach(l => { CACHE_LOCATIONS[l.id_lieu] = l.nom_lieu; });

        const resHoraires = await apiFetch(`${BASE_URL}api/horaires?per_page=100`);
        const jsonHoraires = await resHoraires.json();
        (jsonHoraires.data?.items ?? []).forEach(h => {
            CACHE_SCHEDULES[h.id_horaire] = `${h.heure_depart.slice(0, 5)} - ${h.heure_arrivee.slice(0, 5)}`;
        });

        const resStatuts = await apiFetch(`${BASE_URL}api/reservations/statuts`);
        const jsonStatuts = await resStatuts.json();
        CACHE_STATUSES = jsonStatuts.data?.items ?? [];
        document.getElementById('edit-id-statut').innerHTML = CACHE_STATUSES.map(s =>
            `<option value="${s.id_statut_reservation}">${esc(s.libelle)}</option>`
        ).join('');
    } catch (e) { console.error('Erreur initialisation cache:', e); }

    searchProgrammes();
    loadReservations();
}

window.addEventListener('load', initializeApp);

// ── RECHERCHE PROGRAMMES ────────────────────────────────────────────────────
async function searchProgrammes() {
    const date   = document.getElementById('filter-date').value || TODAY;
    const search = document.getElementById('search-programme').value.trim();
    const list   = document.getElementById('programmes-list');
    const count  = document.getElementById('programmes-count');

    list.innerHTML = '<div class="flex justify-center items-center py-8"><div class="animate-spin w-8 h-8 border-2 border-blue-600 border-t-transparent rounded-full"></div></div>';

    try {
        const params = new URLSearchParams({ per_page: 50, date_debut: date, date_fin: date });
        if (search) params.set('search', search);
        const response = await apiFetch(`${BASE_URL}api/planification/search?${params}`);
        const json = await response.json();
        const items = json.data?.items ?? [];

        count.textContent = `${items.length} voyage(s)`;

        if (!items.length) {
            list.innerHTML = `
                <div class="flex flex-col items-center justify-center py-8 text-gray-400 gap-3">
                    <span class="material-symbols-outlined text-[48px] text-gray-300">directions_bus</span>
                    <div class="text-center">
                        <p class="text-sm font-medium text-gray-500">Aucun voyage programmé pour cette date</p>
                        <p class="text-xs text-gray-400 mt-1">Modifiez vos critères ou consultez la page Programmes.</p>
                    </div>
                </div>`;
            return;
        }

        list.innerHTML = items.map(p => {
            const depart  = p.lieu_depart ?? '?';
            const arrivee = p.lieu_arrivee ?? '?';
            const heure   = (p.heure_depart ?? '').slice(0, 5);
            const places  = p.places_disponibles ?? 0;
            const placesClass = places > 5 ? 'text-green-600' : places > 0 ? 'text-yellow-600' : 'text-red-600';
            return `
            <div class="flex items-center justify-between px-6 py-4 hover:bg-gray-50/50 transition-colors">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center shrink-0 shadow-sm">
                        <span class="material-symbols-outlined">directions_bus</span>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">${esc(depart)} → ${esc(arrivee)}</p>
                        <p class="text-xs text-gray-500 mt-0.5">Départ ${esc(heure)} · Bus ${esc(p.numero_plaque ?? '?')} · Tarif: ${esc(p.prix)} ${esc(p.code_currency)} · <span class="${placesClass} font-semibold">${places} place(s) libre(s)</span></p>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    ${places > 0
                        ? `<button onclick="openModal(${p.id_programme})" class="h-9 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold shadow-sm transition-all">Réserver</button>`
                        : `<span class="px-2.5 py-1 bg-red-50 text-red-700 border border-red-200 rounded-xl text-xs font-semibold">Complet</span>`
                    }
                </div>
            </div>`;
        }).join('');

    } catch (e) {
        list.innerHTML = '<div class="px-6 py-8 text-center text-red-600">Erreur lors du chargement. Veuillez réessayer.</div>';
    }
}

document.getElementById('btn-search-programme').addEventListener('click', searchProgrammes);
document.getElementById('filter-date').addEventListener('change', searchProgrammes);

// ── LISTE RÉSERVATIONS ───────────────────────────────────────────────────────
let currentResPage = 1;
async function loadReservations(page = 1) {
    currentResPage = page;
    const body   = document.getElementById('reservations-table-body');
    const pagin  = document.getElementById('reservations-pagination');
    const search = document.getElementById('search-res-text').value.trim();
    const status = document.getElementById('filter-res-status').value;

    body.innerHTML = '<tr><td colspan="7" class="px-6 py-8 text-center"><div class="inline-block animate-spin w-6 h-6 border-2 border-blue-600 border-t-transparent rounded-full"></div></td></tr>';

    try {
        const params = new URLSearchParams({ page, per_page: 15 });
        if (search) params.set('search', search);
        if (status) params.set('statut', status);

        const response = await apiFetch(`${BASE_URL}api/reservations?${params}`);
        const json = await response.json();
        const items = json.data?.items ?? [];
        const meta  = json.data?.meta ?? { page: 1, total_pages: 1, total: 0 };

        if (!items.length) {
            body.innerHTML = '<tr><td colspan="7" class="px-6 py-8 text-center text-gray-500 font-medium">Aucune réservation trouvée.</td></tr>';
            pagin.innerHTML = '';
            return;
        }

        body.innerHTML = items.map(r => {
            const statut = (r.statut_reservation ?? '').toUpperCase();
            const statBg = statut.includes('CONFIRM') ? 'bg-green-50 text-green-700 border-green-200'
                         : statut.includes('ATTENTE') ? 'bg-yellow-50 text-yellow-700 border-yellow-200'
                         : statut.includes('ANNUL')   ? 'bg-red-50 text-red-700 border-red-200'
                         : 'bg-gray-50 text-gray-600 border-gray-200';
            const totalPaye = parseFloat(r.montant_paye ?? 0);
            const totalDu   = parseFloat(r.nombre_places ?? 1) * parseFloat(r.prix ?? 0);
            const isPaye    = totalPaye >= totalDu;

            return `
            <tr class="hover:bg-gray-50/50 transition-colors">
                <td class="px-6 py-4 font-semibold text-gray-900">${esc(r.reference_reservation)}</td>
                <td class="px-6 py-4">
                    <div class="font-medium text-gray-900">${esc(r.client_nom)}</div>
                    <div class="text-xs text-gray-500 mt-0.5">${esc(r.client_telephone)}</div>
                </td>
                <td class="px-6 py-4 text-gray-600">${esc(r.lieu_depart)} → ${esc(r.lieu_arrivee)}</td>
                <td class="px-6 py-4 text-gray-600">${esc(r.date_programme)} · <span class="text-xs text-gray-500">${esc(r.heure_depart.slice(0, 5))}</span></td>
                <td class="px-6 py-4 text-gray-600">${esc(r.nombre_places)}</td>
                <td class="px-6 py-4"><span class="px-2.5 py-0.5 text-xs font-semibold rounded-full border ${statBg}">${esc(statut)}</span></td>
                <td class="px-6 py-4 text-right whitespace-nowrap">
                    <div class="inline-flex gap-1">
                        <button onclick="viewDetail(${r.id_reservation})" class="p-1.5 text-gray-400 hover:text-gray-900 hover:bg-gray-100 rounded-xl" title="Détails">
                            <span class="material-symbols-outlined text-[18px]">info</span>
                        </button>
                        ${statut.includes('ANNUL') ? '' : `
                            <button onclick="openEditModal(${r.id_reservation})" class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-xl" title="Modifier">
                                <span class="material-symbols-outlined text-[18px]">edit</span>
                            </button>
                            ${!isPaye ? `
                                <button onclick="openPaymentModal(${r.id_reservation}, '${esc(r.reference_reservation)}', ${totalDu - totalPaye}, '${esc(r.symbole || r.code_currency)}')" class="p-1.5 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-xl" title="Paiement">
                                    <span class="material-symbols-outlined text-[18px]">payments</span>
                                </button>
                            ` : ''}
                            <button onclick="openCancelModal(${r.id_reservation}, '${esc(r.reference_reservation)}')" class="p-1.5 text-gray-400 hover:text-yellow-600 hover:bg-yellow-50 rounded-xl" title="Annuler">
                                <span class="material-symbols-outlined text-[18px]">block</span>
                            </button>
                        `}
                        <button onclick="openDeleteModal(${r.id_reservation}, '${esc(r.reference_reservation)}')" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-xl" title="Supprimer">
                            <span class="material-symbols-outlined text-[18px]">delete</span>
                        </button>
                        <a href="${BASE_URL}recept/reservations/${r.id_reservation}/ticket" target="_blank" class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-xl inline-flex items-center justify-center" title="Imprimer billet">
                            <span class="material-symbols-outlined text-[18px]">print</span>
                        </a>
                    </div>
                </td>
            </tr>`;
        }).join('');

        let paginHtml = `<span class="text-xs text-gray-500">Total: ${meta.total} réservation(s)</span><div class="inline-flex gap-1">`;
        if (meta.page > 1) paginHtml += `<button onclick="loadReservations(${meta.page - 1})" class="px-3 py-1.5 border border-gray-300 bg-white rounded-xl hover:bg-gray-50 text-xs font-medium transition-all">Précédent</button>`;
        paginHtml += `<span class="px-3 py-1.5 text-xs font-semibold text-gray-700">Page ${meta.page} / ${meta.total_pages}</span>`;
        if (meta.page < meta.total_pages) paginHtml += `<button onclick="loadReservations(${meta.page + 1})" class="px-3 py-1.5 border border-gray-300 bg-white rounded-xl hover:bg-gray-50 text-xs font-medium transition-all">Suivant</button>`;
        paginHtml += `</div>`;
        pagin.innerHTML = paginHtml;
        pagin.innerHTML = paginHtml;

    } catch (e) {
        body.innerHTML = '<tr><td colspan="7" class="p-xl text-center text-error">Erreur lors de la récupération des réservations.</td></tr>';
    }
}

document.getElementById('btn-search-res').addEventListener('click', () => loadReservations(1));
document.getElementById('btn-refresh-reservations').addEventListener('click', () => loadReservations(currentResPage));

// ── MODAL : NOUVELLE RÉSERVATION ───────────────────────────────────────────
const modal = document.getElementById('modal-reservation');

function openModal(programmeId = null) {
    document.getElementById('modal-errors').classList.add('hidden');
    document.getElementById('modal-client-selected').classList.add('hidden');
    document.getElementById('modal-id-client').value = '';
    document.getElementById('modal-client-search').value = '';
    document.getElementById('modal-nombre-places').value = '1';
    document.getElementById('modal-montant').value = '';
    document.getElementById('modal-ref-paiement').value = '';
    document.getElementById('nouveau-client-form').classList.add('hidden');
    document.getElementById('modal-ref-paiement-container').classList.add('hidden');

    if (programmeId) {
        document.getElementById('modal-id-programme').value = programmeId;
        onProgrammeChange();
    }
    loadProgrammesModal(programmeId);
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    modal.classList.add('hidden');
    document.body.style.overflow = '';
}

document.getElementById('btn-close-modal').addEventListener('click', closeModal);
document.getElementById('btn-annuler-modal').addEventListener('click', closeModal);
document.getElementById('btn-nouvelle-reservation').addEventListener('click', () => openModal());
modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });

document.getElementById('modal-id-mode-paiement').addEventListener('change', function() {
    const refContainer = document.getElementById('modal-ref-paiement-container');
    (this.value === '2' || this.value === '3') ? refContainer.classList.remove('hidden') : refContainer.classList.add('hidden');
});

async function loadProgrammesModal(selectId = null) {
    const select = document.getElementById('modal-id-programme');
    const date   = document.getElementById('filter-date').value || TODAY;
    try {
        const response = await apiFetch(`${BASE_URL}api/planification/search?per_page=100&date_debut=${date}&date_fin=${date}`);
        const json     = await response.json();
        const items    = json.data?.items ?? [];
        select.innerHTML = '<option value="">Sélectionner un programme</option>';
        items.forEach(p => {
            const opt = document.createElement('option');
            opt.value = p.id_programme;
            opt.textContent = `${p.date_programme} | ${p.lieu_depart ?? '?'} → ${p.lieu_arrivee ?? '?'} ${(p.heure_depart ?? '').slice(0, 5)} (${p.places_disponibles ?? 0} places)`;
            opt.dataset.arrets = JSON.stringify(p.arrets ?? []);
            select.appendChild(opt);
        });
        if (selectId) { select.value = selectId; onProgrammeChange(); }
        select.addEventListener('change', onProgrammeChange);
    } catch {}
}

function onProgrammeChange() {
    const select = document.getElementById('modal-id-programme');
    const opt    = select.options[select.selectedIndex];
    const lieu   = document.getElementById('modal-id-lieu');
    lieu.innerHTML = '<option value="">Départ principal</option>';
    if (!opt || !opt.value) return;
    try {
        const arrets = JSON.parse(opt.dataset.arrets || '[]');
        arrets.forEach(a => {
            const o = document.createElement('option');
            o.value = a.id_lieu;
            o.textContent = a.nom_lieu;
            lieu.appendChild(o);
        });
    } catch {}
}

let searchTimeout = null;
document.getElementById('modal-client-search').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(async () => {
        const q = this.value.trim();
        const suggestions = document.getElementById('modal-client-suggestions');
        if (q.length < 2) { suggestions.classList.add('hidden'); return; }
        try {
            const response = await apiFetch(`${BASE_URL}api/clients?search=${encodeURIComponent(q)}&per_page=10`);
            const json     = await response.json();
            const items    = json.data?.items ?? [];
            if (!items.length) { suggestions.classList.add('hidden'); return; }
            suggestions.innerHTML = items.map(c =>
                `<div class="px-md py-sm cursor-pointer hover:bg-primary-fixed text-body-md transition-colors" onclick="selectClient(${c.id_client}, '${(c.nom ?? '').replace(/'/g, "\\'")}', '${(c.telephone ?? '').replace(/'/g, "\\'")}')">
                    <span class="font-medium">${esc(c.nom)}</span>
                    <span class="text-outline text-body-sm ml-sm">${esc(c.telephone ?? '')}</span>
                </div>`
            ).join('');
            suggestions.classList.remove('hidden');
        } catch {}
    }, 300);
});

document.addEventListener('click', (e) => {
    if (!document.getElementById('modal-client-search').contains(e.target)) {
        document.getElementById('modal-client-suggestions').classList.add('hidden');
    }
});

function selectClient(id, nom, tel) {
    document.getElementById('modal-id-client').value = id;
    document.getElementById('modal-client-search').value = `${nom}${tel ? ' — ' + tel : ''}`;
    document.getElementById('modal-client-suggestions').classList.add('hidden');
    const p = document.getElementById('modal-client-selected');
    p.textContent = `✓ ${nom}`;
    p.classList.remove('hidden');
    document.getElementById('nouveau-client-form').classList.add('hidden');
}

document.getElementById('btn-nouveau-client').addEventListener('click', () => {
    document.getElementById('nouveau-client-form').classList.toggle('hidden');
});

document.getElementById('btn-creer-client').addEventListener('click', async () => {
    const nom       = document.getElementById('nouveau-client-nom').value.trim();
    const telephone = document.getElementById('nouveau-client-telephone').value.trim();
    if (!nom || !telephone) { showModalError('Le nom et le téléphone sont requis.'); return; }
    try {
        const response = await apiFetch(`${BASE_URL}api/clients`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ nom, telephone }),
        });
        const json = await response.json();
        if (!response.ok || json.success === false) { showModalError(json.message || 'Impossible de créer le client.'); return; }
        const client = json.data;
        selectClient(client.id_client, client.nom, client.telephone);
        document.getElementById('nouveau-client-form').classList.add('hidden');
        document.getElementById('nouveau-client-nom').value = '';
        document.getElementById('nouveau-client-telephone').value = '';
    } catch { showModalError('Erreur réseau.'); }
});

function showModalError(message) {
    const el = document.getElementById('modal-errors');
    el.textContent = message;
    el.classList.remove('hidden');
}

document.getElementById('btn-enregistrer-reservation').addEventListener('click', async () => {
    document.getElementById('modal-errors').classList.add('hidden');
    const btn = document.getElementById('btn-enregistrer-reservation');
    btn.disabled = true;

    const paymentMode = parseInt(document.getElementById('modal-id-mode-paiement').value) || null;
    const refPaiement = document.getElementById('modal-ref-paiement').value.trim();

    const payload = {
        id_programme:        parseInt(document.getElementById('modal-id-programme').value) || 0,
        id_client:           parseInt(document.getElementById('modal-id-client').value) || 0,
        nombre_places:       parseInt(document.getElementById('modal-nombre-places').value) || 1,
        id_lieu_reservation: parseInt(document.getElementById('modal-id-lieu').value) || null,
        id_mode_paiement:    paymentMode,
        montant_paye:        parseFloat(document.getElementById('modal-montant').value) || null,
        payment:             {}
    };

    const errors = [];
    if (!payload.id_programme) errors.push('Veuillez sélectionner un voyage.');
    if (!payload.id_client)    errors.push('Veuillez sélectionner un client.');
    if (paymentMode && (paymentMode === 2 || paymentMode === 3)) {
        if (!refPaiement) {
            errors.push('La référence de transaction est obligatoire pour ce mode de paiement.');
        } else {
            payload.payment = { statut: 'success', reference_paiement: refPaiement };
        }
    }
    if (errors.length) { showModalError(errors.join(' ')); btn.disabled = false; return; }

    try {
        const response = await apiFetch(`${BASE_URL}api/reservations`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload),
        });
        const json = await response.json();
        if (!response.ok || json.success === false) {
            const msgs = json.errors ? Object.values(json.errors).flat() : [json.message || 'Erreur.'];
            showModalError(msgs.join(' | '));
            return;
        }
        closeModal();
        showPageAlert('Réservation enregistrée avec succès !');
        loadReservations(1);
        const idReservation = json.data?.reservation?.id_reservation;
        if (idReservation) setTimeout(() => window.open(`${BASE_URL}recept/reservations/${idReservation}/ticket`, '_blank'), 500);
    } catch { showModalError('Erreur réseau. Veuillez réessayer.'); }
    finally { btn.disabled = false; }
});

// ── MODAL : DÉTAILS ────────────────────────────────────────────────────────
async function viewDetail(id) {
    const body = document.getElementById('detail-modal-body');
    body.innerHTML = '<div class="flex justify-center items-center py-lg"><div class="animate-spin w-6 h-6 border-2 border-primary border-t-transparent rounded-full"></div></div>';
    document.getElementById('modal-detail').classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    try {
        const response = await apiFetch(`${BASE_URL}api/reservations/${id}`);
        const json = await response.json();
        const r = json.data;
        if (!response.ok || !r) { body.innerHTML = `<div class="text-error text-center font-medium">Erreur lors de la récupération des détails.</div>`; return; }

        const totalPaye = parseFloat(r.montant_paye ?? 0).toFixed(2);
        const prixFinal = (parseFloat(r.prix ?? 0) * parseInt(r.nombre_places ?? 1)).toFixed(2);
        const symb = esc(r.symbole || r.code_currency);

        body.innerHTML = `
        <div class="space-y-md">
            <div class="flex justify-between items-center bg-surface-container p-md rounded-xl">
                <div>
                    <div class="text-xs text-outline font-semibold">RÉFÉRENCE</div>
                    <div class="text-title-lg text-primary font-bold">${esc(r.reference_reservation)}</div>
                </div>
                <div class="text-right">
                    <div class="text-xs text-outline font-semibold">STATUT</div>
                    <span class="px-sm py-xs text-xs font-bold rounded-full bg-primary-container text-on-primary-container">${esc(r.statut_reservation)}</span>
                </div>
            </div>
            <div class="border border-outline-variant rounded-xl p-md">
                <h4 class="font-title-sm text-on-surface mb-sm flex items-center gap-xs"><span class="material-symbols-outlined text-[18px]">person</span> Informations Client</h4>
                <div class="grid grid-cols-2 gap-sm">
                    <div><span class="text-xs text-outline block">Nom</span><span class="font-semibold">${esc(r.client_nom)}</span></div>
                    <div><span class="text-xs text-outline block">Téléphone</span><span>${esc(r.client_telephone)}</span></div>
                </div>
            </div>
            <div class="border border-outline-variant rounded-xl p-md">
                <h4 class="font-title-sm text-on-surface mb-sm flex items-center gap-xs"><span class="material-symbols-outlined text-[18px]">directions_bus</span> Voyage & Programme</h4>
                <div class="grid grid-cols-2 gap-md text-body-sm">
                    <div><span class="text-xs text-outline block">Itinéraire</span><span class="font-semibold text-on-surface">${esc(r.lieu_depart)} → ${esc(r.lieu_arrivee)}</span></div>
                    <div><span class="text-xs text-outline block">Date & Heure</span><span>${esc(r.date_programme)} à <strong>${esc(r.heure_depart.slice(0, 5))}</strong></span></div>
                    <div><span class="text-xs text-outline block">Lieu de descente</span><span>${esc(r.lieu_reservation ?? 'Départ principal')}</span></div>
                    <div><span class="text-xs text-outline block">Bus & Chauffeur</span><span>Bus ${esc(r.numero_plaque)} · ${esc(r.conducteur_prenom)} ${esc(r.conducteur_nom)}</span></div>
                </div>
            </div>
            <div class="border border-outline-variant rounded-xl p-md">
                <h4 class="font-title-sm text-on-surface mb-sm flex items-center gap-xs"><span class="material-symbols-outlined text-[18px]">payments</span> Comptabilité & Paiement</h4>
                <div class="grid grid-cols-2 gap-md text-body-sm">
                    <div><span class="text-xs text-outline block">Places réservées</span><span class="font-semibold">${esc(r.nombre_places)} place(s)</span></div>
                    <div><span class="text-xs text-outline block">Prix final dû</span><span class="font-bold text-primary">${esc(prixFinal)} ${symb}</span></div>
                    <div><span class="text-xs text-outline block">Mode de paiement</span><span>${esc(r.mode_paiement ?? 'Non payé')}</span></div>
                    <div><span class="text-xs text-outline block">Montant payé</span><span class="font-semibold text-emerald-600">${esc(totalPaye)} ${symb}</span></div>
                    ${r.reference_paiement ? `
                    <div class="col-span-2 bg-surface-container-low p-sm rounded-lg flex justify-between">
                        <div><span class="text-[11px] text-outline block">RÉF TRANSACTION</span><span class="font-mono text-xs font-semibold">${esc(r.reference_paiement)}</span></div>
                        <div class="text-right"><span class="text-[11px] text-outline block">DATE TRANSACTION</span><span class="text-xs">${esc(r.date_paiement ?? '')}</span></div>
                    </div>` : ''}
                </div>
            </div>
            <div class="text-[11px] text-outline text-right">
                Enregistré par: ${esc(r.created_by_prenom ?? '')} ${esc(r.created_by_nom ?? r.created_by_username ?? 'Système')}
            </div>
        </div>`;
    } catch { body.innerHTML = `<div class="text-error text-center">Erreur réseau lors de la récupération des détails.</div>`; }
}

function closeDetailModal() {
    document.getElementById('modal-detail').classList.add('hidden');
    document.body.style.overflow = '';
}

// ── MODAL : MODIFIER ───────────────────────────────────────────────────────
async function openEditModal(id) {
    document.getElementById('modal-edit-errors').classList.add('hidden');
    document.getElementById('edit-id-reservation').value = id;
    const response = await apiFetch(`${BASE_URL}api/reservations/${id}`);
    const json = await response.json();
    const r = json.data;
    if (!response.ok || !r) { showPageAlert('Impossible de récupérer la réservation', 'error'); return; }
    document.getElementById('edit-nombre-places').value = r.nombre_places;
    document.getElementById('edit-id-statut').value = r.id_statut_reservation;
    const selectLieu = document.getElementById('edit-id-lieu');
    selectLieu.innerHTML = '<option value="">Départ principal</option>';
    try {
        const respArrets = await apiFetch(`${BASE_URL}api/planification/search?date_debut=${r.date_programme}&date_fin=${r.date_programme}`);
        const jsonArrets = await respArrets.json();
        const currentProg = (jsonArrets.data?.items ?? []).find(p => p.id_programme === r.id_programme);
        if (currentProg?.arrets) {
            currentProg.arrets.forEach(a => {
                const opt = document.createElement('option');
                opt.value = a.id_lieu;
                opt.textContent = a.nom_lieu;
                selectLieu.appendChild(opt);
            });
        }
        selectLieu.value = r.id_lieu_reservation ?? '';
    } catch {}
    document.getElementById('modal-edit').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeEditModal() {
    document.getElementById('modal-edit').classList.add('hidden');
    document.body.style.overflow = '';
}

document.getElementById('btn-save-edit').addEventListener('click', async () => {
    const id = document.getElementById('edit-id-reservation').value;
    const errorEl = document.getElementById('modal-edit-errors');
    errorEl.classList.add('hidden');
    const payload = {
        nombre_places: parseInt(document.getElementById('edit-nombre-places').value) || 1,
        id_lieu_reservation: parseInt(document.getElementById('edit-id-lieu').value) || null,
        id_statut_reservation: parseInt(document.getElementById('edit-id-statut').value) || null,
    };
    try {
        const response = await apiFetch(`${BASE_URL}api/reservations/${id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        const json = await response.json();
        if (!response.ok || json.success === false) {
            errorEl.textContent = json.message || 'Échec de la modification.';
            errorEl.classList.remove('hidden');
            return;
        }
        closeEditModal();
        showPageAlert('Réservation modifiée avec succès.');
        loadReservations(currentResPage);
    } catch {
        errorEl.textContent = 'Erreur réseau.';
        errorEl.classList.remove('hidden');
    }
});

// ── MODAL : PAIEMENT ────────────────────────────────────────────────────────
function openPaymentModal(id, ref, reste, devise) {
    document.getElementById('modal-payment-errors').classList.add('hidden');
    document.getElementById('pay-id-reservation').value = id;
    document.getElementById('pay-reservation-ref').textContent = ref;
    document.getElementById('pay-reservation-du').textContent = `${reste.toFixed(2)} ${devise}`;
    document.getElementById('pay-montant').value = reste.toFixed(2);
    document.getElementById('pay-reference').value = '';
    togglePayRefValidation();
    document.getElementById('modal-payment').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

document.getElementById('pay-id-mode-paiement').addEventListener('change', togglePayRefValidation);

function togglePayRefValidation() {
    const val  = document.getElementById('pay-id-mode-paiement').value;
    const star = document.getElementById('pay-ref-required-star');
    (val === '2' || val === '3') ? star.classList.remove('hidden') : star.classList.add('hidden');
}

function closePaymentModal() {
    document.getElementById('modal-payment').classList.add('hidden');
    document.body.style.overflow = '';
}

document.getElementById('btn-save-payment').addEventListener('click', async () => {
    const id        = document.getElementById('pay-id-reservation').value;
    const errorEl   = document.getElementById('modal-payment-errors');
    const mode      = parseInt(document.getElementById('pay-id-mode-paiement').value) || 0;
    const reference = document.getElementById('pay-reference').value.trim();
    const montant   = parseFloat(document.getElementById('pay-montant').value) || 0;
    errorEl.classList.add('hidden');
    if (montant <= 0) { errorEl.textContent = 'Le montant doit être supérieur à 0.'; errorEl.classList.remove('hidden'); return; }
    if ((mode === 2 || mode === 3) && !reference) { errorEl.textContent = 'Veuillez renseigner la référence de transaction.'; errorEl.classList.remove('hidden'); return; }
    try {
        const response = await apiFetch(`${BASE_URL}api/reservations/${id}/payment`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id_mode_paiement: mode, montant_paye: montant, reference_paiement: reference })
        });
        const json = await response.json();
        if (!response.ok || json.success === false) { errorEl.textContent = json.message || 'Erreur.'; errorEl.classList.remove('hidden'); return; }
        closePaymentModal();
        showPageAlert('Paiement enregistré avec succès.');
        loadReservations(currentResPage);
    } catch { errorEl.textContent = 'Erreur réseau.'; errorEl.classList.remove('hidden'); }
});

// ── MODAL : ANNULATION ─────────────────────────────────────────────────────
function openCancelModal(id, ref) {
    document.getElementById('cancel-id-reservation').value = id;
    document.getElementById('cancel-reservation-ref').textContent = ref;
    document.getElementById('cancel-motif').value = '';
    document.getElementById('modal-cancel').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeCancelModal() {
    document.getElementById('modal-cancel').classList.add('hidden');
    document.body.style.overflow = '';
}

document.getElementById('btn-confirm-cancel').addEventListener('click', async () => {
    const id    = document.getElementById('cancel-id-reservation').value;
    const motif = document.getElementById('cancel-motif').value.trim();
    try {
        const response = await apiFetch(`${BASE_URL}api/reservations/${id}/cancel`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ motif })
        });
        const json = await response.json();
        if (!response.ok || json.success === false) { showPageAlert(json.message || "Impossible d'annuler la réservation.", 'error'); return; }
        closeCancelModal();
        showPageAlert('Réservation annulée.');
        loadReservations(currentResPage);
    } catch { showPageAlert('Erreur de réseau.', 'error'); }
});

// ── MODAL : SUPPRESSION ─────────────────────────────────────────────────────
function openDeleteModal(id, ref) {
    document.getElementById('delete-id-reservation').value = id;
    document.getElementById('delete-reservation-ref').textContent = ref;
    document.getElementById('modal-delete').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeDeleteModal() {
    document.getElementById('modal-delete').classList.add('hidden');
    document.body.style.overflow = '';
}

document.getElementById('btn-confirm-delete').addEventListener('click', async () => {
    const id = document.getElementById('delete-id-reservation').value;
    try {
        const response = await apiFetch(`${BASE_URL}api/reservations/${id}`, { method: 'DELETE' });
        const json = await response.json();
        if (!response.ok || json.success === false) { showPageAlert(json.message || 'Impossible de supprimer la réservation.', 'error'); return; }
        closeDeleteModal();
        showPageAlert('La réservation a été définitivement supprimée.');
        loadReservations(currentResPage);
    } catch { showPageAlert('Erreur réseau lors de la suppression.', 'error'); }
});
</script>
<?= $this->endSection() ?>
