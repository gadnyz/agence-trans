<?= $this->extend($layout ?? 'web/layouts/recept') ?>

<?= $this->section('content') ?>

<?php
$user        = session()->get('user') ?? [];
$displayName = trim(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? '')) ?: ($user['username'] ?? 'Guichetier');
$today       = date('d/m/Y');
$todayIso    = date('Y-m-d');
$timeOfDay   = (int)date('H') < 12 ? 'Bonjour' : ((int)date('H') < 18 ? 'Bon après-midi' : 'Bonsoir');
?>

<div class="space-y-xl">

    <!-- ── Bienvenue ── -->
    <div class="relative overflow-hidden bg-gradient-to-br from-primary to-blue-700 rounded-2xl p-xl shadow-lg">
        <div class="relative z-10">
            <p class="text-on-primary/80 text-body-md font-medium mb-xs"><?= esc($today) ?></p>
            <h2 class="text-headline-lg font-bold text-on-primary leading-tight"><?= esc($timeOfDay) ?>, <?= esc($displayName) ?> 👋</h2>
            <p class="text-on-primary/70 text-body-lg mt-sm">Bienvenue sur votre espace guichet. Toutes vos fonctionnalités sont accessibles via le menu de gauche.</p>
            <div class="mt-lg flex flex-wrap gap-sm">
                <a href="<?= base_url('recept/reservations') ?>" class="inline-flex items-center gap-sm px-lg py-sm bg-white/20 hover:bg-white/30 text-on-primary rounded-xl font-label-lg transition-all backdrop-blur-sm border border-white/30">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    Nouvelle réservation
                </a>
                <a href="<?= base_url('recept/programmes') ?>" class="inline-flex items-center gap-sm px-lg py-sm bg-white/10 hover:bg-white/20 text-on-primary rounded-xl font-label-lg transition-all backdrop-blur-sm border border-white/20">
                    <span class="material-symbols-outlined text-[18px]">directions_bus</span>
                    Voir les voyages du jour
                </a>
            </div>
        </div>
        <!-- Decorative background -->
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2 pointer-events-none"></div>
        <div class="absolute bottom-0 left-1/3 w-32 h-32 bg-white/5 rounded-full translate-y-1/2 pointer-events-none"></div>
    </div>

    <!-- ── Flash/Alert global ── -->
    <div id="page-alert" class="hidden rounded-xl p-md text-sm font-medium border transition-all"></div>

    <!-- ── KPI Cartes ── -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-md">

        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-lg shadow-sm flex items-center gap-md hover:shadow-md transition-shadow cursor-pointer" onclick="window.location='<?= base_url('recept/reservations') ?>'">
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-blue-700 text-[22px]">confirmation_number</span>
            </div>
            <div class="min-w-0">
                <div class="text-[11px] text-outline font-semibold uppercase tracking-wide">Réservations</div>
                <div id="kpi-reservations" class="font-bold text-title-lg text-on-surface">—</div>
                <div class="text-xs text-outline">Aujourd'hui</div>
            </div>
        </div>

        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-lg shadow-sm flex items-center gap-md hover:shadow-md transition-shadow cursor-pointer" onclick="window.location='<?= base_url('recept/paiements') ?>'">
            <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-emerald-700 text-[22px]">payments</span>
            </div>
            <div class="min-w-0">
                <div class="text-[11px] text-outline font-semibold uppercase tracking-wide">Paiements</div>
                <div id="kpi-paiements" class="font-bold text-title-lg text-on-surface">—</div>
                <div class="text-xs text-outline">Encaissés</div>
            </div>
        </div>

        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-lg shadow-sm flex items-center gap-md hover:shadow-md transition-shadow cursor-pointer" onclick="window.location='<?= base_url('recept/programmes') ?>'">
            <div class="w-12 h-12 bg-primary-container rounded-xl flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-on-primary-container text-[22px]">directions_bus</span>
            </div>
            <div class="min-w-0">
                <div class="text-[11px] text-outline font-semibold uppercase tracking-wide">Voyages</div>
                <div id="kpi-voyages" class="font-bold text-title-lg text-on-surface">—</div>
                <div class="text-xs text-outline">Planifiés aujourd'hui</div>
            </div>
        </div>

        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-lg shadow-sm flex items-center gap-md hover:shadow-md transition-shadow cursor-pointer" onclick="window.location='<?= base_url('recept/programmes') ?>'">
            <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-yellow-700 text-[22px]">event_seat</span>
            </div>
            <div class="min-w-0">
                <div class="text-[11px] text-outline font-semibold uppercase tracking-wide">Places libres</div>
                <div id="kpi-places" class="font-bold text-title-lg text-on-surface">—</div>
                <div class="text-xs text-outline">Disponibles maintenant</div>
            </div>
        </div>

    </div>

    <!-- ── Corps principal : 2 colonnes ── -->
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-lg">

        <!-- ── Voyages du jour (colonne gauche 3/5) ── -->
        <div class="lg:col-span-3 bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm overflow-hidden">
            <div class="flex items-center justify-between p-lg border-b border-outline-variant bg-surface-container-low/30">
                <div class="flex items-center gap-sm">
                    <span class="material-symbols-outlined text-primary text-[22px]">directions_bus</span>
                    <h3 class="font-title-md text-on-surface">Voyages du jour</h3>
                </div>
                <a href="<?= base_url('recept/programmes') ?>" class="text-label-sm text-primary hover:underline inline-flex items-center gap-xs">
                    Voir tout <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                </a>
            </div>
            <div id="dashboard-voyages" class="divide-y divide-outline-variant/50">
                <div class="flex items-center justify-center py-xl">
                    <div class="animate-spin w-8 h-8 border-2 border-primary border-t-transparent rounded-full"></div>
                </div>
            </div>
        </div>

        <!-- ── Dernières réservations (colonne droite 2/5) ── -->
        <div class="lg:col-span-2 bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm overflow-hidden">
            <div class="flex items-center justify-between p-lg border-b border-outline-variant bg-surface-container-low/30">
                <div class="flex items-center gap-sm">
                    <span class="material-symbols-outlined text-primary text-[22px]">confirmation_number</span>
                    <h3 class="font-title-md text-on-surface">Récentes réservations</h3>
                </div>
                <a href="<?= base_url('recept/reservations') ?>" class="text-label-sm text-primary hover:underline inline-flex items-center gap-xs">
                    Gérer <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                </a>
            </div>
            <div id="dashboard-reservations" class="divide-y divide-outline-variant/50">
                <div class="flex items-center justify-center py-xl">
                    <div class="animate-spin w-8 h-8 border-2 border-primary border-t-transparent rounded-full"></div>
                </div>
            </div>
        </div>

    </div>

    <!-- ── Raccourcis d'actions ── -->
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-lg shadow-sm">
        <h3 class="font-title-md text-on-surface mb-md flex items-center gap-sm">
            <span class="material-symbols-outlined text-primary text-[20px]">bolt</span>
            Actions rapides
        </h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-md">
            <a href="<?= base_url('recept/reservations') ?>"
               class="flex flex-col items-center gap-sm p-lg bg-primary/5 hover:bg-primary/10 border border-primary/20 rounded-xl transition-all group">
                <div class="w-12 h-12 bg-primary rounded-xl flex items-center justify-center shadow-md group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-on-primary text-[22px]">add_circle</span>
                </div>
                <span class="text-label-lg text-on-surface text-center font-medium">Nouvelle réservation</span>
            </a>
            <a href="<?= base_url('recept/reservations') ?>"
               class="flex flex-col items-center gap-sm p-lg bg-surface-container-low hover:bg-surface-container border border-outline-variant rounded-xl transition-all group">
                <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center shadow-md group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-white text-[22px]">manage_search</span>
                </div>
                <span class="text-label-lg text-on-surface text-center font-medium">Rechercher</span>
            </a>
            <a href="<?= base_url('recept/paiements') ?>"
               class="flex flex-col items-center gap-sm p-lg bg-surface-container-low hover:bg-surface-container border border-outline-variant rounded-xl transition-all group">
                <div class="w-12 h-12 bg-emerald-600 rounded-xl flex items-center justify-center shadow-md group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-white text-[22px]">receipt_long</span>
                </div>
                <span class="text-label-lg text-on-surface text-center font-medium">Voir les paiements</span>
            </a>
            <a href="<?= base_url('recept/flotte') ?>"
               class="flex flex-col items-center gap-sm p-lg bg-surface-container-low hover:bg-surface-container border border-outline-variant rounded-xl transition-all group">
                <div class="w-12 h-12 bg-purple-600 rounded-xl flex items-center justify-center shadow-md group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-white text-[22px]">directions_bus</span>
                </div>
                <span class="text-label-lg text-on-surface text-center font-medium">Consulter la flotte</span>
            </a>
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

