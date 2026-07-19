<?= $this->extend($layout ?? 'web/layouts/recept') ?>

<?= $this->section('content') ?>

<?php
$user        = session()->get('user') ?? [];
$displayName = trim(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? '')) ?: ($user['username'] ?? 'Guichetier');
$today       = date('d/m/Y');
$todayIso    = date('Y-m-d');
$currentMonth = date('Y-m');
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
                        <h2 class="text-lg font-semibold text-gray-900 tracking-tight">Programmes Voyage</h2>
                    </div>
                </div>
                <a href="<?= base_url('recept/reservations') ?>"
                   class="inline-flex h-10 px-3 md:px-4 bg-blue-600 text-white rounded-xl text-sm font-medium hover:bg-blue-700 transition-all items-center justify-center gap-2 shadow-sm whitespace-nowrap">
                    <span class="material-symbols-outlined text-[18px]">confirmation_number</span>
                    <span class="hidden sm:inline">Créer une réservation</span>
                    <span class="sm:hidden">Réserver</span>
                </a>
            </div>
        </div>
    </div>

    <!-- ── Flash/Alert ── -->
    <div id="page-alert" class="hidden rounded-xl p-3 text-sm font-medium border transition-all"></div>

    <!-- ── Filtres de recherche ── -->
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
        <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
            <span class="material-symbols-outlined text-blue-500 text-[18px]">travel_explore</span>
            Rechercher des voyages planifiés
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div class="relative sm:col-span-2 w-full">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">search</span>
                <input
                    id="search-programme"
                    type="text"
                    placeholder="Ville de départ, d'arrivée, trajet..."
                    class="w-full pl-10 pr-3 h-9 bg-white border border-gray-300 rounded-xl text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
                />
            </div>
            
            <div class="flex flex-row gap-2 w-full">
                <input
                    type="month"
                    id="filter-month"
                    value="<?= $currentMonth ?>"
                    class="h-9 px-3 bg-white border w-full border-gray-300 rounded-xl text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none"
                />
                <button
                    id="btn-search-programme"
                    class="h-9 px-4 bg-blue-600 w-full text-white rounded-xl text-sm font-medium hover:bg-blue-700 transition-all flex items-center justify-center gap-2 shadow-sm"
                >
                    <span class="material-symbols-outlined text-[18px]">search</span>
                    Rechercher
                </button>
            </div>
        </div>
    </div>

    <!-- ── Statistiques rapides ── -->
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="report-card bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                    <span class="material-symbols-outlined text-blue-600 text-[18px]">calendar_today</span>
                </div>
            </div>
            <span class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Voyages planifiés</span>
            <strong id="stat-total" class="block text-xl font-bold text-gray-900 mt-1">—</strong>
        </div>
        <div class="report-card bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center">
                    <span class="material-symbols-outlined text-emerald-600 text-[18px]">event_seat</span>
                </div>
            </div>
            <span class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Places libres</span>
            <strong id="stat-places" class="block text-xl font-bold text-gray-900 mt-1">—</strong>
        </div>
        <div class="report-card bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-8 h-8 rounded-lg bg-violet-50 flex items-center justify-center">
                    <span class="material-symbols-outlined text-violet-600 text-[18px]">directions_bus</span>
                </div>
            </div>
            <span class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Bus actifs</span>
            <strong id="stat-bus" class="block text-xl font-bold text-gray-900 mt-1">—</strong>
        </div>
        <div class="report-card bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center">
                    <span class="material-symbols-outlined text-amber-600 text-[18px]">no_crash</span>
                </div>
            </div>
            <span class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Complets</span>
            <strong id="stat-complets" class="block text-xl font-bold text-gray-900 mt-1">—</strong>
        </div>
    </section>

    <!-- ── Liste des programmes du mois ── -->
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
            <div>
                <h3 class="text-base font-semibold text-gray-900">Programmes du mois</h3>
                <p id="programmes-subtitle" class="text-xs text-gray-500 mt-0.5">Tous les voyages planifiés pour le mois courant</p>
            </div>
            <button id="btn-refresh"
                    class="h-9 px-4 rounded-xl bg-white border border-gray-300 text-gray-700 flex items-center gap-2 hover:bg-gray-50 transition-all shadow-sm text-sm font-medium">
                <span class="material-symbols-outlined text-[18px]">refresh</span>
                Actualiser
            </button>
        </div>
        <div id="programmes-list" class="divide-y divide-gray-100">
            <div class="flex flex-col items-center justify-center py-12 gap-3 text-gray-400">
                <div class="animate-spin w-10 h-10 border-2 border-blue-500 border-t-transparent rounded-full"></div>
                <p class="text-sm">Chargement des voyages...</p>
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

