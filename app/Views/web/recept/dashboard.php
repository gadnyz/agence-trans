<?= $this->extend($layout ?? 'web/layouts/recept') ?>

<?= $this->section('content') ?>

<?php
$user        = session()->get('user') ?? [];
$displayName = trim(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? '')) ?: ($user['username'] ?? 'Guichetier');
$today       = date('d/m/Y');
$todayIso    = date('Y-m-d');
$timeOfDay   = (int)date('H') < 12 ? 'Bonjour' : ((int)date('H') < 18 ? 'Bon après-midi' : 'Bonsoir');
?>

<div class="px-4 pb-12 max-w-[1600px] mx-auto space-y-6">
    <!-- ── Flash/Alert ── -->
    <div id="page-alert" class="hidden rounded-xl p-3 text-sm font-medium border transition-all"></div>

    <!-- ── KPI Cards ── -->
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5 cursor-pointer hover:shadow-md transition-shadow"
             onclick="window.location='<?= base_url('recept/reservations') ?>'">
            <div class="flex items-center gap-2 mb-3">
                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-blue-50 text-blue-600">
                    <span class="material-symbols-outlined text-[18px]">confirmation_number</span>
                </div>
            </div>
            <span class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Réservations</span>
            <strong id="kpi-reservations" class="block text-xl font-bold text-gray-900 mt-1">—</strong>
            <span class="block text-xs text-gray-500 mt-1">Aujourd'hui</span>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5 cursor-pointer hover:shadow-md transition-shadow"
             onclick="window.location='<?= base_url('recept/paiements') ?>'">
            <div class="flex items-center gap-2 mb-3">
                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600">
                    <span class="material-symbols-outlined text-[18px]">payments</span>
                </div>
            </div>
            <span class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Paiements</span>
            <strong id="kpi-paiements" class="block text-xl font-bold text-gray-900 mt-1">—</strong>
            <span class="block text-xs text-gray-500 mt-1">Encaissés</span>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5 cursor-pointer hover:shadow-md transition-shadow"
             onclick="window.location='<?= base_url('recept/programmes') ?>'">
            <div class="flex items-center gap-2 mb-3">
                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-violet-50 text-violet-600">
                    <span class="material-symbols-outlined text-[18px]">directions_bus</span>
                </div>
            </div>
            <span class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Voyages</span>
            <strong id="kpi-voyages" class="block text-xl font-bold text-gray-900 mt-1">—</strong>
            <span class="block text-xs text-gray-500 mt-1">Planifiés aujourd'hui</span>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5 cursor-pointer hover:shadow-md transition-shadow"
             onclick="window.location='<?= base_url('recept/programmes') ?>'">
            <div class="flex items-center gap-2 mb-3">
                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-amber-50 text-amber-600">
                    <span class="material-symbols-outlined text-[18px]">event_seat</span>
                </div>
            </div>
            <span class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Places libres</span>
            <strong id="kpi-places" class="block text-xl font-bold text-gray-900 mt-1">—</strong>
            <span class="block text-xs text-gray-500 mt-1">Disponibles maintenant</span>
        </div>

    </section>

    <!-- ── Corps principal : Voyages + Réservations récentes ── -->
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

        <!-- ── Voyages du jour (3/5) ── -->
        <div class="lg:col-span-3 bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-semibold text-gray-900">Voyages du jour</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Programmes planifiés pour aujourd'hui</p>
                </div>
                <a href="<?= base_url('recept/programmes') ?>"
                   class="text-sm text-blue-600 font-medium hover:underline flex items-center gap-1">
                    Voir tout <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                </a>
            </div>
            <div id="dashboard-voyages" class="divide-y divide-gray-100">
                <div class="flex items-center justify-center py-12">
                    <div class="animate-spin w-8 h-8 border-2 border-blue-500 border-t-transparent rounded-full"></div>
                </div>
            </div>
        </div>

        <!-- ── Dernières réservations (2/5) ── -->
        <div class="lg:col-span-2 bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-semibold text-gray-900">Réservations récentes</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Les 8 dernières enregistrées</p>
                </div>
                <a href="<?= base_url('recept/reservations') ?>"
                   class="text-sm text-blue-600 font-medium hover:underline flex items-center gap-1">
                    Gérer <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                </a>
            </div>
            <div id="dashboard-reservations" class="divide-y divide-gray-100">
                <div class="flex items-center justify-center py-12">
                    <div class="animate-spin w-8 h-8 border-2 border-blue-500 border-t-transparent rounded-full"></div>
                </div>
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

