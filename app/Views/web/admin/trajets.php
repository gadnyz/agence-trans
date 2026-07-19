<?= $this->extend($layout ?? 'web/layouts/admin') ?>

<?= $this->section('content') ?>

<main class="px-4 pb-12 max-w-[1600px] mx-auto">
    <!-- ── En-tête de page ── -->
<div class="w-full mb-6 rounded-2xl bg-white border border-gray-200 shadow-sm p-4">

    <div class="flex flex-col gap-4">

        <!-- Header principal -->
        <div class="flex items-center justify-between">

            <div class="flex items-center gap-2 md:gap-3">

                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-blue-50 text-blue-600">
                    <span class="material-symbols-outlined text-[20px]">
                        route
                    </span>
                </div>

                <h2 class="text-lg font-semibold text-gray-900 tracking-tight">
                    Gestion des Trajets
                </h2>

            </div>

            <button
                id="btn-open-modal"
                onclick="openCreateModal()"
                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition-all shadow-sm"
            >
                <span class="material-symbols-outlined text-[18px]">
                    add
                </span>

                <span>Ajouter</span>
            </button>

        </div>

    </div>

</div>


<!-- ── Filtres ── -->
<section class="mb-6 bg-white p-4 rounded-2xl border border-gray-200 shadow-sm">

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

        <div class="relative">

            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">
                search
            </span>

            <input
                id="search-input"
                type="text"
                placeholder="Ville de départ ou d'arrivée..."
                class="w-full rounded-xl border-gray-300 border pl-10 pr-4 py-2 text-sm shadow-sm focus:border-blue-500 outline-none"
            >

        </div>


        <div class="flex gap-2 md:contents">

            <select
                id="status-filter"
                class="w-full rounded-xl border-gray-300 border px-4 py-2 text-sm shadow-sm focus:border-blue-500 outline-none bg-white"
            >
                <option value="">Tous les statuts</option>
                <option value="ACTIF">ACTIF</option>
                <option value="INACTIF">INACTIF</option>
            </select>


            <div></div>


            <button
                id="btn-clear-filters"
                onclick="clearFilters()"
                class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-200 transition-all"
            >
                Réinitialiser
            </button>

        </div>

    </div>

