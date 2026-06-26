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
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-blue-50 text-blue-600">
                <span class="material-symbols-outlined text-[20px]">payments</span>
            </div>
            <div>
                <h2 class="text-lg font-semibold text-gray-900 tracking-tight">Paiements</h2>
                <p class="text-xs text-gray-500"><?= esc($today) ?> — <?= esc($displayName) ?> (Réceptionniste)</p>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <input type="date" id="filter-date-debut" value="<?= $todayIso ?>"
                   class="h-9 px-3 rounded-xl border-gray-300 border text-sm shadow-sm focus:border-blue-500 outline-none">
            <input type="date" id="filter-date-fin" value="<?= $todayIso ?>"
                   class="h-9 px-3 rounded-xl border-gray-300 border text-sm shadow-sm focus:border-blue-500 outline-none">
            <button id="btn-filter-paiements"
                    class="h-9 w-9 rounded-xl bg-blue-600 text-white flex items-center justify-center hover:bg-blue-700 transition-all shadow-sm"
                    title="Filtrer">
                <span class="material-symbols-outlined text-[20px]">filter_alt</span>
            </button>
            <button id="btn-refresh-paiements"
                    class="h-9 px-4 rounded-xl bg-white border border-gray-300 text-gray-700 flex items-center gap-2 hover:bg-gray-50 transition-all shadow-sm">
                <span class="material-symbols-outlined text-[18px]">refresh</span>
                <span class="text-sm font-medium">Actualiser</span>
            </button>
        </div>
    </div>

    <!-- ── Flash/Alert ── -->
    <div id="page-alert" class="hidden rounded-xl p-3 text-sm font-medium border transition-all"></div>

    <!-- ── KPI Cards ── -->
    <section class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
            <div class="flex items-center gap-2 mb-3">
                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-blue-50 text-blue-600">
                    <span class="material-symbols-outlined text-[18px]">payments</span>
                </div>
            </div>
            <span class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Total encaissé</span>
            <strong id="kpi-total" class="block text-xl font-bold text-gray-900 mt-1">—</strong>
            <span class="block text-xs text-gray-500 mt-1">Sur la période</span>
        </div>
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
            <div class="flex items-center gap-2 mb-3">
                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600">
                    <span class="material-symbols-outlined text-[18px]">check_circle</span>
                </div>
            </div>
            <span class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Nb. paiements</span>
            <strong id="kpi-count" class="block text-xl font-bold text-gray-900 mt-1">—</strong>
            <span class="block text-xs text-gray-500 mt-1">Transactions traitées</span>
        </div>
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
            <div class="flex items-center gap-2 mb-3">
                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-amber-50 text-amber-600">
                    <span class="material-symbols-outlined text-[18px]">hourglass_empty</span>
                </div>
            </div>
            <span class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Solde restant dû</span>
            <strong id="kpi-restant" class="block text-xl font-bold text-gray-900 mt-1">—</strong>
            <span class="block text-xs text-gray-500 mt-1">Réservations non soldées</span>
        </div>
    </section>

    <!-- ── Recherche ── -->
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
        <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
            <span class="material-symbols-outlined text-blue-500 text-[18px]">manage_search</span>
            Rechercher un paiement
        </h3>
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">search</span>
                <input id="search-paiement" type="text"
                       placeholder="Référence réservation, nom client..."
                       class="w-full pl-10 pr-3 h-9 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
            </div>
            <select id="filter-mode" class="h-9 px-3 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                <option value="">Tous les modes</option>
                <?php foreach ($modes_paiement ?? [] as $mp): ?>
                    <option value="<?= esc($mp['id_mode_paiement']) ?>"><?= esc($mp['libelle']) ?></option>
                <?php endforeach; ?>
            </select>
            <button id="btn-search-paiement"
                    class="h-9 px-4 rounded-xl bg-blue-600 text-white flex items-center gap-2 hover:bg-blue-700 transition-all text-sm font-medium shadow-sm">
                <span class="material-symbols-outlined text-[18px]">filter_list</span>
                Filtrer
            </button>
        </div>
    </div>

    <!-- ── Tableau des paiements ── -->
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
            <div>
                <h3 class="text-base font-semibold text-gray-900">Historique des paiements</h3>
                <p class="text-xs text-gray-500 mt-0.5">Paiements enregistrés pour les réservations</p>
            </div>
            <span id="paiements-count" class="text-xs font-semibold text-gray-500 bg-gray-100 px-2 py-1 rounded-full">0 entrée(s)</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-gray-600 uppercase text-[11px] font-semibold bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">Référence</th>
                        <th class="px-6 py-4">Client</th>
                        <th class="px-6 py-4">Trajet</th>
                        <th class="px-6 py-4">Mode</th>
                        <th class="px-6 py-4">Réf. Transaction</th>
                        <th class="px-6 py-4">Date paiement</th>
                        <th class="px-6 py-4 text-right">Montant</th>
                    </tr>
                </thead>
                <tbody id="paiements-table-body" class="divide-y divide-gray-100">
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                            <div class="flex flex-col items-center gap-2">
                                <div class="animate-spin w-8 h-8 border-2 border-blue-500 border-t-transparent rounded-full"></div>
                                <p class="text-sm">Chargement en cours...</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div id="paiements-pagination" class="flex justify-between items-center px-6 py-3 border-t border-gray-100 bg-gray-50/50"></div>
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

