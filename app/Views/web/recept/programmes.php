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
            Rechercher des voyages disponibles
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div class="relative sm:col-span-2 w-full md:w-[60%]">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">search</span>
                <input
                    id="search-programme"
                    type="text"
                    placeholder="Ville de départ, d'arrivée, trajet..."
                    class="w-full pl-10 pr-3 h-9 bg-white border border-gray-300 rounded-xl text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
                />
            </div>
            
            <div class="flex flex-row gap-2 w-full md:w-[40%]">
                <input
                type="date"
                id="filter-date"
                value="<?= $todayIso ?>"
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
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                    <span class="material-symbols-outlined text-blue-600 text-[18px]">calendar_today</span>
                </div>
            </div>
            <span class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Voyages trouvés</span>
            <strong id="stat-total" class="block text-xl font-bold text-gray-900 mt-1">—</strong>
        </div>
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center">
                    <span class="material-symbols-outlined text-emerald-600 text-[18px]">event_seat</span>
                </div>
            </div>
            <span class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Places libres</span>
            <strong id="stat-places" class="block text-xl font-bold text-gray-900 mt-1">—</strong>
        </div>
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-8 h-8 rounded-lg bg-violet-50 flex items-center justify-center">
                    <span class="material-symbols-outlined text-violet-600 text-[18px]">directions_bus</span>
                </div>
            </div>
            <span class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Bus actifs</span>
            <strong id="stat-bus" class="block text-xl font-bold text-gray-900 mt-1">—</strong>
        </div>
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center">
                    <span class="material-symbols-outlined text-amber-600 text-[18px]">no_crash</span>
                </div>
            </div>
            <span class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Complets</span>
            <strong id="stat-complets" class="block text-xl font-bold text-gray-900 mt-1">—</strong>
        </div>
    </div>

    <!-- ── Liste des programmes (cards) ── -->
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
            <div>
                <h3 class="text-base font-semibold text-gray-900">Voyages disponibles</h3>
                <p class="text-xs text-gray-500 mt-0.5">Liste des programmes actifs</p>
            </div>
            <button id="btn-refresh"
                    class="h-9 px-4 rounded-xl bg-white border border-gray-300 text-gray-700 flex items-center gap-2 hover:bg-gray-50 transition-all shadow-sm text-sm font-medium">
                <span class="material-symbols-outlined text-[18px]">refresh</span>
                Actualiser
            </button>
        </div>
        <div id="programmes-grid" class="divide-y divide-gray-100">
            <div class="flex flex-col items-center justify-center py-12 gap-3 text-gray-400">
                <div class="animate-spin w-10 h-10 border-2 border-blue-500 border-t-transparent rounded-full"></div>
                <p class="text-sm">Chargement des voyages...</p>
            </div>
        </div>
    </div>

    <!-- ── Vue tableau détaillée ── -->
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
            <h3 class="text-base font-semibold text-gray-900">Tableau récapitulatif</h3>
            <p class="text-xs text-gray-500 mt-0.5">Vue détaillée de tous les programmes</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-gray-600 uppercase text-[11px] font-semibold bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">Trajet</th>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4">Horaire</th>
                        <th class="px-6 py-4">Bus</th>
                        <th class="px-6 py-4">Conducteur</th>
                        <th class="px-6 py-4">Prix</th>
                        <th class="px-6 py-4">Places libres</th>
                        <th class="px-6 py-4">Statut</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody id="programmes-table-body" class="divide-y divide-gray-100">
                    <tr><td colspan="9" class="px-6 py-12 text-center text-gray-400">Chargement...</td></tr>
                </tbody>
            </table>
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