// ── Voyages du jour ──────────────────────────────────────────────────────────
async function loadDashboardVoyages() {
    const el = document.getElementById('dashboard-voyages');
    try {
        const params = new URLSearchParams({ per_page: 10, date_debut: TODAY, date_fin: TODAY });
        const res    = await apiFetch(`${BASE_URL}api/planification/search?${params}`);
        const json   = await res.json();
        const items  = json.data?.items ?? [];

        const totalPlaces = items.reduce((s, p) => s + (parseInt(p.places_disponibles) || 0), 0);
        document.getElementById('kpi-voyages').textContent = items.length;
        document.getElementById('kpi-places').textContent  = totalPlaces;

        if (!items.length) {
            el.innerHTML = `<div class="flex flex-col items-center justify-center py-12 gap-3 text-gray-400">
                <span class="material-symbols-outlined text-[48px] text-gray-300">directions_bus</span>
                <p class="text-sm font-medium">Aucun voyage planifié aujourd'hui</p>
            </div>`;
            return;
        }

        el.innerHTML = items.map(p => {
            const places      = parseInt(p.places_disponibles) || 0;
            const placesColor = places > 5 ? 'text-emerald-600' : places > 0 ? 'text-amber-600' : 'text-red-500';
            const dotColor    = places > 5 ? 'bg-emerald-500' : places > 0 ? 'bg-amber-500' : 'bg-red-500';
            return `
            <div class="flex items-center justify-between px-6 py-3.5 hover:bg-gray-50/50 transition-colors">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-2.5 h-2.5 rounded-full ${dotColor} shrink-0"></div>
                    <div class="min-w-0">
                        <p class="font-medium text-gray-900 text-sm truncate">${esc(p.lieu_depart ?? '?')} → ${esc(p.lieu_arrivee ?? '?')}</p>
                        <p class="text-xs text-gray-500">${esc((p.heure_depart ?? '').slice(0, 5))} · Bus ${esc(p.numero_plaque ?? '?')}</p>
                    </div>
                </div>
                <div class="shrink-0 ml-4 text-right">
                    <span class="font-semibold text-sm ${placesColor}">${places}</span>
                    <span class="text-xs text-gray-400 block">places</span>
                </div>
            </div>`;
        }).join('');

    } catch { document.getElementById('kpi-voyages').textContent = '—'; }
}

// ── Dernières réservations ───────────────────────────────────────────────────
async function loadDashboardReservations() {
    const el = document.getElementById('dashboard-reservations');
    try {
        const res   = await apiFetch(`${BASE_URL}api/reservations?page=1&per_page=8`);
        const json  = await res.json();
        const items = json.data?.items ?? [];
        const meta  = json.data?.meta  ?? {};

        document.getElementById('kpi-reservations').textContent = meta.total ?? items.length;
        const paidCount = items.filter(r => parseFloat(r.montant_paye ?? 0) > 0).length;
        document.getElementById('kpi-paiements').textContent = paidCount;

        if (!items.length) {
            el.innerHTML = `<div class="flex flex-col items-center justify-center py-12 gap-3 text-gray-400">
                <span class="material-symbols-outlined text-[48px] text-gray-300">confirmation_number</span>
                <p class="text-sm font-medium">Aucune réservation</p>
            </div>`;
            return;
        }

        el.innerHTML = items.map(r => {
            const statut = (r.statut_reservation ?? '').toUpperCase();
            const badgeClass = statut.includes('CONFIRM') ? 'bg-green-50 text-green-700 border-green-200'
                             : statut.includes('ATTENTE') ? 'bg-yellow-50 text-yellow-700 border-yellow-200'
                             : 'bg-red-50 text-red-700 border-red-200';
            return `
            <div class="flex items-center justify-between px-6 py-3.5 hover:bg-gray-50/50 transition-colors">
                <div class="min-w-0">
                    <p class="font-semibold text-blue-600 text-xs">${esc(r.reference_reservation)}</p>
                    <p class="text-xs text-gray-500 truncate">${esc(r.client_nom ?? '?')}</p>
                </div>
                <div class="shrink-0 ml-3 text-right">
                    <span class="inline-block px-2 py-0.5 text-[10px] font-semibold rounded-full border ${badgeClass}">${esc(statut)}</span>
                    <p class="text-xs text-gray-400 mt-0.5">${esc(r.nombre_places ?? '?')} pl.</p>
                </div>
            </div>`;
        }).join('');

    } catch { document.getElementById('kpi-reservations').textContent = '—'; }
}

window.addEventListener('load', () => {
    loadDashboardVoyages();
    loadDashboardReservations();
});
</script>
<?= $this->endSection() ?>
