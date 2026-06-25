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
            <h2 class="font-headline-lg text-headline-lg text-on-surface">Flotte & Réseau</h2>
            <p class="text-body-lg text-outline">Consultation en lecture seule &mdash; <?= esc($today) ?></p>
        </div>
        <span class="inline-flex items-center gap-sm px-md py-sm bg-surface-container border border-outline-variant rounded-xl text-label-sm text-outline">
            <span class="material-symbols-outlined text-[16px]">visibility</span>
            Mode consultation uniquement
        </span>
    </div>

    <!-- ── Flash/Alert global ── -->
    <div id="page-alert" class="hidden rounded-xl p-md text-sm font-medium border transition-all"></div>

    <!-- ── Navigation sous-sections ── -->
    <div class="flex flex-wrap gap-sm">
        <button data-section="bus" class="section-btn active-section inline-flex items-center gap-sm px-lg py-sm rounded-xl font-label-lg transition-all border">
            <span class="material-symbols-outlined text-[18px]">directions_bus</span>
            Bus
        </button>
        <button data-section="conducteurs" class="section-btn inline-flex items-center gap-sm px-lg py-sm rounded-xl font-label-lg transition-all border border-transparent">
            <span class="material-symbols-outlined text-[18px]">badge</span>
            Chauffeurs
        </button>
        <button data-section="trajets" class="section-btn inline-flex items-center gap-sm px-lg py-sm rounded-xl font-label-lg transition-all border border-transparent">
            <span class="material-symbols-outlined text-[18px]">route</span>
            Trajets
        </button>
        <button data-section="horaires" class="section-btn inline-flex items-center gap-sm px-lg py-sm rounded-xl font-label-lg transition-all border border-transparent">
            <span class="material-symbols-outlined text-[18px]">schedule</span>
            Horaires
        </button>
    </div>

    <!-- ═══ SECTION : BUS ═══ -->
    <div id="section-bus" class="section-content space-y-md">
        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm overflow-hidden">
            <div class="flex justify-between items-center p-lg border-b border-outline-variant bg-surface-container-low/30">
                <div class="flex items-center gap-sm">
                    <div class="w-9 h-9 bg-primary-container rounded-lg flex items-center justify-center">
                        <span class="material-symbols-outlined text-on-primary-container text-[18px]">directions_bus</span>
                    </div>
                    <h3 class="font-title-md text-title-md text-on-surface">Flotte de bus</h3>
                </div>
                <button id="btn-refresh-bus" class="inline-flex items-center gap-xs px-md py-xs bg-surface-container border border-outline-variant rounded-lg text-label-sm hover:bg-surface-container-high transition-all">
                    <span class="material-symbols-outlined text-[16px]">refresh</span> Actualiser
                </button>
            </div>
            <!-- Stats cards -->
            <div class="grid grid-cols-3 divide-x divide-outline-variant/50 border-b border-outline-variant">
                <div class="p-md text-center">
                    <div id="bus-stat-total" class="font-bold text-title-lg text-on-surface">—</div>
                    <div class="text-xs text-outline">Total bus</div>
                </div>
                <div class="p-md text-center">
                    <div id="bus-stat-active" class="font-bold text-title-lg text-emerald-600">—</div>
                    <div class="text-xs text-outline">En service</div>
                </div>
                <div class="p-md text-center">
                    <div id="bus-stat-places" class="font-bold text-title-lg text-primary">—</div>
                    <div class="text-xs text-outline">Capacité totale</div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-body-md">
                    <thead>
                        <tr class="bg-surface-container border-b border-outline-variant text-on-surface font-semibold text-[13px]">
                            <th class="p-md">Plaque</th>
                            <th class="p-md">Marque & Modèle</th>
                            <th class="p-md">Capacité</th>
                            <th class="p-md">Couleur / Année</th>
                            <th class="p-md">Statut</th>
                        </tr>
                    </thead>
                    <tbody id="bus-table-body" class="divide-y divide-outline-variant/30">
                        <tr><td colspan="5" class="p-xl text-center text-outline">Chargement...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ═══ SECTION : CHAUFFEURS ═══ -->
    <div id="section-conducteurs" class="section-content hidden space-y-md">
        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm overflow-hidden">
            <div class="flex justify-between items-center p-lg border-b border-outline-variant bg-surface-container-low/30">
                <div class="flex items-center gap-sm">
                    <div class="w-9 h-9 bg-emerald-100 rounded-lg flex items-center justify-center">
                        <span class="material-symbols-outlined text-emerald-700 text-[18px]">badge</span>
                    </div>
                    <h3 class="font-title-md text-title-md text-on-surface">Chauffeurs & Conducteurs</h3>
                </div>
                <button id="btn-refresh-conducteurs" class="inline-flex items-center gap-xs px-md py-xs bg-surface-container border border-outline-variant rounded-lg text-label-sm hover:bg-surface-container-high transition-all">
                    <span class="material-symbols-outlined text-[16px]">refresh</span> Actualiser
                </button>
            </div>
            <div class="grid grid-cols-2 divide-x divide-outline-variant/50 border-b border-outline-variant">
                <div class="p-md text-center">
                    <div id="cond-stat-total" class="font-bold text-title-lg text-on-surface">—</div>
                    <div class="text-xs text-outline">Total chauffeurs</div>
                </div>
                <div class="p-md text-center">
                    <div id="cond-stat-actif" class="font-bold text-title-lg text-emerald-600">—</div>
                    <div class="text-xs text-outline">Actifs</div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-body-md">
                    <thead>
                        <tr class="bg-surface-container border-b border-outline-variant text-on-surface font-semibold text-[13px]">
                            <th class="p-md">Nom complet</th>
                            <th class="p-md">Téléphone</th>
                            <th class="p-md">N° Permis</th>
                            <th class="p-md">Date d'embauche</th>
                            <th class="p-md">Statut</th>
                        </tr>
                    </thead>
                    <tbody id="conducteurs-table-body" class="divide-y divide-outline-variant/30">
                        <tr><td colspan="5" class="p-xl text-center text-outline">Chargement...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ═══ SECTION : TRAJETS ═══ -->
    <div id="section-trajets" class="section-content hidden space-y-md">
        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm overflow-hidden">
            <div class="flex justify-between items-center p-lg border-b border-outline-variant bg-surface-container-low/30">
                <div class="flex items-center gap-sm">
                    <div class="w-9 h-9 bg-blue-100 rounded-lg flex items-center justify-center">
                        <span class="material-symbols-outlined text-blue-700 text-[18px]">route</span>
                    </div>
                    <h3 class="font-title-md text-title-md text-on-surface">Lignes de voyage (Trajets)</h3>
                </div>
                <button id="btn-refresh-trajets" class="inline-flex items-center gap-xs px-md py-xs bg-surface-container border border-outline-variant rounded-lg text-label-sm hover:bg-surface-container-high transition-all">
                    <span class="material-symbols-outlined text-[16px]">refresh</span> Actualiser
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-body-md">
                    <thead>
                        <tr class="bg-surface-container border-b border-outline-variant text-on-surface font-semibold text-[13px]">
                            <th class="p-md">Départ → Arrivée</th>
                            <th class="p-md">Prix standard</th>
                            <th class="p-md">Distance</th>
                            <th class="p-md">Durée estimée</th>
                            <th class="p-md">Statut</th>
                        </tr>
                    </thead>
                    <tbody id="trajets-table-body" class="divide-y divide-outline-variant/30">
                        <tr><td colspan="5" class="p-xl text-center text-outline">Chargement...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ═══ SECTION : HORAIRES ═══ -->
    <div id="section-horaires" class="section-content hidden space-y-md">
        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm overflow-hidden">
            <div class="flex justify-between items-center p-lg border-b border-outline-variant bg-surface-container-low/30">
                <div class="flex items-center gap-sm">
                    <div class="w-9 h-9 bg-purple-100 rounded-lg flex items-center justify-center">
                        <span class="material-symbols-outlined text-purple-700 text-[18px]">schedule</span>
                    </div>
                    <h3 class="font-title-md text-title-md text-on-surface">Créneaux horaires standards</h3>
                </div>
                <button id="btn-refresh-horaires" class="inline-flex items-center gap-xs px-md py-xs bg-surface-container border border-outline-variant rounded-lg text-label-sm hover:bg-surface-container-high transition-all">
                    <span class="material-symbols-outlined text-[16px]">refresh</span> Actualiser
                </button>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-md p-lg" id="horaires-cards-container">
                <div class="col-span-full text-center text-outline p-xl">Chargement...</div>
            </div>
        </div>
    </div>