/**
 * Returns { dateDebut: 'YYYY-MM-01', dateFin: 'YYYY-MM-last' } for the selected month
 */
function getMonthRange() {
    const monthVal = document.getElementById('filter-month').value; // e.g. "2026-07"
    if (!monthVal) {
        const now = new Date();
        const y = now.getFullYear();
        const m = String(now.getMonth() + 1).padStart(2, '0');
        return {
            dateDebut: `${y}-${m}-01`,
            dateFin: `${y}-${m}-${new Date(y, now.getMonth() + 1, 0).getDate()}`
        };
    }
    const [y, m] = monthVal.split('-').map(Number);
    const lastDay = new Date(y, m, 0).getDate();
    return {
        dateDebut: `${y}-${String(m).padStart(2, '0')}-01`,
        dateFin: `${y}-${String(m).padStart(2, '0')}-${String(lastDay).padStart(2, '0')}`
    };
}

/**
 * Formats a month string like "juillet 2026"
 */
function formatMonthLabel(monthVal) {
    const moisNoms = ['janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre'];
    if (!monthVal) return '';
    const [y, m] = monthVal.split('-').map(Number);
    return `${moisNoms[m - 1]} ${y}`;
}

async function searchProgrammes() {
    const search = document.getElementById('search-programme').value.trim();
    const list   = document.getElementById('programmes-list');
    const subtitle = document.getElementById('programmes-subtitle');
    const { dateDebut, dateFin } = getMonthRange();
    const monthLabel = formatMonthLabel(document.getElementById('filter-month').value);

    subtitle.textContent = `Tous les voyages planifiés pour ${monthLabel}`;

    list.innerHTML = `<div class="flex flex-col items-center justify-center py-12 gap-3 text-gray-400">
        <div class="animate-spin w-10 h-10 border-2 border-blue-500 border-t-transparent rounded-full"></div>
        <p class="text-sm">Chargement des voyages...</p>
    </div>`;

    try {
        const params = new URLSearchParams({ per_page: 200, date_debut: dateDebut, date_fin: dateFin });
        if (search) params.set('search', search);
        const response = await apiFetch(`${BASE_URL}api/planification/search?${params}`);
        const json = await response.json();
        const items = json.data?.items ?? [];

        // Statistiques
        const totalPlaces = items.reduce((s, p) => s + (parseInt(p.places_disponibles) || 0), 0);
        const busSet = new Set(items.map(p => p.numero_plaque));
        const complets = items.filter(p => parseInt(p.places_disponibles) === 0).length;

        document.getElementById('stat-total').textContent = items.length;
        document.getElementById('stat-places').textContent = totalPlaces;
        document.getElementById('stat-bus').textContent = busSet.size;
        document.getElementById('stat-complets').textContent = complets;

        if (!items.length) {
            list.innerHTML = `
                <div class="flex flex-col items-center justify-center py-12 text-gray-400 gap-3">
                    <span class="material-symbols-outlined text-[48px] text-gray-300">directions_bus</span>
                    <div class="text-center">
                        <p class="text-sm font-medium text-gray-500">Aucun voyage planifié pour ${esc(monthLabel)}</p>
                        <p class="text-xs text-gray-400 mt-1">Sélectionnez un autre mois ou modifiez vos critères.</p>
                    </div>
                </div>`;
            return;
        }

        // Grouper les programmes par date pour un affichage clair
        const grouped = {};
        items.forEach(p => {
            const dateKey = p.date_programme ?? 'Inconnue';
            if (!grouped[dateKey]) grouped[dateKey] = [];
            grouped[dateKey].push(p);
        });

        let html = '';
        const joursSemaine = ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'];
        const moisNoms = ['janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre'];

        for (const [dateStr, programmes] of Object.entries(grouped)) {
            const dt = new Date(dateStr + 'T00:00:00');
            const jourNom = joursSemaine[dt.getDay()];
            const jourNum = dt.getDate();
            const moisNom = moisNoms[dt.getMonth()];
            const annee = dt.getFullYear();
            const isToday = dateStr === TODAY;

            html += `<div class="px-6 py-3 bg-gray-50/80 border-b border-gray-100 sticky top-0 z-10">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[16px] ${isToday ? 'text-blue-600' : 'text-gray-400'}">calendar_today</span>
                    <span class="text-sm font-semibold ${isToday ? 'text-blue-600' : 'text-gray-700'}">${esc(jourNom)} ${jourNum} ${esc(moisNom)} ${annee}</span>
                    ${isToday ? '<span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full text-[10px] font-bold uppercase">Aujourd\'hui</span>' : ''}
                    <span class="text-xs text-gray-400 ml-auto">${programmes.length} voyage(s)</span>
                </div>
            </div>`;

            programmes.forEach(p => {
                const depart  = p.lieu_depart ?? '?';
                const arrivee = p.lieu_arrivee ?? '?';
                const heure   = (p.heure_depart ?? '').slice(0, 5);
                const places  = parseInt(p.places_disponibles) || 0;
                const placesClass = places > 5 ? 'text-green-600' : places > 0 ? 'text-yellow-600' : 'text-red-600';

                html += `<div class="flex flex-col md:flex-row items-center justify-between px-6 py-4 hover:bg-gray-50/50 transition-colors">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center shrink-0 shadow-sm">
                        <span class="material-symbols-outlined">directions_bus</span>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">${esc(depart)} → ${esc(arrivee)}</p>
                        <p class="text-xs text-gray-500 mt-0.5">Départ ${esc(heure)} · Bus ${esc(p.numero_plaque ?? '?')} · Tarif: ${esc(p.prix)} ${esc(p.code_currency)} · <span class="${placesClass} font-semibold">${places} place(s) libre(s)</span></p>
                    </div>
                </div>
                <div class="flex items-center gap-2 mt-2.5 md:mt-0 justify-end w-full sm:w-auto shrink-0">
                    ${places > 0
                        ? `<a href="${BASE_URL}recept/reservations" class="h-9 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold shadow-sm transition-all flex items-center justify-center">Réserver</a>`
                        : `<span class="px-2.5 py-1 bg-red-50 text-red-700 border border-red-200 rounded-xl text-xs font-semibold">Complet</span>`
                    }
                    <a href="${BASE_URL}recept/programmes/${p.id_programme}/manifeste" target="_blank" class="h-9 px-4 bg-white border border-gray-300 text-gray-700 rounded-xl text-xs font-semibold hover:bg-gray-50 transition-all flex items-center justify-center gap-1 shadow-sm">
                        <span class="material-symbols-outlined text-[16px]">print</span>
                        Manifeste
                    </a>
                </div>
            </div>`;
            });
        }

        list.innerHTML = html;

    } catch (e) {
        console.error('Erreur chargement programmes:', e);
        list.innerHTML = '<div class="px-6 py-8 text-center text-red-600">Erreur lors du chargement. Veuillez réessayer.</div>';
    }
}

document.getElementById('btn-search-programme').addEventListener('click', searchProgrammes);
document.getElementById('btn-refresh').addEventListener('click', searchProgrammes);
document.getElementById('filter-month').addEventListener('change', searchProgrammes);
document.getElementById('search-programme').addEventListener('keypress', (e) => { if (e.key === 'Enter') searchProgrammes(); });

window.addEventListener('load', searchProgrammes);
</script>
<?= $this->endSection() ?>
