<?= $this->extend($layout ?? 'web/layouts/recept') ?>

<?= $this->section('content') ?>

<?php
$user        = session()->get('user') ?? [];
$displayName = trim(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? '')) ?: ($user['username'] ?? 'Guichetier');
$today       = date('d/m/Y');
$todayIso    = date('Y-m-d');
?>

<div class="space-y-lg">

    <!-- ── En-tête ── -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-sm">
        <div>
            <h2 class="font-headline-lg text-headline-lg text-on-surface">Guichet — Réservations</h2>
            <p class="text-body-lg text-outline"><?= esc($today) ?> &mdash; <?= esc($displayName) ?></p>
        </div>
        <button
            id="btn-nouvelle-reservation"
            class="inline-flex items-center gap-sm px-lg py-sm bg-primary text-on-primary rounded-lg font-label-lg hover:brightness-110 transition-all shadow-sm"
        >
            <span class="material-symbols-outlined text-[20px]">add</span>
            Nouvelle réservation
        </button>
    </div>

    <!-- ── Flash/Alert global ── -->
    <div id="page-alert" class="hidden rounded-xl p-md text-sm font-medium border transition-all"></div>

    <!-- ── Recherche programme ── -->
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-lg shadow-sm">
        <h3 class="font-title-sm text-title-sm text-on-surface mb-md">Rechercher un programme de voyage</h3>
        <div class="flex flex-col sm:flex-row gap-md">
            <div class="relative flex-1">
                <span class="material-symbols-outlined absolute left-md top-1/2 -translate-y-1/2 text-outline text-[20px]">search</span>
                <input
                    id="search-programme"
                    type="text"
                    placeholder="Trajet, ville de départ..."
                    class="w-full pl-xl pr-md py-sm bg-surface-container-low border border-outline-variant rounded-lg text-body-md focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all"
                />
            </div>
            <input
                type="date"
                id="filter-date"
                value="<?= $todayIso ?>"
                class="px-md py-sm bg-surface-container-low border border-outline-variant rounded-lg text-body-md focus:ring-2 focus:ring-primary outline-none"
            />
            <button
                id="btn-search-programme"
                class="inline-flex items-center gap-sm px-lg py-sm bg-primary text-on-primary rounded-lg font-label-lg hover:brightness-110 transition-all"
            >
                <span class="material-symbols-outlined text-[20px]">search</span>
                Rechercher
            </button>
        </div>
    </div>

    <!-- ── Liste des programmes ── -->
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm overflow-hidden">
        <div class="flex justify-between items-center p-lg border-b border-outline-variant">
            <h3 class="font-title-md text-title-md text-on-surface">Programmes disponibles</h3>
            <span id="programmes-count" class="text-label-sm text-outline bg-surface-container px-sm py-xs rounded-full">0 voyage(s)</span>
        </div>

        <div id="programmes-list" class="divide-y divide-outline-variant/50">
            <!-- État initial vide -->
            <div id="programmes-empty" class="flex flex-col items-center justify-center py-xl text-outline gap-md">
                <span class="material-symbols-outlined text-[56px] text-outline-variant">directions_bus</span>
                <div class="text-center">
                    <p class="text-body-md font-medium text-on-surface">Cliquez sur "Rechercher" pour charger les programmes</p>
                    <p class="text-body-sm text-outline mt-xs">Les programmes du jour s'afficheront ici.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Réservations du jour (créées par ce guichetier) ── -->
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm overflow-hidden">
        <div class="flex justify-between items-center p-lg border-b border-outline-variant">
            <h3 class="font-title-md text-title-md text-on-surface">Mes réservations du jour</h3>
            <button
                id="btn-refresh-reservations"
                class="inline-flex items-center gap-xs px-md py-xs bg-surface-container border border-outline-variant rounded-lg text-label-sm hover:bg-surface-container-high transition-all"
            >
                <span class="material-symbols-outlined text-[16px]">refresh</span>
                Actualiser
            </button>
        </div>

        <div id="reservations-list">
            <div class="flex flex-col items-center justify-center py-xl text-outline gap-sm">
                <span class="material-symbols-outlined text-[48px] text-outline-variant">inbox</span>
                <p class="text-body-md">Aucune réservation enregistrée aujourd'hui.</p>
            </div>
        </div>
    </div>

</div>

<!-- ═══════════════════════════════════════════════════════════════
     MODAL — Nouvelle réservation