</section>

    <!-- ── Tableau des trajets ── -->
    <section class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
            <h3 class="text-base font-semibold text-gray-900">Lignes de voyage configurées</h3>
            <span class="text-xs font-medium text-gray-500 bg-white px-2 py-1 rounded-md border border-gray-200">
                Total : <span id="trajets-count"><?= count($trajets ?? []) ?></span>
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left" id="trajets-table">
                <thead class="text-gray-600 uppercase text-[11px] font-semibold bg-gray-50">
                    <tr>
                        <th class="px-6 py-4">Itinéraire (Départ → Arrivée)</th>
                        <th class="px-6 py-4">Horaire Standard</th>
                        <th class="px-6 py-4 text-right">Tarif</th>
                        <th class="px-6 py-4 text-center">Distance</th>
                        <th class="px-6 py-4 text-center">Durée Estimée</th>
                        <th class="px-6 py-4 text-center">Statut</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100" id="trajets-tbody">
                    <?php if (empty($trajets)) : ?>
                        <tr id="empty-row">
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-3 text-gray-400">
                                    <span class="material-symbols-outlined text-[20px] md:text-[48px]">inbox</span>
                                    <p class="text-[12px] md:text-sm font-medium">Aucun trajet enregistré pour le moment</p>
                                </div>
                            </td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($trajets as $t) : ?>
                            <?php
                                $statut = strtoupper($t['statut'] ?? 'ACTIF');
                                $badgeClass = 'bg-gray-100 text-gray-600 border-gray-200';
                                if ($statut === 'ACTIF') {
                                    $badgeClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                                } elseif ($statut === 'INACTIF') {
                                    $badgeClass = 'bg-red-50 text-red-700 border-red-200';
                                }
                            ?>
                            <tr class="hover:bg-gray-50 transition-colors trajet-row"
                                data-id="<?= esc($t['id_trajet']) ?>"
                                data-depart="<?= strtolower(esc($t['lieu_depart'] ?? '')) ?>"
                                data-arrivee="<?= strtolower(esc($t['lieu_arrivee'] ?? '')) ?>"
                                data-statut="<?= esc($statut) ?>"
                            >
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-gray-900"><?= esc($t['lieu_depart']) ?></span>
                                        <span class="material-symbols-outlined text-gray-400 text-[16px]">arrow_forward</span>
                                        <span class="font-bold text-gray-900"><?= esc($t['lieu_arrivee']) ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-1 text-gray-700 text-xs font-semibold">
                                        <span class="material-symbols-outlined text-[14px] text-gray-400">schedule</span>
                                        <?= substr($t['heure_depart'] ?? '', 0, 5) ?>
                                        <span class="text-gray-400">→</span>
                                        <?= substr($t['heure_arrivee'] ?? '', 0, 5) ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right font-bold text-blue-600">
                                    <?= number_format($t['prix'], 2, '.', ' ') ?>
                                    <span class="text-gray-500 font-normal text-xs ml-0.5"><?= esc($t['symbole'] ?? '') ?></span>
                                </td>
                                <td class="px-6 py-4 text-center text-gray-700 font-medium">
                                    <?= $t['distance_km'] ? esc($t['distance_km']) . ' km' : '—' ?>
                                </td>
                                <td class="px-6 py-4 text-center text-gray-500 text-xs font-medium">
                                    <?= esc($t['duree_estimee'] ?? '—') ?>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2.5 py-1 text-[11px] font-semibold rounded-full <?= $badgeClass ?>">
                                        <?= esc(ucfirst(strtolower($t['statut'] ?? 'Actif'))) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <button
                                            title="Modifier"
                                            onclick="openEditModal(<?= esc(json_encode($t)) ?>)"
                                            class="p-1.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                        >
                                            <span class="material-symbols-outlined text-[18px]">edit</span>
                                        </button>
                                        <button
                                            title="Supprimer"
                                            onclick="deleteTrajet(<?= (int)$t['id_trajet'] ?>, '<?= esc($t['lieu_depart']) ?> → <?= esc($t['lieu_arrivee']) ?>')"
                                            class="p-1.5 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                        >
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<!-- ══════════════════════════════════════════════════
     MODAL — Trajet Form (Création / Edition)
     ══════════════════════════════════════════════════ -->