// ── Voyages du jour ─────────────────────────────────────────────────────────
async function loadDashboardVoyages() {
    const el = document.getElementById('dashboard-voyages');
    try {
        const params = new URLSearchParams({ per_page: 10, date_debut: TODAY, date_fin: TODAY });
        const res  = await apiFetch(`${BASE_URL}api/planification/search?${params}`);
        const json = await res.json();
        const items = json.data?.items ?? [];

        // Update KPI
        const totalPlaces = items.reduce((s, p) => s + (parseInt(p.places_disponibles) || 0), 0);
        document.getElementById('kpi-voyages').textContent = items.length;
        document.getElementById('kpi-places').textContent  = totalPlaces;

        if (!items.length) {
            el.innerHTML = `<div class="flex flex-col items-center justify-center py-xl gap-md text-outline">
                <span class="material-symbols-outlined text-[48px] text-outline-variant">directions_bus</span>
                <p class="text-body-md">Aucun voyage planifié aujourd'hui</p>
            </div>`;
            return;
        }

        el.innerHTML = items.map(p => {
            const places = parseInt(p.places_disponibles) || 0;
            const placesClass = places > 5 ? 'text-emerald-600' : places > 0 ? 'text-yellow-600' : 'text-red-500';
            const barColor = places > 5 ? 'bg-emerald-500' : places > 0 ? 'bg-yellow-500' : 'bg-red-500';
            return `
            <div class="flex items-center justify-between p-md hover:bg-surface-container-low transition-colors">
                <div class="flex items-center gap-md min-w-0">
                    <div class="w-3 h-3 rounded-full ${barColor} shrink-0"></div>
                    <div class="min-w-0">
                        <p class="font-medium text-on-surface text-sm truncate">${esc(p.lieu_depart ?? '?')} → ${esc(p.lieu_arrivee ?? '?')}</p>
                        <p class="text-xs text-outline">${esc((p.heure_depart ?? '').slice(0, 5))} · Bus ${esc(p.numero_plaque ?? '?')}</p>
                    </div>
                </div>
                <div class="shrink-0 ml-md text-right">
                    <span class="font-semibold text-sm ${placesClass}">${places}</span>
                    <span class="text-xs text-outline block">places</span>
                </div>
            </div>`;
        }).join('');

    } catch { document.getElementById('kpi-voyages').textContent = '—'; }
}