function showPageAlert(message, type = 'success') {
    const el = document.getElementById('page-alert');
    el.className = 'rounded-xl p-3 text-sm font-medium border transition-all ' + (
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

function fmtMoney(v) { return parseFloat(v || 0).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ' '); }

// ── Chargement des paiements ────────────────────────────────────────────────
let currentPage = 1;

async function loadPaiements(page = 1) {
    currentPage = page;
    const body   = document.getElementById('paiements-table-body');
    const pagin  = document.getElementById('paiements-pagination');
    const count  = document.getElementById('paiements-count');

    const debut  = document.getElementById('filter-date-debut').value || TODAY;
    const fin    = document.getElementById('filter-date-fin').value   || TODAY;
    const search = document.getElementById('search-paiement').value.trim();
    const mode   = document.getElementById('filter-mode').value;

    body.innerHTML = `<tr><td colspan="7" class="px-6 py-12 text-center text-gray-400">
        <div class="flex flex-col items-center gap-2">
            <div class="animate-spin w-8 h-8 border-2 border-blue-500 border-t-transparent rounded-full"></div>
            <p class="text-sm">Chargement...</p>
        </div></td></tr>`;

    try {
        const params = new URLSearchParams({ page, per_page: 20, date_debut: debut, date_fin: fin });
        if (search) params.set('search', search);
        if (mode)   params.set('id_mode_paiement', mode);

        const res  = await apiFetch(`${BASE_URL}api/paiements?${params}`);
        const json = await res.json();
        const items = json.data?.items ?? [];
        const meta  = json.data?.meta  ?? { page: 1, total_pages: 1, total: 0 };

        // KPI
        let totalMontant = 0, totalRestant = 0;
        items.forEach(p => {
            totalMontant += parseFloat(p.montant_paye ?? 0);
            totalRestant += parseFloat(p.restant_du  ?? 0);
        });
        document.getElementById('kpi-total').textContent   = fmtMoney(totalMontant) + ' USD';
        document.getElementById('kpi-count').textContent   = meta.total;
        document.getElementById('kpi-restant').textContent = fmtMoney(totalRestant) + ' USD';
        count.textContent = `${meta.total} entrée(s)`;

        if (!items.length) {
            body.innerHTML = `<tr><td colspan="7" class="px-6 py-12 text-center text-gray-400">
                <div class="flex flex-col items-center gap-2">
                    <span class="material-symbols-outlined text-[36px]">payments</span>
                    <p class="text-sm font-medium">Aucun paiement trouvé pour ces critères.</p>
                </div></td></tr>`;
            pagin.innerHTML = '';
            return;
        }

        body.innerHTML = items.map(p => {
            const montant = parseFloat(p.montant_paye ?? 0);
            const mode    = esc(p.mode_paiement ?? '—');
            const ref     = esc(p.reference_paiement ?? '—');
            const datePay = esc(p.date_paiement ?? '—');

            return `
            <tr class="hover:bg-gray-50/50 transition-colors">
                <td class="px-6 py-4 font-semibold text-gray-900">${esc(p.reference_reservation)}</td>
                <td class="px-6 py-4">
                    <div class="font-medium text-gray-900">${esc(p.client_nom ?? '—')}</div>
                    <div class="text-xs text-gray-500 mt-0.5">${esc(p.client_telephone ?? '')}</div>
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[16px] text-gray-400">route</span>
                        <span class="text-gray-700">${esc(p.trajet ?? '—')}</span>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100">
                        ${mode}
                    </span>
                </td>
                <td class="px-6 py-4 font-mono text-xs text-gray-600">${ref}</td>
                <td class="px-6 py-4 text-gray-600">${datePay}</td>
                <td class="px-6 py-4 text-right font-semibold text-gray-900">
                    ${fmtMoney(montant)} <span class="text-xs text-gray-400 font-normal">${esc(p.symbole ?? 'USD')}</span>
                </td>
            </tr>`;
        }).join('');

        // Pagination
        let paginHtml = `<span class="text-xs text-gray-500">Total: ${meta.total} paiement(s)</span><div class="inline-flex gap-1">`;
        if (meta.page > 1)
            paginHtml += `<button onclick="loadPaiements(${meta.page - 1})" class="px-3 py-1.5 border border-gray-300 bg-white rounded-lg hover:bg-gray-50 text-xs font-medium transition-all">Précédent</button>`;
        paginHtml += `<span class="px-3 py-1.5 text-xs font-semibold text-gray-700">Page ${meta.page} / ${meta.total_pages}</span>`;
        if (meta.page < meta.total_pages)
            paginHtml += `<button onclick="loadPaiements(${meta.page + 1})" class="px-3 py-1.5 border border-gray-300 bg-white rounded-lg hover:bg-gray-50 text-xs font-medium transition-all">Suivant</button>`;
        paginHtml += `</div>`;
        pagin.innerHTML = paginHtml;

    } catch(e) {
        body.innerHTML = `<tr><td colspan="7" class="px-6 py-4 text-center text-red-600 text-sm font-medium">Erreur lors de la récupération des paiements.</td></tr>`;
        showPageAlert('Erreur réseau. Veuillez réessayer.', 'error');
    }
}

// ── Événements ────────────────────────────────────────────────────────────────
document.getElementById('btn-filter-paiements').addEventListener('click', () => loadPaiements(1));
document.getElementById('btn-search-paiement').addEventListener('click', () => loadPaiements(1));
document.getElementById('btn-refresh-paiements').addEventListener('click', () => loadPaiements(currentPage));
document.getElementById('search-paiement').addEventListener('keydown', e => { if (e.key === 'Enter') loadPaiements(1); });

window.addEventListener('load', () => loadPaiements(1));
</script>
<?= $this->endSection() ?>