═══════════════════════════════════════════════════════════════ -->
<div id="modal-reservation" class="fixed inset-0 z-50 hidden bg-on-surface/40 backdrop-blur-sm overflow-y-auto" role="dialog" aria-modal="true" aria-labelledby="modal-res-title">
    <div class="flex items-center justify-center min-h-screen px-md pt-md pb-xl sm:p-0">
        <div class="relative bg-surface-container-lowest rounded-2xl text-left overflow-hidden shadow-2xl border border-outline-variant sm:my-lg sm:max-w-2xl w-full">

            <!-- Header modal -->
            <div class="px-lg py-md border-b border-outline-variant flex justify-between items-center bg-surface-container-low/50">
                <div class="flex items-center gap-md">
                    <div class="w-8 h-8 rounded-full bg-primary-container text-on-primary-container flex items-center justify-center">
                        <span class="material-symbols-outlined text-[18px]">confirmation_number</span>
                    </div>
                    <h3 class="font-title-md text-title-md text-on-surface" id="modal-res-title">Nouvelle réservation</h3>
                </div>
                <button id="btn-close-modal" class="text-outline hover:text-on-surface hover:bg-surface-container p-xs rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <!-- Erreurs -->
            <div id="modal-errors" class="hidden mx-lg mt-md p-md bg-error-container border border-error/30 text-on-error-container rounded-xl text-sm font-medium"></div>

            <!-- Info programme sélectionné -->
            <div id="selected-programme-info" class="hidden mx-lg mt-md p-md bg-primary-fixed rounded-xl">
                <div class="flex items-center gap-sm">
                    <span class="material-symbols-outlined text-primary text-[20px]">directions_bus</span>
                    <div>
                        <p id="programme-info-text" class="font-label-lg text-on-primary-container"></p>
                        <p id="programme-info-sub" class="text-body-sm text-on-surface-variant"></p>
                    </div>
                </div>
            </div>

            <div class="px-lg py-lg space-y-md">

                <!-- Programme -->
                <div>
                    <label class="block text-label-lg text-on-surface mb-xs">Programme <span class="text-error">*</span></label>
                    <select id="modal-id-programme" class="w-full rounded-lg border border-outline-variant px-md py-sm bg-surface-container-low text-body-md focus:ring-2 focus:ring-primary outline-none transition-all">
                        <option value="">Sélectionner un programme</option>
                    </select>
                </div>

                <!-- Client -->
                <div class="relative">
                    <label class="block text-label-lg text-on-surface mb-xs">Client <span class="text-error">*</span></label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-md top-1/2 -translate-y-1/2 text-outline text-[20px]">person_search</span>
                        <input
                            id="modal-client-search"
                            type="text"
                            placeholder="Chercher par nom ou téléphone..."
                            class="w-full pl-xl pr-md py-sm bg-surface-container-low border border-outline-variant rounded-lg text-body-md focus:ring-2 focus:ring-primary outline-none transition-all"
                            autocomplete="off"
                        >
                    </div>
                    <div id="modal-client-suggestions" class="absolute z-20 mt-xs w-full bg-surface-container-lowest border border-outline-variant rounded-xl shadow-lg hidden max-h-48 overflow-y-auto"></div>
                    <input type="hidden" id="modal-id-client">
                    <p id="modal-client-selected" class="mt-xs text-xs text-primary font-medium hidden"></p>

                    <!-- Nouveau client rapide -->
                    <button
                        id="btn-nouveau-client"
                        type="button"
                        class="mt-xs inline-flex items-center gap-xs text-label-sm text-primary hover:underline"
                    >
                        <span class="material-symbols-outlined text-[14px]">person_add</span>
                        Créer un nouveau client
                    </button>
                </div>

                <!-- Nouveau client (caché par défaut) -->
                <div id="nouveau-client-form" class="hidden bg-surface-container-low rounded-xl p-md space-y-sm border border-outline-variant">
                    <p class="text-label-lg font-semibold text-on-surface">Nouveau client</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-md">
                        <div>
                            <label class="block text-label-sm text-outline mb-xs">Nom complet <span class="text-error">*</span></label>
                            <input id="nouveau-client-nom" type="text" placeholder="Ex: Kabila Augustin" class="w-full rounded-lg border border-outline-variant px-md py-sm text-body-md focus:ring-2 focus:ring-primary outline-none bg-surface-container-lowest">
                        </div>
                        <div>
                            <label class="block text-label-sm text-outline mb-xs">Téléphone <span class="text-error">*</span></label>
                            <input id="nouveau-client-telephone" type="tel" placeholder="Ex: +243 81 234 5678" class="w-full rounded-lg border border-outline-variant px-md py-sm text-body-md focus:ring-2 focus:ring-primary outline-none bg-surface-container-lowest">
                        </div>
                    </div>
                    <button
                        id="btn-creer-client"
                        type="button"
                        class="inline-flex items-center gap-xs px-md py-sm bg-primary text-on-primary rounded-lg text-label-sm hover:brightness-110 transition-all"
                    >
                        <span class="material-symbols-outlined text-[16px]">save</span>
                        Créer le client
                    </button>
                </div>

                <!-- Nombre de places + Lieu de montée -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-md">
                    <div>
                        <label class="block text-label-lg text-on-surface mb-xs">Nombre de places <span class="text-error">*</span></label>
                        <input id="modal-nombre-places" type="number" min="1" value="1" class="w-full rounded-lg border border-outline-variant px-md py-sm bg-surface-container-low text-body-md focus:ring-2 focus:ring-primary outline-none">
                    </div>
                    <div>
                        <label class="block text-label-lg text-on-surface mb-xs">Lieu de montée</label>
                        <select id="modal-id-lieu" class="w-full rounded-lg border border-outline-variant px-md py-sm bg-surface-container-low text-body-md focus:ring-2 focus:ring-primary outline-none">
                            <option value="">Départ principal</option>
                        </select>
                    </div>
                </div>

                <!-- Paiement -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-md">
                    <div>
                        <label class="block text-label-lg text-on-surface mb-xs">Mode de paiement</label>
                        <select id="modal-id-mode-paiement" class="w-full rounded-lg border border-outline-variant px-md py-sm bg-surface-container-low text-body-md focus:ring-2 focus:ring-primary outline-none">
                            <option value="">Sans paiement immédiat</option>
                            <?php foreach ($modes_paiement ?? [] as $mp): ?>
                                <option value="<?= esc($mp['id_mode_paiement']) ?>"><?= esc($mp['libelle']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-label-lg text-on-surface mb-xs">Montant reçu</label>
                        <input id="modal-montant" type="number" min="0" step="0.01" placeholder="0.00" class="w-full rounded-lg border border-outline-variant px-md py-sm bg-surface-container-low text-body-md focus:ring-2 focus:ring-primary outline-none">
                    </div>
                </div>
            </div>

            <div class="px-lg py-md bg-surface-container-low/50 border-t border-outline-variant flex justify-end gap-sm">
                <button id="btn-annuler-modal" type="button" class="px-lg py-sm bg-surface-container border border-outline-variant text-on-surface-variant font-label-lg rounded-lg hover:bg-surface-container-high transition-all">
                    Annuler
                </button>
                <button id="btn-enregistrer-reservation" type="button" class="inline-flex items-center gap-sm px-lg py-sm bg-primary text-on-primary font-label-lg rounded-lg hover:brightness-110 shadow-sm transition-all">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    Enregistrer
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
const TODAY     = '<?= $todayIso ?>';

// ── Fetch helper ───────────────────────────────────────────────────────────
async function apiFetch(url, options = {}) {
    const headers = { Accept: 'application/json', Authorization: `Bearer ${API_TOKEN}`, ...(options.headers || {}) };
    return fetch(url, { ...options, headers });
}

// ── Alerte page ────────────────────────────────────────────────────────────
function showPageAlert(message, type = 'success') {
    const el = document.getElementById('page-alert');
    el.className = 'rounded-xl p-md text-sm font-medium border transition-all ' + (
        type === 'success'
            ? 'bg-emerald-50 border-emerald-200 text-emerald-800'
            : 'bg-red-50 border-red-200 text-red-800'
    );
    el.textContent = message;
    el.classList.remove('hidden');
    setTimeout(() => el.classList.add('hidden'), 5000);
}

// ── Modal ──────────────────────────────────────────────────────────────────
const modal = document.getElementById('modal-reservation');

function openModal(programmeId = null) {
    document.getElementById('modal-errors').classList.add('hidden');
    document.getElementById('modal-client-selected').classList.add('hidden');
    document.getElementById('modal-id-client').value = '';
    document.getElementById('modal-client-search').value = '';
    document.getElementById('modal-nombre-places').value = '1';
    document.getElementById('modal-montant').value = '';
    document.getElementById('nouveau-client-form').classList.add('hidden');

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

// ── Chargement programmes (recherche) ─────────────────────────────────────
async function searchProgrammes() {
    const date   = document.getElementById('filter-date').value || TODAY;
    const search = document.getElementById('search-programme').value.trim();
    const list   = document.getElementById('programmes-list');
    const count  = document.getElementById('programmes-count');
    const empty  = document.getElementById('programmes-empty');

    list.innerHTML = '<div class="flex justify-center items-center py-xl"><div class="animate-spin w-8 h-8 border-2 border-primary border-t-transparent rounded-full"></div></div>';

    try {
        const params = new URLSearchParams({ per_page: 50, date_debut: date, date_fin: date });
        if (search) params.set('search', search);
        const response = await apiFetch(`${BASE_URL}api/planification?${params}`);
        const json = await response.json();
        const items = json.data?.items ?? [];

        count.textContent = `${items.length} voyage(s)`;

        if (!items.length) {
            list.innerHTML = `
                <div class="flex flex-col items-center justify-center py-xl text-outline gap-md">
                    <span class="material-symbols-outlined text-[56px] text-outline-variant">directions_bus</span>
                    <div class="text-center">
                        <p class="text-body-md font-medium text-on-surface">Aucun programme pour cette date</p>
                        <p class="text-body-sm text-outline mt-xs">Sélectionnez une autre date ou contactez l'administrateur.</p>
                    </div>
                </div>`;
            return;
        }

        list.innerHTML = items.map(p => {
            const depart  = p.lieu_depart ?? '?';
            const arrivee = p.lieu_arrivee ?? '?';
            const heure   = (p.heure_depart ?? '').slice(0, 5);
            const places  = p.places_disponibles ?? 0;
            const statut  = p.statut ?? '';
            const placesClass = places > 5 ? 'text-green-600' : places > 0 ? 'text-yellow-600' : 'text-red-600';
            return `
            <div class="flex items-center justify-between p-lg hover:bg-surface-container-low transition-colors">
                <div class="flex items-center gap-md">
                    <div class="w-10 h-10 bg-primary-container text-on-primary-container rounded-lg flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined">directions_bus</span>
                    </div>
                    <div>
                        <p class="font-label-lg text-on-surface">${esc(depart)} → ${esc(arrivee)}</p>
                        <p class="text-body-sm text-outline">Départ ${esc(heure)} · Bus ${esc(p.numero_plaque ?? '?')} · <span class="${placesClass} font-medium">${places} place(s) libre(s)</span></p>
                    </div>
                </div>
                <div class="flex items-center gap-sm shrink-0">
                    <span class="hidden sm:inline px-sm py-xs bg-surface-container text-on-surface-variant text-label-sm rounded-full">${esc(statut)}</span>
                    ${places > 0
                        ? `<button onclick="openModal(${p.id_programme})" class="px-md py-sm bg-primary text-on-primary rounded-lg font-label-sm hover:brightness-110 transition-all">Réserver</button>`
                        : `<span class="px-md py-sm bg-error-container text-on-error-container rounded-lg font-label-sm">Complet</span>`
                    }
                </div>
            </div>`;
        }).join('');

    } catch (e) {
        list.innerHTML = '<div class="p-lg text-center text-error">Erreur lors du chargement. Veuillez réessayer.</div>';
    }
}

function esc(str) {
    const d = document.createElement('div');
    d.textContent = str ?? '';
    return d.innerHTML;
}

document.getElementById('btn-search-programme').addEventListener('click', searchProgrammes);
document.getElementById('filter-date').addEventListener('change', searchProgrammes);

// Charger au démarrage
window.addEventListener('load', () => searchProgrammes());

// ── Chargement programmes pour le modal ────────────────────────────────────
async function loadProgrammesModal(selectId = null) {
    const select = document.getElementById('modal-id-programme');
    const date   = document.getElementById('filter-date').value || TODAY;
    try {
        const response = await apiFetch(`${BASE_URL}api/planification?per_page=100&date_debut=${date}&date_fin=${date}`);
        const json     = await response.json();
        const items    = json.data?.items ?? [];
        select.innerHTML = '<option value="">Sélectionner un programme</option>';
        items.forEach(p => {
            const opt = document.createElement('option');
            opt.value = p.id_programme;
            const depart  = p.lieu_depart ?? '?';
            const arrivee = p.lieu_arrivee ?? '?';
            const heure   = (p.heure_depart ?? '').slice(0, 5);
            opt.textContent = `${date} | ${depart} → ${arrivee} ${heure} (${p.places_disponibles ?? 0} places)`;
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

// ── Recherche client ───────────────────────────────────────────────────────
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

// ── Créer nouveau client ───────────────────────────────────────────────────
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
    } catch {
        showModalError('Erreur réseau.');
    }
});

// ── Erreurs modal ──────────────────────────────────────────────────────────
function showModalError(message) {
    const el = document.getElementById('modal-errors');
    el.textContent = message;
    el.classList.remove('hidden');
}

// ── Enregistrer réservation ────────────────────────────────────────────────
document.getElementById('btn-enregistrer-reservation').addEventListener('click', async () => {
    document.getElementById('modal-errors').classList.add('hidden');
    const btn = document.getElementById('btn-enregistrer-reservation');
    btn.disabled = true;

    const payload = {
        id_programme:        parseInt(document.getElementById('modal-id-programme').value) || 0,
        id_client:           parseInt(document.getElementById('modal-id-client').value) || 0,
        nombre_places:       parseInt(document.getElementById('modal-nombre-places').value) || 1,
        id_lieu_reservation: parseInt(document.getElementById('modal-id-lieu').value) || null,
        id_mode_paiement:    parseInt(document.getElementById('modal-id-mode-paiement').value) || null,
        montant_paye:        parseFloat(document.getElementById('modal-montant').value) || null,
    };

    const errors = [];
    if (!payload.id_programme) errors.push('Veuillez sélectionner un programme.');
    if (!payload.id_client)    errors.push('Veuillez sélectionner un client.');
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
        loadReservationsAujourdhui();

        // Ouvrir le ticket dans un nouvel onglet
        const idReservation = json.data?.id_reservation;
        if (idReservation) {
            setTimeout(() => window.open(`${BASE_URL}recept/reservations/${idReservation}/ticket`, '_blank'), 500);
        }
    } catch {
        showModalError('Erreur réseau. Veuillez réessayer.');
    } finally {
        btn.disabled = false;
    }
});

// ── Réservations du jour ───────────────────────────────────────────────────
async function loadReservationsAujourdhui() {
    const container = document.getElementById('reservations-list');
    container.innerHTML = '<div class="flex justify-center items-center py-xl"><div class="animate-spin w-8 h-8 border-2 border-primary border-t-transparent rounded-full"></div></div>';
    try {
        const response = await apiFetch(`${BASE_URL}api/reservations?date_debut=${TODAY}&date_fin=${TODAY}&per_page=50`);
        const json     = await response.json();
        const items    = json.data?.items ?? [];

        if (!items.length) {
            container.innerHTML = `
                <div class="flex flex-col items-center justify-center py-xl text-outline gap-sm">
                    <span class="material-symbols-outlined text-[48px] text-outline-variant">inbox</span>
                    <p class="text-body-md">Aucune réservation enregistrée aujourd'hui.</p>
                </div>`;
            return;
        }

        container.innerHTML = `<div class="divide-y divide-outline-variant/50">` + items.map(r => {
            const statut = (r.statut_reservation ?? '').toUpperCase();
            const statBg = statut.includes('CONFIRM') ? 'bg-green-100 text-green-700'
                         : statut.includes('ATTENTE')  ? 'bg-yellow-100 text-yellow-700'
                         : statut.includes('ANNUL')    ? 'bg-red-100 text-red-700'
                         : 'bg-surface-container text-on-surface-variant';
            return `
            <div class="flex items-center justify-between p-lg hover:bg-surface-container-low transition-colors">
                <div class="flex items-center gap-md">
                    <div class="w-9 h-9 rounded-full bg-primary-container text-on-primary-container flex items-center justify-center font-bold text-body-sm shrink-0">
                        ${esc((r.client_nom ?? '?').charAt(0).toUpperCase())}
                    </div>
                    <div>
                        <p class="font-label-lg text-on-surface">${esc(r.reference_reservation)} · ${esc(r.client_nom ?? '')}</p>
                        <p class="text-body-sm text-outline">${esc(r.lieu_depart ?? '')} → ${esc(r.lieu_arrivee ?? '')} · ${esc(r.date_programme ?? '')}</p>
                    </div>
                </div>
                <div class="flex items-center gap-sm shrink-0">
                    <span class="px-sm py-xs text-label-sm rounded-full font-medium ${statBg}">${esc(r.statut_reservation ?? '')}</span>
                    <a href="${BASE_URL}recept/reservations/${r.id_reservation}/ticket" target="_blank"
                       class="p-xs text-outline hover:text-primary hover:bg-primary-fixed rounded-lg transition-colors"
                       title="Imprimer le billet">
                        <span class="material-symbols-outlined text-[18px]">print</span>
                    </a>
                </div>
            </div>`;
        }).join('') + `</div>`;

    } catch {
        container.innerHTML = '<div class="p-lg text-center text-error text-sm">Erreur lors du chargement.</div>';
    }
}

document.getElementById('btn-refresh-reservations').addEventListener('click', loadReservationsAujourdhui);
window.addEventListener('load', () => loadReservationsAujourdhui());
</script>
<?= $this->endSection() ?>
