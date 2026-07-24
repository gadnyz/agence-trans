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
    <div class="w-full rounded-2xl bg-white border border-gray-200 shadow-sm p-4">
        <div class="flex flex-col gap-4">
            <!-- Header principal -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 md:gap-3">
                    <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-blue-50 text-blue-600">
                        <span class="material-symbols-outlined text-[20px]">directions_bus</span>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 tracking-tight">Flotte &amp; Réseau</h2>
                    </div>
                </div>
                <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold text-gray-500 whitespace-nowrap">
                    <span class="material-symbols-outlined text-[16px]">visibility</span>
                    <span class="hidden sm:inline">Mode consultation uniquement</span>
                    <span class="sm:hidden">Lecture seule</span>
                </span>
            </div>
        </div>
    </div>

    <!-- ── Flash/Alert ── -->
    <div id="page-alert" class="hidden rounded-xl p-3 text-sm font-medium border transition-all"></div>

    <!-- ── Navigation sous-sections ── -->
    <div class="flex flex-wrap gap-2">
        <button data-section="bus" class="section-btn active-section inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold transition-all">
            <span class="material-symbols-outlined text-[18px]">directions_bus</span>
            Bus
        </button>
        <button data-section="conducteurs" class="section-btn inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold transition-all">
            <span class="material-symbols-outlined text-[18px]">badge</span>
            Chauffeurs
        </button>
        <button data-section="trajets" class="section-btn inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold transition-all">
            <span class="material-symbols-outlined text-[18px]">route</span>
            Trajets
        </button>
        <button data-section="horaires" class="section-btn inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold transition-all">
            <span class="material-symbols-outlined text-[18px]">schedule</span>
            Horaires
        </button>
    </div>

    <!-- ═══ SECTION : BUS ═══ -->
    <div id="section-bus" class="section-content space-y-4">
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-blue-50 rounded-lg flex items-center justify-center">
                        <span class="material-symbols-outlined text-blue-600 text-[18px]">directions_bus</span>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Flotte de bus</h3>
                        <p class="text-xs text-gray-500">Liste complète des véhicules</p>
                    </div>
                </div>
                <button id="btn-refresh-bus" class="h-9 px-4 rounded-xl bg-white border border-gray-300 text-gray-700 flex items-center gap-2 hover:bg-gray-50 transition-all shadow-sm text-sm font-medium">
                    <span class="material-symbols-outlined text-[16px]">refresh</span> Actualiser
                </button>
            </div>
            <!-- Stats cards -->
            <div class="grid grid-cols-3 divide-x divide-gray-100 border-b border-gray-100">
                <div class="p-4 text-center">
                    <div id="bus-stat-total" class="text-xl font-bold text-gray-900">—</div>
                    <div class="text-xs text-gray-500 mt-0.5">Total bus</div>
                </div>
                <div class="p-4 text-center">
                    <div id="bus-stat-active" class="text-xl font-bold text-emerald-600">—</div>
                    <div class="text-xs text-gray-500 mt-0.5">En service</div>
                </div>
                <div class="p-4 text-center">
                    <div id="bus-stat-places" class="text-xl font-bold text-blue-600">—</div>
                    <div class="text-xs text-gray-500 mt-0.5">Capacité totale</div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-gray-600 uppercase text-[11px] font-semibold bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4">Plaque</th>
                            <th class="px-6 py-4">Marque &amp; Modèle</th>
                            <th class="px-6 py-4">Capacité</th>
                            <th class="px-6 py-4">Couleur / Année</th>
                            <th class="px-6 py-4">Statut</th>
                        </tr>
                    </thead>
                    <tbody id="bus-table-body" class="divide-y divide-gray-100">
                        <tr><td colspan="5" class="px-6 py-12 text-center text-gray-400">Chargement...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ═══ SECTION : CHAUFFEURS ═══ -->
    <div id="section-conducteurs" class="section-content hidden space-y-4">
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-emerald-50 rounded-lg flex items-center justify-center">
                        <span class="material-symbols-outlined text-emerald-600 text-[18px]">badge</span>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Chauffeurs &amp; Conducteurs</h3>
                        <p class="text-xs text-gray-500">Personnel de conduite enregistré</p>
                    </div>
                </div>
                <button id="btn-refresh-conducteurs" class="h-9 px-4 rounded-xl bg-white border border-gray-300 text-gray-700 flex items-center gap-2 hover:bg-gray-50 transition-all shadow-sm text-sm font-medium">
                    <span class="material-symbols-outlined text-[16px]">refresh</span> Actualiser
                </button>
            </div>
            <div class="grid grid-cols-2 divide-x divide-gray-100 border-b border-gray-100">
                <div class="p-4 text-center">
                    <div id="cond-stat-total" class="text-xl font-bold text-gray-900">—</div>
                    <div class="text-xs text-gray-500 mt-0.5">Total chauffeurs</div>
                </div>
                <div class="p-4 text-center">
                    <div id="cond-stat-actif" class="text-xl font-bold text-emerald-600">—</div>
                    <div class="text-xs text-gray-500 mt-0.5">Actifs</div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-gray-600 uppercase text-[11px] font-semibold bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4">Nom complet</th>
                            <th class="px-6 py-4">Téléphone</th>
                            <th class="px-6 py-4">N° Permis</th>
                            <th class="px-6 py-4">Date d'embauche</th>
                            <th class="px-6 py-4">Statut</th>
                        </tr>
                    </thead>
                    <tbody id="conducteurs-table-body" class="divide-y divide-gray-100">
                        <tr><td colspan="5" class="px-6 py-12 text-center text-gray-400">Chargement...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ═══ SECTION : TRAJETS ═══ -->
    <div id="section-trajets" class="section-content hidden space-y-4">
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-blue-50 rounded-lg flex items-center justify-center">
                        <span class="material-symbols-outlined text-blue-600 text-[18px]">route</span>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Lignes de voyage (Trajets)</h3>
                        <p class="text-xs text-gray-500">Itinéraires configurés dans le système</p>
                    </div>
                </div>
                <button id="btn-refresh-trajets" class="h-9 px-4 rounded-xl bg-white border border-gray-300 text-gray-700 flex items-center gap-2 hover:bg-gray-50 transition-all shadow-sm text-sm font-medium">
                    <span class="material-symbols-outlined text-[16px]">refresh</span> Actualiser
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-gray-600 uppercase text-[11px] font-semibold bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4">Départ &rarr; Arrivée</th>
                            <th class="px-6 py-4">Prix standard</th>
                            <th class="px-6 py-4">Distance</th>
                            <th class="px-6 py-4">Durée estimée</th>
                            <th class="px-6 py-4">Statut</th>
                        </tr>
                    </thead>
                    <tbody id="trajets-table-body" class="divide-y divide-gray-100">
                        <tr><td colspan="5" class="px-6 py-12 text-center text-gray-400">Chargement...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ═══ SECTION : HORAIRES ═══ -->
    <div id="section-horaires" class="section-content hidden space-y-4">
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-purple-50 rounded-lg flex items-center justify-center">
                        <span class="material-symbols-outlined text-purple-600 text-[18px]">schedule</span>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Créneaux horaires standards</h3>
                        <p class="text-xs text-gray-500">Horaires de départ et d'arrivée</p>
                    </div>
                </div>
                <button id="btn-refresh-horaires" class="h-9 px-4 rounded-xl bg-white border border-gray-300 text-gray-700 flex items-center gap-2 hover:bg-gray-50 transition-all shadow-sm text-sm font-medium">
                    <span class="material-symbols-outlined text-[16px]">refresh</span> Actualiser
                </button>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 p-6" id="horaires-cards-container">
                <div class="col-span-full text-center text-gray-400 py-12">Chargement...</div>
            </div>
        </div>
    </div>