</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<style>
.section-btn {
    color: var(--color-on-surface-variant);
    background: transparent;
}
.section-btn.active-section {
    background-color: var(--color-primary-container);
    color: var(--color-on-primary-container);
    border-color: var(--color-primary);
    font-weight: 600;
}
.section-btn:not(.active-section):hover {
    background-color: var(--color-surface-container);
}
</style>
<script>
const API_TOKEN = <?= json_encode((string)($api_token ?? ''), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
const BASE_URL  = '<?= base_url() ?>';
const TODAY     = '<?= $todayIso ?>';

let CACHE_LOCATIONS = {};
let loaded = {};

async function apiFetch(url, options = {}) {
    let token = (typeof API_TOKEN !== 'undefined' && API_TOKEN) ? API_TOKEN : localStorage.getItem('access_token');
    const headers = { 'Accept': 'application/json', 'Authorization': `Bearer ${token}`, ...(options.headers || {}) };
    const response = await fetch(url, { ...options, headers });
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
            body.innerHTML = '<tr><td colspan="5" class="p-xl text-center text-outline font-medium">Aucun bus répertorié.</td></tr>';
            return;
        }
        body.innerHTML = items.map(b => {
            const isActif = (b.statut ?? 'Actif').toLowerCase() !== 'inactif';
            const statutClass = isActif ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600';
            return `
            <tr class="hover:bg-surface-container-low transition-colors">
                <td class="p-md font-semibold font-mono">${esc(b.numero_plaque ?? '—')}</td>
                <td class="p-md">${esc(b.marque ?? '—')} <span class="text-outline">${esc(b.modele ?? '')}</span></td>
                <td class="p-md font-medium">${esc(b.nombre_places ?? '0')} <span class="text-xs text-outline">sièges</span></td>
                <td class="p-md text-xs text-on-surface-variant">${esc(b.couleur ?? '—')} / ${esc(b.annee ?? '—')}</td>
                <td class="p-md"><span class="px-sm py-xs text-xs font-semibold rounded-full ${statutClass}">${esc(b.statut ?? 'Actif')}</span></td>
            </tr>`;
        }).join('');
    } catch {
        body.innerHTML = '<tr><td colspan="5" class="p-xl text-center text-error">Erreur de chargement.</td></tr>';
    }
}

