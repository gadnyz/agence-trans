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
            <h2 class="font-headline-lg text-headline-lg text-on-surface">Programmes de Voyage</h2>
            <p class="text-body-lg text-outline"><?= esc($today) ?> &mdash; <?= esc($displayName) ?></p>
        </div>
        <a href="<?= base_url('recept/reservations') ?>"
           class="inline-flex items-center gap-sm px-lg py-sm bg-primary text-on-primary rounded-lg font-label-lg hover:brightness-110 transition-all shadow-sm">
            <span class="material-symbols-outlined text-[20px]">confirmation_number</span>
            Créer une réservation
        </a>
    </div>

    <!-- ── Flash/Alert global ── -->
    <div id="page-alert" class="hidden rounded-xl p-md text-sm font-medium border transition-all"></div>

    <!-- ── Filtres de recherche ── -->
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-lg shadow-sm">
        <h3 class="font-title-sm text-title-sm text-on-surface mb-md flex items-center gap-sm">
            <span class="material-symbols-outlined text-primary text-[20px]">travel_explore</span>
            Rechercher des voyages disponibles
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-md">
            <div class="relative sm:col-span-2">
                <span class="material-symbols-outlined absolute left-md top-1/2 -translate-y-1/2 text-outline text-[20px]">search</span>
                <input
                    id="search-programme"
                    type="text"
                    placeholder="Ville de départ, d'arrivée, trajet..."
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
                class="inline-flex items-center justify-center gap-sm px-lg py-sm bg-primary text-on-primary rounded-lg font-label-lg hover:brightness-110 transition-all"
            >
                <span class="material-symbols-outlined text-[20px]">search</span>
                Rechercher
            </button>
        </div>
    </div>

    <!-- ── Statistiques rapides ── -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-md">
        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-lg flex items-center gap-md shadow-sm">
            <div class="w-10 h-10 rounded-xl bg-primary-container flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-on-primary-container">calendar_today</span>
            </div>
            <div>
                <div class="text-[11px] text-outline font-semibold uppercase tracking-wide">Voyages trouvés</div>
                <div id="stat-total" class="font-bold text-title-md text-on-surface">—</div>
            </div>
        </div>
        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-lg flex items-center gap-md shadow-sm">
            <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-emerald-700">event_seat</span>
            </div>
            <div>
                <div class="text-[11px] text-outline font-semibold uppercase tracking-wide">Places libres</div>
                <div id="stat-places" class="font-bold text-title-md text-on-surface">—</div>
            </div>
        </div>
        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-lg flex items-center gap-md shadow-sm">
            <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-blue-700">directions_bus</span>
            </div>
            <div>
                <div class="text-[11px] text-outline font-semibold uppercase tracking-wide">Bus actifs</div>
                <div id="stat-bus" class="font-bold text-title-md text-on-surface">—</div>
            </div>
        </div>
        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-lg flex items-center gap-md shadow-sm">
            <div class="w-10 h-10 rounded-xl bg-yellow-100 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-yellow-700">no_crash</span>
            </div>
            <div>
                <div class="text-[11px] text-outline font-semibold uppercase tracking-wide">Complets</div>
                <div id="stat-complets" class="font-bold text-title-md text-on-surface">—</div>
            </div>
        </div>
    </div>

    <!-- ── Liste des programmes (cards) ── -->
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm overflow-hidden">
        <div class="flex justify-between items-center p-lg border-b border-outline-variant bg-surface-container-low/30">
            <h3 class="font-title-md text-title-md text-on-surface">Voyages disponibles</h3>
            <button
                id="btn-refresh"
                class="inline-flex items-center gap-xs px-md py-xs bg-surface-container border border-outline-variant rounded-lg text-label-sm hover:bg-surface-container-high transition-all"
            >
                <span class="material-symbols-outlined text-[16px]">refresh</span>
                Actualiser
            </button>
        </div>

        <!-- Vue grille -->
        <div id="programmes-grid" class="divide-y divide-outline-variant/50">
            <div class="flex flex-col items-center justify-center py-xl text-outline gap-md">
                <div class="animate-spin w-10 h-10 border-2 border-primary border-t-transparent rounded-full"></div>
                <p class="text-body-md">Chargement des voyages...</p>
            </div>
        </div>
    </div>

    <!-- ── Vue tableau détaillée ── -->
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm overflow-hidden">
        <div class="p-lg border-b border-outline-variant bg-surface-container-low/30">
            <h3 class="font-title-md text-title-md text-on-surface">Tableau récapitulatif</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-body-md">
                <thead>
                    <tr class="bg-surface-container border-b border-outline-variant text-on-surface font-semibold text-[13px]">
                        <th class="p-md">Trajet</th>
                        <th class="p-md">Date</th>
                        <th class="p-md">Horaire</th>
                        <th class="p-md">Bus</th>
                        <th class="p-md">Conducteur</th>
                        <th class="p-md">Prix</th>
                        <th class="p-md">Places libres</th>
                        <th class="p-md">Statut</th>
                        <th class="p-md text-right">Action</th>
                    </tr>
                </thead>
                <tbody id="programmes-table-body" class="divide-y divide-outline-variant/40">
                    <tr><td colspan="9" class="p-xl text-center text-outline">Chargement...</td></tr>
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
                    <span class="material-symbols-outlined text-[64px] text-outline-variant">directions_bus</span>
                    <div class="text-center">
                        <p class="text-body-md font-medium text-on-surface">Aucun voyage trouvé pour ces critères</p>
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
            <div class="flex items-center justify-between p-lg hover:bg-surface-container-low/70 transition-colors ${borderClass}">
                <div class="flex items-center gap-md min-w-0">
                    <div class="w-12 h-12 bg-primary-container text-on-primary-container rounded-xl flex flex-col items-center justify-center shrink-0 shadow-sm">
                        <span class="material-symbols-outlined text-[20px]">directions_bus</span>
                        <span class="text-[9px] font-semibold">${esc(p.numero_plaque ?? '')}</span>
                    </div>
                    <div class="min-w-0">
                        <p class="font-label-lg text-on-surface font-semibold truncate">${esc(p.lieu_depart ?? '?')} → ${esc(p.lieu_arrivee ?? '?')}</p>
                        <div class="flex items-center flex-wrap gap-sm mt-xs">
                            <span class="inline-flex items-center gap-xs text-xs text-outline">
                                <span class="material-symbols-outlined text-[14px]">calendar_today</span>
                                ${esc(p.date_programme ?? '?')}
                            </span>
                            <span class="inline-flex items-center gap-xs text-xs text-outline">
                                <span class="material-symbols-outlined text-[14px]">schedule</span>
                                ${esc((p.heure_depart ?? '').slice(0, 5))}
                            </span>
                            <span class="inline-flex items-center gap-xs text-xs text-outline">
                                <span class="material-symbols-outlined text-[14px]">badge</span>
                                ${esc(p.conducteur_prenom ?? '')} ${esc(p.conducteur_nom ?? '')}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-lg shrink-0 ml-md">
                    <div class="text-right hidden sm:block">
                        <div class="text-xs text-outline">Tarif</div>
                        <div class="font-bold text-on-surface">${esc(p.prix ?? '0')} <span class="text-xs font-normal text-outline">${esc(p.code_currency ?? '')}</span></div>
                    </div>
                    <div class="text-center">
                        <span class="px-md py-xs font-bold text-sm rounded-full ${placesClass}">${places} place${places !== 1 ? 's' : ''}</span>
                    </div>
                    ${places > 0
                        ? `<a href="${BASE_URL}recept/reservations" class="px-md py-sm bg-primary text-on-primary rounded-lg font-label-sm hover:brightness-110 transition-all whitespace-nowrap text-xs">Réserver</a>`
                        : `<span class="px-md py-sm bg-error-container text-on-error-container rounded-lg font-label-sm text-xs">Complet</span>`
                    }
                </div>
            </div>`;
        }).join('');

        // Vue tableau
        tbody.innerHTML = items.map(p => {
            const places = parseInt(p.places_disponibles) || 0;
            const placesClass = places > 5 ? 'text-emerald-600 font-semibold' : places > 0 ? 'text-yellow-600 font-semibold' : 'text-red-500 font-semibold';
            const statutBg = places > 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700';
            return `
            <tr class="hover:bg-surface-container-low transition-colors">
                <td class="p-md font-medium">${esc(p.lieu_depart ?? '?')} → ${esc(p.lieu_arrivee ?? '?')}</td>
                <td class="p-md">${esc(p.date_programme ?? '?')}</td>
                <td class="p-md text-xs font-semibold">${esc((p.heure_depart ?? '').slice(0, 5))} - ${esc((p.heure_arrivee ?? '').slice(0, 5))}</td>
                <td class="p-md font-mono text-xs">${esc(p.numero_plaque ?? '—')}</td>
                <td class="p-md text-xs">${esc(p.conducteur_prenom ?? '')} ${esc(p.conducteur_nom ?? '')}</td>
                <td class="p-md font-semibold">${esc(p.prix ?? '0')} <span class="text-xs text-outline">${esc(p.code_currency ?? '')}</span></td>
                <td class="p-md"><span class="${placesClass}">${places}</span></td>
                <td class="p-md"><span class="px-sm py-xs text-[11px] font-semibold rounded-full ${statutBg}">${places > 0 ? 'Disponible' : 'Complet'}</span></td>
                <td class="p-md text-right">
                    ${places > 0
                        ? `<a href="${BASE_URL}recept/reservations" class="inline-flex items-center gap-xs px-sm py-xs bg-primary text-on-primary rounded-lg text-xs font-semibold hover:brightness-110 transition-all">
                               <span class="material-symbols-outlined text-[14px]">add</span> Réserver
                           </a>`
                        : `<span class="text-xs text-red-500 font-medium">Complet</span>`
                    }
                </td>
            </tr>`;
        }).join('');

    } catch (e) {
        grid.innerHTML = '<div class="p-lg text-center text-error">Erreur lors du chargement. Veuillez réessayer.</div>';
        tbody.innerHTML = '<tr><td colspan="9" class="p-xl text-center text-error">Erreur de chargement.</td></tr>';
    }
}

document.getElementById('btn-search-programme').addEventListener('click', searchProgrammes);
document.getElementById('btn-refresh').addEventListener('click', searchProgrammes);
document.getElementById('filter-date').addEventListener('change', searchProgrammes);
document.getElementById('search-programme').addEventListener('keypress', (e) => { if (e.key === 'Enter') searchProgrammes(); });

window.addEventListener('load', searchProgrammes);
</script>
<?= $this->endSection() ?>