async function searchProgrammes() {
    const date   = document.getElementById('filter-date').value || TODAY;
    const search = document.getElementById('search-programme').value.trim();
    const grid   = document.getElementById('programmes-grid');
    const tbody  = document.getElementById('programmes-table-body');

    const loadingHtml = `<div class="flex flex-col items-center justify-center py-xl text-outline gap-md">
        <div class="animate-spin w-10 h-10 border-2 border-primary border-t-transparent rounded-full"></div>
        <p class="text-body-md">Chargement...</p>
    </div>`;
    grid.innerHTML = loadingHtml;
    tbody.innerHTML = '<tr><td colspan="9" class="p-xl text-center text-outline">Chargement...</td></tr>';

    try {
        const params = new URLSearchParams({ per_page: 100, date_debut: date, date_fin: date });
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
            grid.innerHTML = `
                <div class="flex flex-col items-center justify-center py-xl text-outline gap-md">
                    <span class="material-symbols-outlined text-[32px] md:text-[64px] text-outline-variant">directions_bus</span>
                    <div class="text-center">
                        <p class="text-body-md font-medium md:text-on-surface">Aucun voyage trouvé pour ces critères</p>
                        <p class="text-body-sm text-outline mt-xs">Essayez une autre date ou un autre trajet.</p>
                    </div>
                </div>`;
            tbody.innerHTML = '<tr><td colspan="9" class="p-xl text-center text-outline">Aucun voyage.</td></tr>';
            return;
        }

        // Vue cards
        grid.innerHTML = items.map(p => {
            const places = parseInt(p.places_disponibles) || 0;
            const placesClass = places > 5 ? 'text-emerald-600 bg-emerald-50' : places > 0 ? 'text-yellow-600 bg-yellow-50' : 'text-red-600 bg-red-50';
            const borderClass = places > 5 ? 'border-l-4 border-l-emerald-400' : places > 0 ? 'border-l-4 border-l-yellow-400' : 'border-l-4 border-l-red-400 opacity-70';
            return `
            <div class="flex items-center justify-between px-6 py-4 hover:bg-gray-50/50 transition-colors ${borderClass}">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex flex-col items-center justify-center shrink-0 shadow-sm">
                        <span class="material-symbols-outlined text-[20px]">directions_bus</span>
                        <span class="text-[9px] font-semibold">${esc(p.numero_plaque ?? '')}</span>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-gray-900 truncate">${esc(p.lieu_depart ?? '?')} → ${esc(p.lieu_arrivee ?? '?')}</p>
                        <div class="flex items-center flex-wrap gap-2 mt-1">
                            <span class="inline-flex items-center gap-1 text-xs text-gray-500">
                                <span class="material-symbols-outlined text-[14px]">calendar_today</span>
                                ${esc(p.date_programme ?? '?')}
                            </span>
                            <span class="inline-flex items-center gap-1 text-xs text-gray-500">
                                <span class="material-symbols-outlined text-[14px]">schedule</span>
                                ${esc((p.heure_depart ?? '').slice(0, 5))}
                            </span>
                            <span class="inline-flex items-center gap-1 text-xs text-gray-500">
                                <span class="material-symbols-outlined text-[14px]">badge</span>
                                ${esc(p.conducteur_prenom ?? '')} ${esc(p.conducteur_nom ?? '')}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-6 shrink-0 ml-4">
                    <div class="text-right hidden sm:block">
                        <div class="text-xs text-gray-500">Tarif</div>
                        <div class="font-bold text-gray-900">${esc(p.prix ?? '0')} <span class="text-xs font-normal text-gray-500">${esc(p.code_currency ?? '')}</span></div>
                    </div>
                    <div class="text-center">
                        <span class="px-2.5 py-0.5 font-bold text-xs rounded-full ${placesClass}">${places} place${places !== 1 ? 's' : ''}</span>
                    </div>
                    ${places > 0
                        ? `<a href="${BASE_URL}recept/reservations" class="h-9 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold flex items-center justify-center transition-all whitespace-nowrap shadow-sm">Réserver</a>`
                        : `<span class="h-9 px-4 bg-red-50 text-red-700 border border-red-200 rounded-xl text-xs font-semibold flex items-center justify-center">Complet</span>`
                    }
                </div>
            </div>`;
        }).join('');

        // Vue tableau
        tbody.innerHTML = items.map(p => {
            const places = parseInt(p.places_disponibles) || 0;
            const placesClass = places > 5 ? 'text-emerald-600 font-semibold' : places > 0 ? 'text-yellow-600 font-semibold' : 'text-red-500 font-semibold';
            const statutBg = places > 0 ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200';
            return `
            <tr class="hover:bg-gray-50/50 transition-colors">
                <td class="px-6 py-4 font-medium text-gray-900">${esc(p.lieu_depart ?? '?')} → ${esc(p.lieu_arrivee ?? '?')}</td>
                <td class="px-6 py-4 text-gray-600">${esc(p.date_programme ?? '?')}</td>
                <td class="px-6 py-4 text-xs font-semibold text-gray-900">${esc((p.heure_depart ?? '').slice(0, 5))} - ${esc((p.heure_arrivee ?? '').slice(0, 5))}</td>
                <td class="px-6 py-4 font-mono text-xs text-gray-600">${esc(p.numero_plaque ?? '—')}</td>
                <td class="px-6 py-4 text-xs text-gray-600">${esc(p.conducteur_prenom ?? '')} ${esc(p.conducteur_nom ?? '')}</td>
                <td class="px-6 py-4 font-semibold text-gray-900">${esc(p.prix ?? '0')} <span class="text-xs text-gray-500">${esc(p.code_currency ?? '')}</span></td>
                <td class="px-6 py-4"><span class="${placesClass}">${places}</span></td>
                <td class="px-6 py-4"><span class="px-2.5 py-0.5 text-[11px] font-semibold rounded-full border ${statutBg}">${places > 0 ? 'Disponible' : 'Complet'}</span></td>
                <td class="px-6 py-4 text-right">
                    ${places > 0
                        ? `<a href="${BASE_URL}recept/reservations" class="inline-flex items-center gap-1 h-8 px-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold transition-all">
                               <span class="material-symbols-outlined text-[14px]">add</span> Réserver
                           </a>`
                        : `<span class="text-xs text-red-500 font-medium">Complet</span>`
                    }
                </td>
            </tr>`;
        }).join('');

    } catch (e) {
        grid.innerHTML = '<div class="px-6 py-8 text-center text-red-600">Erreur lors du chargement. Veuillez réessayer.</div>';
        tbody.innerHTML = '<tr><td colspan="9" class="px-6 py-8 text-center text-red-600">Erreur de chargement.</td></tr>';
    }
}

document.getElementById('btn-search-programme').addEventListener('click', searchProgrammes);
document.getElementById('btn-refresh').addEventListener('click', searchProgrammes);
document.getElementById('filter-date').addEventListener('change', searchProgrammes);
document.getElementById('search-programme').addEventListener('keypress', (e) => { if (e.key === 'Enter') searchProgrammes(); });

window.addEventListener('load', searchProgrammes);
</script>
<?= $this->endSection() ?>