</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<style>
.section-btn {
    color: #4b5563;
    background-color: #ffffff;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}
.section-btn.active-section {
    background-color: #eff6ff;
    color: #2563eb;
    border-color: #bfdbfe;
    font-weight: 600;
}
.section-btn:not(.active-section):hover {
    background-color: #f9fafb;
    border-color: #d1d5db;
}
</style>
<script>
const API_TOKEN = <?= json_encode((string)($api_token ?? ''), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
const BASE_URL  = '<?= base_url() ?>';
const TODAY     = '<?= $todayIso ?>';

let CACHE_LOCATIONS = {};
let loaded = {};

async function apiFetch(url, options = {}) {
    let token = (typeof API_TOKEN !== 'undefined' && API_TOKEN) ? API_TOKEN : (localStorage.getItem('access_token') || '');
    const headers = { 'Accept': 'application/json', ...(options.headers || {}) };
    if (token) headers.Authorization = `Bearer ${token}`;
    const response = await fetch(url, { ...options, credentials: 'same-origin', headers });
    if (response.status === 401) window.location.href = BASE_URL;
    return response;
}

function esc(str) {
    const d = document.createElement('div');
    d.textContent = str ?? '';
    return d.innerHTML;
}

// ── Section switching ───────────────────────────────────────────────────────
function switchSection(sectionId) {
    document.querySelectorAll('.section-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.section-btn').forEach(btn => btn.classList.remove('active-section'));
    document.getElementById(`section-${sectionId}`).classList.remove('hidden');
    document.querySelector(`[data-section="${sectionId}"]`).classList.add('active-section');

    if (!loaded[sectionId]) {
        if (sectionId === 'bus')         loadBus();
        if (sectionId === 'conducteurs') loadConducteurs();
        if (sectionId === 'trajets')     loadTrajets();
        if (sectionId === 'horaires')    loadHoraires();
        loaded[sectionId] = true;
    }
}

document.querySelectorAll('.section-btn').forEach(btn => {
    btn.addEventListener('click', () => switchSection(btn.getAttribute('data-section')));
});

// ── Chargement Lieux (cache global) ────────────────────────────────────────
async function initCacheLieux() {
    try {
        const res = await apiFetch(`${BASE_URL}api/lieux?per_page=200`);
        const json = await res.json();
        (json.data?.items ?? []).forEach(l => { CACHE_LOCATIONS[l.id_lieu] = l.nom_lieu; });
    } catch {}
}

// ── BUS ────────────────────────────────────────────────────────────────────
async function loadBus(force = false) {
    const body = document.getElementById('bus-table-body');
    body.innerHTML = '<tr><td colspan="5" class="p-xl text-center text-outline"><div class="inline-block animate-spin w-5 h-5 border-2 border-primary border-t-transparent rounded-full"></div></td></tr>';
    try {
        const res = await apiFetch(`${BASE_URL}api/bus?per_page=200`);
        const json = await res.json();
        const items = json.data?.items ?? [];

        document.getElementById('bus-stat-total').textContent = items.length;
        document.getElementById('bus-stat-active').textContent = items.filter(b => (b.statut ?? '').toLowerCase() !== 'inactif').length;
        document.getElementById('bus-stat-places').textContent = items.reduce((s, b) => s + (parseInt(b.nombre_places) || 0), 0);

        if (!items.length) {
            body.innerHTML = '<tr><td colspan="5" class="px-6 py-8 text-center text-gray-500 font-medium">Aucun bus répertorié.</td></tr>';
            return;
        }
        body.innerHTML = items.map(b => {
            const isActif = (b.statut ?? 'Actif').toLowerCase() !== 'inactif';
            const statutClass = isActif ? 'bg-green-50 text-green-700 border-green-200' : 'bg-gray-50 text-gray-600 border-gray-200';
            return `
            <tr class="hover:bg-gray-50/50 transition-colors">
                <td class="px-6 py-4 font-semibold font-mono text-gray-900">${esc(b.numero_plaque ?? '—')}</td>
                <td class="px-6 py-4 text-gray-900">${esc(b.marque ?? '—')} <span class="text-gray-500">${esc(b.modele ?? '')}</span></td>
                <td class="px-6 py-4 font-medium text-gray-900">${esc(b.nombre_places ?? '0')} <span class="text-xs text-gray-500">sièges</span></td>
                <td class="px-6 py-4 text-xs text-gray-600">${esc(b.couleur ?? '—')} / ${esc(b.annee ?? '—')}</td>
                <td class="px-6 py-4"><span class="px-2.5 py-0.5 text-xs font-semibold rounded-full border ${statutClass}">${esc(b.statut ?? 'Actif')}</span></td>
            </tr>`;
        }).join('');
    } catch {
        body.innerHTML = '<tr><td colspan="5" class="px-6 py-8 text-center text-red-600">Erreur de chargement.</td></tr>';
    }
}

document.getElementById('btn-refresh-bus').addEventListener('click', () => { loaded.bus = false; loadBus(); });

// ── CHAUFFEURS ──────────────────────────────────────────────────────────────
async function loadConducteurs() {
    const body = document.getElementById('conducteurs-table-body');
    body.innerHTML = '<tr><td colspan="5" class="px-6 py-8 text-center"><div class="inline-block animate-spin w-5 h-5 border-2 border-blue-600 border-t-transparent rounded-full"></div></td></tr>';
    try {
        const res = await apiFetch(`${BASE_URL}api/conducteurs?per_page=200`);
        const json = await res.json();
        const items = json.data?.items ?? [];

        document.getElementById('cond-stat-total').textContent = items.length;
        document.getElementById('cond-stat-actif').textContent = items.filter(c => (c.statut ?? '').toLowerCase() !== 'inactif').length;

        if (!items.length) {
            body.innerHTML = '<tr><td colspan="5" class="px-6 py-8 text-center text-gray-500 font-medium">Aucun chauffeur.</td></tr>';
            return;
        }
        body.innerHTML = items.map(c => {
            const isActif = (c.statut ?? 'Actif').toLowerCase() !== 'inactif';
            const statutClass = isActif ? 'bg-green-50 text-green-700 border-green-200' : 'bg-gray-50 text-gray-600 border-gray-200';
            return `
            <tr class="hover:bg-gray-50/50 transition-colors">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center text-xs font-bold shrink-0">
                            ${esc((c.prenom ?? ' ')[0].toUpperCase())}${esc((c.nom ?? ' ')[0].toUpperCase())}
                        </div>
                        <span class="font-medium text-gray-900">${esc(c.prenom ?? '')} ${esc(c.nom ?? '')} ${esc(c.postnom ?? '')}</span>
                    </div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">${esc(c.telephone ?? '—')}</td>
                <td class="px-6 py-4 font-mono text-xs text-gray-600">${esc(c.numero_permis ?? '—')}</td>
                <td class="px-6 py-4 text-xs text-gray-500">${esc(c.date_embauche ?? '—')}</td>
                <td class="px-6 py-4"><span class="px-2.5 py-0.5 text-xs font-semibold rounded-full border ${statutClass}">${esc(c.statut ?? 'Actif')}</span></td>
            </tr>`;
        }).join('');
    } catch {
        body.innerHTML = '<tr><td colspan="5" class="px-6 py-8 text-center text-red-600">Erreur de chargement.</td></tr>';
    }
}

document.getElementById('btn-refresh-conducteurs').addEventListener('click', () => { loaded.conducteurs = false; loadConducteurs(); });

// ── TRAJETS ─────────────────────────────────────────────────────────────────
async function loadTrajets() {
    const body = document.getElementById('trajets-table-body');
    body.innerHTML = '<tr><td colspan="5" class="px-6 py-8 text-center"><div class="inline-block animate-spin w-5 h-5 border-2 border-blue-600 border-t-transparent rounded-full"></div></td></tr>';
    try {
        const res = await apiFetch(`${BASE_URL}api/trajets?per_page=200`);
        const json = await res.json();
        const items = json.data?.items ?? [];

        if (!items.length) {
            body.innerHTML = '<tr><td colspan="5" class="px-6 py-8 text-center text-gray-500 font-medium">Aucun trajet configuré.</td></tr>';
            return;
        }
        body.innerHTML = items.map(t => {
            const depart   = CACHE_LOCATIONS[t.id_lieu_depart]  ?? `Lieu #${t.id_lieu_depart}`;
            const arrivee  = CACHE_LOCATIONS[t.id_lieu_arrivee] ?? `Lieu #${t.id_lieu_arrivee}`;
            const prix     = parseFloat(t.prix ?? 0).toFixed(2);
            return `
            <tr class="hover:bg-gray-50/50 transition-colors">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-2">
                        <span class="text-gray-400">🚌</span>
                        <span class="font-medium text-gray-900">${esc(depart)}</span>
                        <span class="material-symbols-outlined text-gray-400 text-[16px]">arrow_forward</span>
                        <span class="font-medium text-gray-900">${esc(arrivee)}</span>
                    </div>
                </td>
                <td class="px-6 py-4 font-bold text-blue-600">${esc(prix)}</td>
                <td class="px-6 py-4 text-sm text-gray-600">${esc(t.distance_km ?? '—')} km</td>
                <td class="px-6 py-4 text-xs text-gray-500">${esc(t.duree_estimee ?? '—')}</td>
                <td class="px-6 py-4">
                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-gray-50 text-gray-600 border border-gray-200">${esc(t.statut ?? 'Actif')}</span>
                </td>
            </tr>`;
        }).join('');
    } catch {
        body.innerHTML = '<tr><td colspan="5" class="px-6 py-8 text-center text-red-600">Erreur de chargement.</td></tr>';
    }
}

document.getElementById('btn-refresh-trajets').addEventListener('click', () => { loaded.trajets = false; loadTrajets(); });

// ── HORAIRES ─────────────────────────────────────────────────────────────────
async function loadHoraires() {
    const container = document.getElementById('horaires-cards-container');
    container.innerHTML = '<div class="col-span-full text-center py-8"><div class="inline-block animate-spin w-5 h-5 border-2 border-blue-600 border-t-transparent rounded-full"></div></div>';
    try {
        const res = await apiFetch(`${BASE_URL}api/horaires?per_page=200`);
        const json = await res.json();
        const items = json.data?.items ?? [];

        if (!items.length) {
            container.innerHTML = '<div class="col-span-full text-center text-gray-500 font-medium py-8">Aucun horaire programmé.</div>';
            return;
        }
        container.innerHTML = items.map(h => {
            const depart  = (h.heure_depart  ?? '').slice(0, 5);
            const arrivee = (h.heure_arrivee ?? '').slice(0, 5);
            return `
            <div class="bg-white border border-gray-200 rounded-2xl p-5 flex flex-col items-center gap-3 hover:shadow-md transition-shadow">
                <span class="material-symbols-outlined text-blue-600 text-[28px]">schedule</span>
                <div class="text-center">
                    <div class="font-bold text-base text-gray-900">${esc(depart)}</div>
                    <div class="text-xs text-gray-500">Départ</div>
                </div>
                <span class="material-symbols-outlined text-gray-400 text-[16px]">arrow_downward</span>
                <div class="text-center">
                    <div class="font-bold text-base text-gray-900">${esc(arrivee)}</div>
                    <div class="text-xs text-gray-500">Arrivée</div>
                </div>
            </div>`;
        }).join('');
    } catch {
        container.innerHTML = '<div class="col-span-full text-center text-red-600 py-8">Erreur de chargement.</div>';
    }
}

document.getElementById('btn-refresh-horaires').addEventListener('click', () => { loaded.horaires = false; loadHoraires(); });

// ── Initialisation ──────────────────────────────────────────────────────────
window.addEventListener('load', async () => {
    await initCacheLieux();
    switchSection('bus'); // charge la section par défaut
});
</script>
<?= $this->endSection() ?>