<div id="trajetModal" class="fixed inset-0 z-50 hidden bg-gray-900/50 backdrop-blur-sm overflow-y-auto" role="dialog" aria-modal="true" aria-labelledby="modal-title">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 sm:p-0">
        <div class="relative bg-white rounded-2xl text-left overflow-hidden shadow-xl sm:my-8 sm:max-w-2xl w-full">

            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[18px]" id="modal-icon">route</span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900" id="modal-title">Nouveau Trajet</h3>
                </div>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-500 hover:bg-gray-100 p-1.5 rounded-full transition-colors">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <!-- Erreurs du formulaire -->
            <div id="form-errors" class="hidden mx-6 mt-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm font-medium"></div>

            <div class="px-6 py-6 space-y-5" id="modal-content">
                <input type="hidden" id="id_trajet" name="id_trajet">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Lieu de départ -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Lieu de départ <span class="text-red-500">*</span>
                        </label>
                        <select id="id_lieu_depart" name="id_lieu_depart" class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm shadow-sm focus:border-blue-500 outline-none bg-white" required>
                            <option value="">Sélectionner le départ</option>
                            <?php foreach ($lieux as $l) : ?>
                                <option value="<?= esc($l['id_lieu']) ?>"><?= esc($l['nom_lieu']) ?> (<?= esc($l['province'] ?? '') ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Lieu d'arrivée -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Lieu d'arrivée <span class="text-red-500">*</span>
                        </label>
                        <select id="id_lieu_arrivee" name="id_lieu_arrivee" class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm shadow-sm focus:border-blue-500 outline-none bg-white" required>
                            <option value="">Sélectionner la destination</option>
                            <?php foreach ($lieux as $l) : ?>
                                <option value="<?= esc($l['id_lieu']) ?>"><?= esc($l['nom_lieu']) ?> (<?= esc($l['province'] ?? '') ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Horaire -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Créneau Horaire <span class="text-red-500">*</span>
                        </label>
                        <select id="id_horaire" name="id_horaire" class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm shadow-sm focus:border-blue-500 outline-none bg-white" required>
                            <option value="">Sélectionner un créneau</option>
                            <?php foreach ($horaires as $h) : ?>
                                <option value="<?= esc($h['id_horaire']) ?>"><?= substr($h['heure_depart'], 0, 5) ?> - <?= substr($h['heure_arrivee'], 0, 5) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Devise -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Devise de facturation <span class="text-red-500">*</span>
                        </label>
                        <select id="id_currency" name="id_currency" class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm shadow-sm focus:border-blue-500 outline-none bg-white" required>
                            <option value="">Sélectionner la devise</option>
                            <?php foreach ($currencies as $c) : ?>
                                <option value="<?= esc($c['id_currency']) ?>"><?= esc($c['code_currency']) ?> (<?= esc($c['symbole']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Prix -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Prix du billet <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="prix"
                            type="number"
                            step="0.01"
                            min="0"
                            placeholder="ex: 25.00"
                            class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm shadow-sm focus:border-blue-500 outline-none"
                            required
                        >
                    </div>

                    <!-- Distance en KM -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Distance (en KM)</label>
                        <input
                            id="distance_km"
                            type="number"
                            step="0.1"
                            min="0"
                            placeholder="ex: 120"
                            class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm shadow-sm focus:border-blue-500 outline-none"
                        >
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Durée Estimée -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Durée estimée</label>
                        <input
                            id="duree_estimee"
                            type="text"
                            placeholder="ex: 2h 30m"
                            class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm shadow-sm focus:border-blue-500 outline-none"
                        >
                    </div>

                    <!-- Statut -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Statut</label>
                        <select
                            id="statut"
                            class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm shadow-sm focus:border-blue-500 outline-none bg-white"
                        >
                            <option value="Actif">Actif</option>
                            <option value="Inactif">Inactif</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100 flex justify-end gap-3">
                <button
                    type="button"
                    onclick="closeModal()"
                    class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-50 transition-all"
                >
                    Annuler
                </button>
                <button
                    type="button"
                    id="btn-submit-trajet"
                    onclick="submitTrajet()"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 shadow-sm transition-all"
                >
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    <span id="btn-submit-text">Enregistrer</span>
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
const API_TOKEN = <?= json_encode((string)($api_token ?? ''), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
const BASE_URL  = '<?= base_url() ?>';

// ── Helpers fetch ──────────────────────────────────────────────────────────
async function apiFetch(url, options = {}) {
    const headers = { Accept: 'application/json', Authorization: `Bearer ${API_TOKEN}`, ...(options.headers || {}) };
    const response = await fetch(url, { ...options, headers });
    return response;
}

function showAlert(message, type = 'success') {
    const el = document.getElementById('page-alert');
    el.textContent = '';
    el.className = 'mb-4 p-4 rounded-xl text-sm font-medium border flex items-center gap-2 transition-all';
    
    let icon = 'check_circle';
    if (type === 'success') {
        el.classList.add('bg-green-50', 'border-green-200', 'text-green-700');
    } else {
        el.classList.add('bg-red-50', 'border-red-200', 'text-red-700');
        icon = 'error';
    }
    
    el.innerHTML = `<span class="material-symbols-outlined text-[18px]">${icon}</span> <span>${message}</span>`;
    el.classList.remove('hidden');
    
    setTimeout(() => {
        el.classList.add('hidden');
    }, 5000);
}

// ── Modal Actions ──────────────────────────────────────────────────────────
function openCreateModal() {
    clearForm();
    document.getElementById('modal-title').textContent = 'Nouveau Trajet';
    document.getElementById('btn-submit-text').textContent = 'Enregistrer';
    document.getElementById('trajetModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function openEditModal(t) {
    clearForm();
    document.getElementById('modal-title').textContent = 'Modifier le Trajet';
    document.getElementById('btn-submit-text').textContent = 'Mettre à jour';
    
    document.getElementById('id_trajet').value = t.id_trajet;
    document.getElementById('id_lieu_depart').value = t.id_lieu_depart;
    document.getElementById('id_lieu_arrivee').value = t.id_lieu_arrivee;
    document.getElementById('id_horaire').value = t.id_horaire;
    document.getElementById('id_currency').value = t.id_currency;
    document.getElementById('prix').value = t.prix;
    document.getElementById('distance_km').value = t.distance_km ?? '';
    document.getElementById('duree_estimee').value = t.duree_estimee ?? '';
    document.getElementById('statut').value = t.statut ?? 'Actif';
    
    document.getElementById('trajetModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    document.getElementById('trajetModal').classList.add('hidden');
    document.body.style.overflow = '';
    clearFormErrors();
}

function clearForm() {
    document.getElementById('id_trajet').value = '';
    document.getElementById('id_lieu_depart').value = '';
    document.getElementById('id_lieu_arrivee').value = '';
    document.getElementById('id_horaire').value = '';
    document.getElementById('id_currency').value = '';
    document.getElementById('prix').value = '';
    document.getElementById('distance_km').value = '';
    document.getElementById('duree_estimee').value = '';
    document.getElementById('statut').value = 'Actif';
    clearFormErrors();
}

function clearFormErrors() {
    const el = document.getElementById('form-errors');
    el.textContent = '';
    el.classList.add('hidden');
}

function showFormErrors(messages) {
    const el = document.getElementById('form-errors');
    el.innerHTML = '<ul class="list-disc pl-5 space-y-1">' + messages.map(m => `<li>${m}</li>`).join('') + '</ul>';
    el.classList.remove('hidden');
}

// ── Submit logic ───────────────────────────────────────────────────────────
async function submitTrajet() {
    clearFormErrors();
    const btn = document.getElementById('btn-submit-trajet');
    btn.disabled = true;
    btn.classList.add('opacity-60', 'cursor-wait');

    const id = document.getElementById('id_trajet').value;
    const isEdit = !!id;

    const payload = {
        id_lieu_depart:  parseInt(document.getElementById('id_lieu_depart').value) || null,
        id_lieu_arrivee: parseInt(document.getElementById('id_lieu_arrivee').value) || null,
        id_horaire:      parseInt(document.getElementById('id_horaire').value) || null,
        id_currency:     parseInt(document.getElementById('id_currency').value) || null,
        prix:            parseFloat(document.getElementById('prix').value) || null,
        distance_km:     parseFloat(document.getElementById('distance_km').value) || null,
        duree_estimee:   document.getElementById('duree_estimee').value.trim() || null,
        statut:          document.getElementById('statut').value,
    };

    const errors = [];
    if (!payload.id_lieu_depart) errors.push('Le lieu de départ est requis.');
    if (!payload.id_lieu_arrivee) errors.push('Le lieu d\'arrivée est requis.');
    if (payload.id_lieu_depart === payload.id_lieu_arrivee) errors.push('Le départ et l\'arrivée doivent être différents.');
    if (!payload.id_horaire) errors.push('Le créneau horaire est requis.');
    if (!payload.id_currency) errors.push('La devise est requise.');
    if (payload.prix === null || payload.prix < 0) errors.push('Le prix du billet est requis et doit être positif.');
    
    if (errors.length) {
        showFormErrors(errors);
        btn.disabled = false;
        btn.classList.remove('opacity-60', 'cursor-wait');
        return;
    }

    const url = isEdit ? `${BASE_URL}api/trajets/${id}` : `${BASE_URL}api/trajets`;
    const method = isEdit ? 'PUT' : 'POST';

    try {
        const response = await apiFetch(url, {
            method: method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload),
        });
        const json = await response.json();
        
        if (!response.ok || json.success === false) {
            const errs = json.errors ? Object.values(json.errors).flat() : [json.message || 'Une erreur est survenue.'];
            showFormErrors(errs);
            return;
        }

        closeModal();
        showAlert(isEdit ? 'Trajet mis à jour avec succès.' : 'Trajet créé avec succès.', 'success');

        const selectDepart = document.getElementById('id_lieu_depart');
        const nameDepart = selectDepart.options[selectDepart.selectedIndex].text.split('(')[0].trim();

        const selectArrivee = document.getElementById('id_lieu_arrivee');
        const nameArrivee = selectArrivee.options[selectArrivee.selectedIndex].text.split('(')[0].trim();

        const selectHoraire = document.getElementById('id_horaire');
        const horaireText = selectHoraire.options[selectHoraire.selectedIndex].text;
        const parts = horaireText.split('-');
        const heureDepart = parts[0].trim();
        const heureArrivee = parts[1].trim();

        const selectCurrency = document.getElementById('id_currency');
        const currencyText = selectCurrency.options[selectCurrency.selectedIndex].text;
        const symboleMatch = currencyText.match(/\(([^)]+)\)/);
        const symbole = symboleMatch ? symboleMatch[1] : '';

        const t = json.data;
        t.lieu_depart = nameDepart;
        t.lieu_arrivee = nameArrivee;
        t.heure_depart = heureDepart + ':00';
        t.heure_arrivee = heureArrivee + ':00';
        t.symbole = symbole;

        const statut = (t.statut || 'ACTIF').toUpperCase();
        let badgeClass = 'bg-gray-100 text-gray-600 border-gray-200';
        if (statut === 'ACTIF') {
            badgeClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
        } else if (statut === 'INACTIF') {
            badgeClass = 'bg-red-50 text-red-700 border-red-200';
        }

        const formattedPrix = parseFloat(t.prix || 0).toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).replace(',', '.');

        if (isEdit) {
            const row = document.querySelector(`.trajet-row[data-id="${id}"]`);
            if (row) {
                row.dataset.depart = nameDepart.toLowerCase();
                row.dataset.arrivee = nameArrivee.toLowerCase();
                row.dataset.statut = statut;

                const cols = row.querySelectorAll('td');
                cols[0].innerHTML = `
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-gray-900">${escapeHtml(nameDepart)}</span>
                        <span class="material-symbols-outlined text-gray-400 text-[16px]">arrow_forward</span>
                        <span class="font-bold text-gray-900">${escapeHtml(nameArrivee)}</span>
                    </div>
                `;
                cols[1].innerHTML = `
                    <div class="flex items-center gap-1 text-gray-700 text-xs font-semibold">
                        <span class="material-symbols-outlined text-[14px] text-gray-400">schedule</span>
                        ${escapeHtml(heureDepart)}
                        <span class="text-gray-400">→</span>
                        ${escapeHtml(heureArrivee)}
                    </div>
                `;
                cols[2].innerHTML = `${formattedPrix} <span class="text-gray-500 font-normal text-xs ml-0.5">${escapeHtml(symbole)}</span>`;
                cols[3].textContent = t.distance_km ? `${escapeHtml(t.distance_km)} km` : '—';
                cols[4].textContent = t.duree_estimee || '—';
                cols[5].innerHTML = `
                    <span class="px-2.5 py-1 text-[11px] font-semibold rounded-full ${badgeClass}">
                        ${escapeHtml(t.statut.charAt(0).toUpperCase() + t.statut.slice(1).toLowerCase())}
                    </span>
                `;
                
                const editBtn = cols[6].querySelector('button[title="Modifier"]');
                if (editBtn) {
                    editBtn.setAttribute('onclick', `openEditModal(${JSON.stringify(t).replace(/"/g, '&quot;')})`);
                }
            }
        } else {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50 transition-colors trajet-row';
            tr.dataset.id = t.id_trajet;
            tr.dataset.depart = nameDepart.toLowerCase();
            tr.dataset.arrivee = nameArrivee.toLowerCase();
            tr.dataset.statut = statut;

            tr.innerHTML = `
                <td class="px-6 py-4">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-gray-900">${escapeHtml(nameDepart)}</span>
                        <span class="material-symbols-outlined text-gray-400 text-[16px]">arrow_forward</span>
                        <span class="font-bold text-gray-900">${escapeHtml(nameArrivee)}</span>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center gap-1 text-gray-700 text-xs font-semibold">
                        <span class="material-symbols-outlined text-[14px] text-gray-400">schedule</span>
                        ${escapeHtml(heureDepart)}
                        <span class="text-gray-400">→</span>
                        ${escapeHtml(heureArrivee)}
                    </div>
                </td>
                <td class="px-6 py-4 text-right font-bold text-blue-600">
                    ${formattedPrix}
                    <span class="text-gray-500 font-normal text-xs ml-0.5">${escapeHtml(symbole)}</span>
                </td>
                <td class="px-6 py-4 text-center text-gray-700 font-medium">
                    ${t.distance_km ? escapeHtml(t.distance_km) + ' km' : '—'}
                </td>
                <td class="px-6 py-4 text-center text-gray-500 text-xs font-medium">
                    ${escapeHtml(t.duree_estimee || '—')}
                </td>
                <td class="px-6 py-4 text-center">
                    <span class="px-2.5 py-1 text-[11px] font-semibold rounded-full ${badgeClass}">
                        ${escapeHtml(t.statut.charAt(0).toUpperCase() + t.statut.slice(1).toLowerCase())}
                    </span>
                </td>
                <td class="px-6 py-4 text-right">
                    <div class="flex items-center justify-end gap-1">
                        <button
                            title="Modifier"
                            onclick="openEditModal(${JSON.stringify(t).replace(/"/g, '&quot;')})"
                            class="p-1.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                        >
                            <span class="material-symbols-outlined text-[18px]">edit</span>
                        </button>
                        <button
                            title="Supprimer"
                            onclick="deleteTrajet(${parseInt(t.id_trajet)}, '${escapeHtml(nameDepart)} → ${escapeHtml(nameArrivee)}')"
                            class="p-1.5 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                        >
                            <span class="material-symbols-outlined text-[18px]">delete</span>
                        </button>
                    </div>
                </td>
            `;

            const tbody = document.getElementById('trajets-tbody');
            const emptyRow = document.getElementById('empty-row');
            if (emptyRow) emptyRow.remove();

            tbody.prepend(tr);
        }
        filterTable();
    } catch (e) {
        showFormErrors(['Une erreur réseau est survenue.']);
    } finally {
        btn.disabled = false;
        btn.classList.remove('opacity-60', 'cursor-wait');
    }
}

function escapeHtml(str) {
    if (!str) return '';
    return str.toString()
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

// ── Delete logic ───────────────────────────────────────────────────────────
async function deleteTrajet(id, description) {
    if (!confirm(`Supprimer le trajet ${description} ?`)) return;
    try {
        const response = await apiFetch(`${BASE_URL}api/trajets/${id}`, { method: 'DELETE' });
        const json = await response.json();
        
        if (!response.ok || json.success === false) {
            showAlert(json.message || 'Impossible de supprimer ce trajet.', 'error');
            return;
        }
        
        showAlert('Trajet supprimé avec succès.', 'success');
        const row = document.querySelector(`.trajet-row[data-id="${id}"]`);
        if (row) {
            row.remove();
        }
        
        const tbody = document.getElementById('trajets-tbody');
        if (tbody && tbody.querySelectorAll('.trajet-row').length === 0) {
            const emptyRow = document.createElement('tr');
            emptyRow.id = 'empty-row';
            emptyRow.innerHTML = `
                <td colspan="7" class="px-6 py-12 text-center">
                    <div class="flex flex-col items-center gap-3 text-gray-400">
                        <span class="material-symbols-outlined text-[20px] md:text-[48px]">inbox</span>
                        <p class="text-[12px] md:text-sm font-medium">Aucun trajet enregistré pour le moment</p>
                    </div>
                </td>
            `;
            tbody.appendChild(emptyRow);
        }
        filterTable();
    } catch {
        showAlert('Erreur réseau.', 'error');
    }
}

// ── Search & Filter ────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('search-input')?.addEventListener('input', filterTable);
    document.getElementById('status-filter')?.addEventListener('change', filterTable);
});

function filterTable() {
    const search = document.getElementById('search-input').value.toLowerCase().trim();
    const status = document.getElementById('status-filter').value.toUpperCase();
    const rows = document.querySelectorAll('.trajet-row');
    let visibleCount = 0;

    rows.forEach(row => {
        const matchSearch = !search || 
            row.dataset.depart.includes(search) || 
            row.dataset.arrivee.includes(search);
            
        const matchStatus = !status || row.dataset.statut === status;

        if (matchSearch && matchStatus) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    document.getElementById('trajets-count').textContent = visibleCount;
    
    const emptyRow = document.getElementById('empty-row');
    if (visibleCount === 0) {
        if (!emptyRow) {
            const tbody = document.getElementById('trajets-tbody');
            const tr = document.createElement('tr');
            tr.id = 'empty-row-search';
            tr.innerHTML = `<td colspan="7" class="px-6 py-12 text-center text-gray-400">Aucun résultat trouvé pour votre recherche</td>`;
            tbody.appendChild(tr);
        }
    } else {
        document.getElementById('empty-row-search')?.remove();
    }
}

function clearFilters() {
    document.getElementById('search-input').value = '';
    document.getElementById('status-filter').value = '';
    filterTable();
}
</script>
<?= $this->endSection() ?>