document.getElementById('btn-refresh-bus').addEventListener('click', () => { loaded.bus = false; loadBus(); });

// ── CHAUFFEURS ──────────────────────────────────────────────────────────────
async function loadConducteurs() {
    const body = document.getElementById('conducteurs-table-body');
    body.innerHTML = '<tr><td colspan="5" class="p-xl text-center text-outline"><div class="inline-block animate-spin w-5 h-5 border-2 border-primary border-t-transparent rounded-full"></div></td></tr>';
    try {
        const res = await apiFetch(`${BASE_URL}api/conducteurs?per_page=200`);
        const json = await res.json();
        const items = json.data?.items ?? [];

        document.getElementById('cond-stat-total').textContent = items.length;
        document.getElementById('cond-stat-actif').textContent = items.filter(c => (c.statut ?? '').toLowerCase() !== 'inactif').length;

        if (!items.length) {
            body.innerHTML = '<tr><td colspan="5" class="p-xl text-center text-outline font-medium">Aucun chauffeur.</td></tr>';
            return;
        }
        body.innerHTML = items.map(c => {
            const isActif = (c.statut ?? 'Actif').toLowerCase() !== 'inactif';
            const statutClass = isActif ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600';
            return `
            <tr class="hover:bg-surface-container-low transition-colors">
                <td class="p-md">
                    <div class="flex items-center gap-sm">
                        <div class="w-8 h-8 bg-primary-container text-on-primary-container rounded-full flex items-center justify-center text-xs font-bold shrink-0">
                            ${esc((c.prenom ?? ' ')[0].toUpperCase())}${esc((c.nom ?? ' ')[0].toUpperCase())}
                        </div>
                        <span class="font-medium">${esc(c.prenom ?? '')} ${esc(c.nom ?? '')} ${esc(c.postnom ?? '')}</span>
                    </div>
                </td>
                <td class="p-md text-sm">${esc(c.telephone ?? '—')}</td>
                <td class="p-md font-mono text-xs">${esc(c.numero_permis ?? '—')}</td>
                <td class="p-md text-xs text-outline">${esc(c.date_embauche ?? '—')}</td>
                <td class="p-md"><span class="px-sm py-xs text-xs font-semibold rounded-full ${statutClass}">${esc(c.statut ?? 'Actif')}</span></td>
            </tr>`;
        }).join('');
    } catch {
        body.innerHTML = '<tr><td colspan="5" class="p-xl text-center text-error">Erreur de chargement.</td></tr>';
    }
}

