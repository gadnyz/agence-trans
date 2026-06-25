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
            <h2 class="font-headline-lg text-headline-lg text-on-surface">Historique des Paiements</h2>
            <p class="text-body-lg text-outline"><?= esc($today) ?> &mdash; <?= esc($displayName) ?></p>
        </div>
        <div class="flex items-center gap-sm">
            <div id="stat-total-badge" class="flex items-center gap-sm bg-surface-container-low border border-outline-variant rounded-xl px-lg py-sm">
                <span class="material-symbols-outlined text-primary text-[22px]">payments</span>
                <div>
                    <div class="text-[11px] text-outline font-semibold uppercase tracking-wide">Total encaissé</div>
                    <div id="stat-total-montant" class="font-bold text-on-surface text-title-md">—</div>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Flash/Alert global ── -->
    <div id="page-alert" class="hidden rounded-xl p-md text-sm font-medium border transition-all"></div>

    <!-- ── Filtres ── -->
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-lg shadow-sm">
        <h3 class="font-title-sm text-title-sm text-on-surface mb-md flex items-center gap-sm">
            <span class="material-symbols-outlined text-primary text-[20px]">filter_list</span>
            Filtrer les paiements
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-md">
            <div class="relative sm:col-span-2">
                <span class="material-symbols-outlined absolute left-md top-1/2 -translate-y-1/2 text-outline text-[20px]">search</span>
                <input
                    id="search-payment-text"
                    type="text"
                    placeholder="Réf paiement, Réf réservation, Client..."
                    class="w-full pl-xl pr-md py-sm bg-surface-container-low border border-outline-variant rounded-lg text-body-md focus:ring-2 focus:ring-primary outline-none"
                />
            </div>
            <input
                type="date"
                id="filter-payment-date"
                class="px-md py-sm bg-surface-container-low border border-outline-variant rounded-lg text-body-md focus:ring-2 focus:ring-primary outline-none"
            />
            <button
                id="btn-search-payments"
                class="inline-flex items-center justify-center gap-sm px-lg py-sm bg-primary text-on-primary rounded-lg font-label-lg hover:brightness-110 transition-all"
            >
                <span class="material-symbols-outlined text-[20px]">search</span>
                Rechercher
            </button>
        </div>
    </div>

    <!-- ── Tableau des paiements ── -->
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm overflow-hidden">
        <div class="flex justify-between items-center p-lg border-b border-outline-variant bg-surface-container-low/30">
            <h3 class="font-title-md text-title-md text-on-surface">Transactions enregistrées</h3>
            <button
                id="btn-refresh-payments"
                class="inline-flex items-center gap-xs px-md py-xs bg-surface-container border border-outline-variant rounded-lg text-label-sm hover:bg-surface-container-high transition-all"
            >
                <span class="material-symbols-outlined text-[16px]">refresh</span>
                Actualiser
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-body-md">
                <thead>
                    <tr class="bg-surface-container border-b border-outline-variant text-on-surface font-semibold">
                        <th class="p-md">Date</th>
                        <th class="p-md">Réf Transaction</th>
                        <th class="p-md">Réservation</th>
                        <th class="p-md">Client</th>
                        <th class="p-md">Mode</th>
                        <th class="p-md">Montant</th>
                        <th class="p-md">Statut</th>
                        <th class="p-md text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="payments-table-body" class="divide-y divide-outline-variant/40">
                    <tr>
                        <td colspan="8" class="p-xl text-center text-outline">Chargement...</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div id="payments-pagination" class="flex justify-between items-center p-md border-t border-outline-variant bg-surface-container-low/20"></div>
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
    el.className = 'rounded-xl p-md text-sm font-medium border transition-all ' + (
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

let currentPage = 1;

async function loadPayments(page = 1) {
    currentPage = page;
    const body   = document.getElementById('payments-table-body');
    const pagin  = document.getElementById('payments-pagination');
    const search = document.getElementById('search-payment-text').value.trim();
    const date   = document.getElementById('filter-payment-date').value;

    body.innerHTML = '<tr><td colspan="8" class="p-xl text-center"><div class="inline-block animate-spin w-6 h-6 border-2 border-primary border-t-transparent rounded-full"></div></td></tr>';

    try {
        const params = new URLSearchParams({ page, per_page: 20 });
        if (search) params.set('search', search);
        // NOTE: The /api/reservations endpoint returns reservation data including payment info
        const response = await apiFetch(`${BASE_URL}api/reservations?${params}`);
        const json = await response.json();
        const items = json.data?.items ?? [];
        const meta  = json.data?.meta ?? { page: 1, total_pages: 1, total: 0 };

        // Only show reservations that have payment info
        const payments = items.filter(r => r.reference_paiement || parseFloat(r.montant_paye ?? 0) > 0);

        if (!payments.length) {
            body.innerHTML = '<tr><td colspan="8" class="p-xl text-center text-outline">Aucun paiement trouvé pour ces critères.</td></tr>';
            pagin.innerHTML = '';
            document.getElementById('stat-total-montant').textContent = '0.00';
            return;
        }

        // Calculate total
        const totalMontant = payments.reduce((sum, p) => sum + parseFloat(p.montant_paye ?? 0), 0);
        const firstCurrency = payments[0]?.symbole || payments[0]?.code_currency || '';
        document.getElementById('stat-total-montant').textContent = `${totalMontant.toFixed(2)} ${firstCurrency}`;

        body.innerHTML = payments.map(p => {
            const datePaiement = p.date_paiement ? p.date_paiement.split(' ')[0] : '-';
            const statut = (p.statut_paiement ?? 'VALIDÉ').toUpperCase();
            const statusClass = statut.includes('VALID') || statut.includes('CONFIRM')
                ? 'bg-emerald-100 text-emerald-800'
                : 'bg-red-100 text-red-800';
            const montant = parseFloat(p.montant_paye ?? 0).toFixed(2);
            const symb = p.symbole || p.code_currency || '';
            const modePaiement = p.mode_paiement ?? 'Espèces';

            return `
            <tr class="hover:bg-surface-container-low transition-colors">
                <td class="p-md text-xs text-outline">${esc(datePaiement)}</td>
                <td class="p-md font-mono font-semibold text-xs">${esc(p.reference_paiement ?? '—')}</td>
                <td class="p-md font-medium text-primary cursor-pointer hover:underline" onclick="window.open('${BASE_URL}recept/reservations/${p.id_reservation}/ticket', '_blank')" title="Voir le billet">${esc(p.reference_reservation)}</td>
                <td class="p-md">
                    <div class="font-medium">${esc(p.client_nom)}</div>
                    <div class="text-xs text-outline">${esc(p.client_telephone ?? '')}</div>
                </td>
                <td class="p-md">
                    <span class="inline-flex items-center gap-xs px-sm py-xs bg-surface-container text-on-surface-variant rounded-full text-xs font-medium">
                        <span class="material-symbols-outlined text-[12px]">payments</span>
                        ${esc(modePaiement)}
                    </span>
                </td>
                <td class="p-md font-bold text-on-surface">${esc(montant)} <span class="text-xs text-outline font-normal">${esc(symb)}</span></td>
                <td class="p-md"><span class="px-sm py-xs text-[11px] font-semibold rounded-full ${statusClass}">${esc(statut)}</span></td>
                <td class="p-md text-right">
                    <a href="${BASE_URL}recept/reservations/${p.id_reservation}/ticket" target="_blank"
                       class="inline-flex items-center gap-xs px-sm py-xs bg-primary/10 hover:bg-primary/20 text-primary rounded-lg text-xs font-semibold transition-all">
                        <span class="material-symbols-outlined text-[14px]">receipt</span>
                        Reçu
                    </a>
                </td>
            </tr>`;
        }).join('');

        let paginHtml = `<span class="text-xs text-outline">Total: ${meta.total} entrée(s)</span><div class="inline-flex gap-xs">`;
        if (meta.page > 1) paginHtml += `<button onclick="loadPayments(${meta.page - 1})" class="px-sm py-xs border border-outline-variant bg-surface-container rounded hover:bg-surface-container-high text-xs">Précédent</button>`;
        paginHtml += `<span class="px-md py-xs text-xs font-semibold">Page ${meta.page} / ${meta.total_pages}</span>`;
        if (meta.page < meta.total_pages) paginHtml += `<button onclick="loadPayments(${meta.page + 1})" class="px-sm py-xs border border-outline-variant bg-surface-container rounded hover:bg-surface-container-high text-xs">Suivant</button>`;
        paginHtml += `</div>`;
        pagin.innerHTML = paginHtml;

    } catch (e) {
        body.innerHTML = '<tr><td colspan="8" class="p-xl text-center text-error">Erreur lors du chargement des paiements.</td></tr>';
    }
}

document.getElementById('btn-search-payments').addEventListener('click', () => loadPayments(1));
document.getElementById('btn-refresh-payments').addEventListener('click', () => loadPayments(currentPage));
document.getElementById('search-payment-text').addEventListener('keypress', (e) => { if (e.key === 'Enter') loadPayments(1); });

window.addEventListener('load', () => loadPayments(1));
</script>
<?= $this->endSection() ?>