// ── Dernières réservations ───────────────────────────────────────────────────
async function loadDashboardReservations() {
    const el = document.getElementById('dashboard-reservations');
    try {
        const res  = await apiFetch(`${BASE_URL}api/reservations?page=1&per_page=8`);
        const json = await res.json();
        const items = json.data?.items ?? [];
        const meta  = json.data?.meta ?? {};

        // Update KPI
        document.getElementById('kpi-reservations').textContent = meta.total ?? items.length;

        // Count payments
        const paidCount = items.filter(r => parseFloat(r.montant_paye ?? 0) > 0).length;
        document.getElementById('kpi-paiements').textContent = paidCount;

        if (!items.length) {
            el.innerHTML = `<div class="flex flex-col items-center justify-center py-xl gap-md text-outline">
                <span class="material-symbols-outlined text-[48px] text-outline-variant">confirmation_number</span>
                <p class="text-body-md">Aucune réservation</p>
            </div>`;
            return;
        }

        el.innerHTML = items.map(r => {
            const statut = (r.statut_reservation ?? '').toUpperCase();
            const dotColor = statut.includes('CONFIRM') ? 'bg-emerald-500' : statut.includes('ATTENTE') ? 'bg-yellow-500' : 'bg-red-400';
            return `
            <div class="flex items-center justify-between p-md hover:bg-surface-container-low transition-colors">
                <div class="flex items-center gap-sm min-w-0">
                    <div class="w-2.5 h-2.5 rounded-full ${dotColor} shrink-0"></div>
                    <div class="min-w-0">
                        <p class="font-semibold text-primary text-xs truncate">${esc(r.reference_reservation)}</p>
                        <p class="text-xs text-outline truncate">${esc(r.client_nom ?? '?')}</p>
                    </div>
                </div>
                <div class="shrink-0 ml-sm text-right">
                    <p class="text-xs text-on-surface-variant">${esc(r.lieu_depart ?? '?')}</p>
                    <p class="text-xs text-outline">${esc(r.nombre_places ?? '?')} pl.</p>
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