document.getElementById('btn-refresh-conducteurs').addEventListener('click', () => { loaded.conducteurs = false; loadConducteurs(); });

// ── TRAJETS ─────────────────────────────────────────────────────────────────
async function loadTrajets() {
    const body = document.getElementById('trajets-table-body');
    body.innerHTML = '<tr><td colspan="5" class="p-xl text-center text-outline"><div class="inline-block animate-spin w-5 h-5 border-2 border-primary border-t-transparent rounded-full"></div></td></tr>';
    try {
        const res = await apiFetch(`${BASE_URL}api/trajets?per_page=200`);
        const json = await res.json();
        const items = json.data?.items ?? [];

        if (!items.length) {
            body.innerHTML = '<tr><td colspan="5" class="p-xl text-center text-outline font-medium">Aucun trajet configuré.</td></tr>';
            return;
        }
        body.innerHTML = items.map(t => {
            const depart   = CACHE_LOCATIONS[t.id_lieu_depart]  ?? `Lieu #${t.id_lieu_depart}`;
            const arrivee  = CACHE_LOCATIONS[t.id_lieu_arrivee] ?? `Lieu #${t.id_lieu_arrivee}`;
            const prix     = parseFloat(t.prix ?? 0).toFixed(2);
            return `
            <tr class="hover:bg-surface-container-low transition-colors">
                <td class="p-md">
                    <div class="flex items-center gap-sm">
                        <span class="text-outline">🚌</span>
                        <span class="font-medium">${esc(depart)}</span>
                        <span class="material-symbols-outlined text-outline text-[16px]">arrow_forward</span>
                        <span class="font-medium">${esc(arrivee)}</span>
                    </div>
                </td>
                <td class="p-md font-bold text-primary">${esc(prix)}</td>
                <td class="p-md text-sm text-on-surface-variant">${esc(t.distance_km ?? '—')} km</td>
                <td class="p-md text-xs text-outline">${esc(t.duree_estimee ?? '—')}</td>
                <td class="p-md">
                    <span class="px-sm py-xs text-xs font-semibold rounded-full bg-surface-container text-on-surface-variant">${esc(t.statut ?? 'Actif')}</span>
                </td>
            </tr>`;
        }).join('');
    } catch {
        body.innerHTML = '<tr><td colspan="5" class="p-xl text-center text-error">Erreur de chargement.</td></tr>';
    }
}

document.getElementById('btn-refresh-trajets').addEventListener('click', () => { loaded.trajets = false; loadTrajets(); });

// ── HORAIRES ─────────────────────────────────────────────────────────────────
async function loadHoraires() {
    const container = document.getElementById('horaires-cards-container');
    container.innerHTML = '<div class="col-span-full text-center text-outline p-xl"><div class="inline-block animate-spin w-5 h-5 border-2 border-primary border-t-transparent rounded-full"></div></div>';
    try {
        const res = await apiFetch(`${BASE_URL}api/horaires?per_page=200`);
        const json = await res.json();
        const items = json.data?.items ?? [];

        if (!items.length) {
            container.innerHTML = '<div class="col-span-full text-center text-outline font-medium p-xl">Aucun horaire programmé.</div>';
            return;
        }
        container.innerHTML = items.map(h => {
            const depart  = (h.heure_depart  ?? '').slice(0, 5);
            const arrivee = (h.heure_arrivee ?? '').slice(0, 5);
            return `
            <div class="bg-surface-container-low border border-outline-variant rounded-xl p-md flex flex-col items-center gap-sm hover:shadow-md transition-shadow">
                <span class="material-symbols-outlined text-primary text-[28px]">schedule</span>
                <div class="text-center">
                    <div class="font-bold text-title-md text-on-surface">${esc(depart)}</div>
                    <div class="text-xs text-outline">Départ</div>
                </div>
                <span class="material-symbols-outlined text-outline text-[16px]">arrow_downward</span>
                <div class="text-center">
                    <div class="font-bold text-title-md text-on-surface">${esc(arrivee)}</div>
                    <div class="text-xs text-outline">Arrivée</div>
                </div>
            </div>`;
        }).join('');
    } catch {
        container.innerHTML = '<div class="col-span-full text-center text-error p-xl">Erreur de chargement.</div>';
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
