<?= $this->extend($layout ?? 'web/layouts/admin') ?>

<?= $this->section('content') ?>

<main class="px-4 pb-12 max-w-[1600px] mx-auto">
    <!-- ── En-tête de page ── -->
    <div class="w-full h-[60px] mb-6 rounded-2xl flex items-center justify-between px-6 bg-white border-b border-gray-200 shadow-sm transition-all">
        <div class="flex items-center gap-3 h-full">
            <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-blue-50 text-blue-600">
                <span class="material-symbols-outlined text-[20px]">confirmation_number</span>
            </div>
            <h2 class="text-lg font-semibold text-gray-900 tracking-tight">Réservations</h2>
        </div>
        <div class="flex items-center gap-3">
            <button
                id="btn-open-modal"
                onclick="openModal()"
                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition-all shadow-sm"
            >
                <span class="material-symbols-outlined text-[18px]">add</span>
                <span>Nouvelle réservation</span>
            </button>
        </div>
    </div>

    <!-- ── Flash messages ── -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm font-medium flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            <?= esc(session()->getFlashdata('success')) ?>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm font-medium flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">error</span>
            <?= esc(session()->getFlashdata('error')) ?>
        </div>
    <?php endif; ?>

    <!-- ── Filtres ── -->
    <section class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4 bg-white p-4 rounded-2xl border border-gray-200 shadow-sm">
        <input
            id="search-input"
            type="text"
            placeholder="Référence, nom, téléphone..."
            class="w-full rounded-xl border-gray-300 border px-4 py-2 text-sm shadow-sm focus:border-blue-500 outline-none"
        >
        <input
            id="date-filter"
            type="date"
            class="w-full rounded-xl border-gray-300 border px-4 py-2 text-sm shadow-sm focus:border-blue-500 outline-none"
        >
        <select
            id="status-filter"
            class="w-full rounded-xl border-gray-300 border px-4 py-2 text-sm shadow-sm focus:border-blue-500 outline-none bg-white"
        >
            <option value="">Tous les statuts</option>
            <option value="EN ATTENTE">EN ATTENTE</option>
            <option value="CONFIRME">CONFIRMÉ</option>
            <option value="ANNULE">ANNULÉ</option>
        </select>
        <button
            id="btn-filter"
            class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition-all"
        >
            Filtrer
        </button>
    </section>

    <!-- ── Tableau des réservations ── -->
    <section class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
            <h3 class="text-base font-semibold text-gray-900">Liste des réservations</h3>
            <span class="text-xs font-medium text-gray-500 bg-white px-2 py-1 rounded-md border border-gray-200">
                Total : <?= count($reservations ?? []) ?>
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left" id="reservations-table">
                <thead class="text-gray-600 uppercase text-[11px] font-semibold bg-gray-50">
                    <tr>
                        <th class="px-6 py-4">Référence</th>
                        <th class="px-6 py-4">Client</th>
                        <th class="px-6 py-4">Programme</th>
                        <th class="px-6 py-4 text-right">Montant</th>
                        <th class="px-6 py-4 text-center">Paiement</th>
                        <th class="px-6 py-4 text-center">Statut</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100" id="reservations-tbody">

                    <?php if (empty($reservations)) : ?>
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-3 text-gray-400">
                                    <span class="material-symbols-outlined text-[48px]">inbox</span>
                                    <p class="text-sm font-medium">Aucune réservation pour le moment</p>
                                </div>
                            </td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($reservations as $res) : ?>
                            <?php
                                $statut = strtoupper($res['statut_reservation'] ?? 'INCONNU');
                                $badgeClass = 'bg-gray-100 text-gray-800 border-gray-200';
                                if ($statut === 'EN ATTENTE') {
                                    $badgeClass = 'bg-yellow-50 text-yellow-700 border-yellow-200';
                                } elseif (in_array($statut, ['CONFIRME', 'CONFIRMÉ'])) {
                                    $badgeClass = 'bg-green-50 text-green-700 border-green-200';
                                } elseif (in_array($statut, ['ANNULE', 'ANNULÉ'])) {
                                    $badgeClass = 'bg-red-50 text-red-700 border-red-200';
                                }
                                $paiementStatut = $res['statut_paiement'] ?? null;
                                $paiementClass = 'bg-gray-100 text-gray-600 border-gray-200';
                                if ($paiementStatut === 'Valide') {
                                    $paiementClass = 'bg-green-50 text-green-700 border-green-200';
                                } elseif ($paiementStatut === 'En attente') {
                                    $paiementClass = 'bg-yellow-50 text-yellow-700 border-yellow-200';
                                }
                            ?>
                            <tr class="hover:bg-gray-50 transition-colors reservation-row"
                                data-ref="<?= strtolower(esc($res['reference_reservation'])) ?>"
                                data-client="<?= strtolower(esc($res['client_nom'])) ?>"
                                data-date="<?= esc($res['date_programme'] ?? '') ?>"
                                data-statut="<?= strtoupper(esc($res['statut_reservation'] ?? '')) ?>"
                            >
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    <?= esc($res['reference_reservation']) ?>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs shrink-0">
                                            <?= strtoupper(substr($res['client_nom'] ?? '?', 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900"><?= esc($res['client_nom']) ?></div>
                                            <div class="text-xs text-gray-500"><?= esc($res['client_telephone']) ?></div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">
                                        <?= esc($res['lieu_depart']) ?> <span class="text-gray-400">→</span> <?= esc($res['lieu_arrivee']) ?>
                                    </div>
                                    <div class="text-xs text-gray-500 flex items-center gap-1 mt-0.5">
                                        <span class="material-symbols-outlined text-[12px]">calendar_today</span>
                                        <?= date('d/m/Y', strtotime($res['date_programme'])) ?>
                                        &nbsp;·&nbsp;
                                        <?= substr($res['heure_depart'] ?? '', 0, 5) ?>
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-right font-medium">
                                    <?= number_format($res['montant_final'] ?? 0, 2, '.', ' ') ?>
                                    <span class="text-gray-500 text-xs"><?= esc($res['symbole'] ?? '') ?></span>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <?php if ($paiementStatut): ?>
                                        <span class="px-2.5 py-1 text-[11px] font-semibold rounded-full <?= $paiementClass ?>">
                                            <?= esc($paiementStatut) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-xs text-gray-400">—</span>
                                    <?php endif; ?>
                                </td>

                                <td class="px-2 py-4 text-center">
                                    <span class="px-2 py-1 text-[11px] font-semibold rounded-full <?= $badgeClass ?>">
                                        <?= esc(ucfirst(strtolower($res['statut_reservation']))) ?>
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <!-- Imprimer billet -->
                                        <a
                                            href="<?= base_url('admin/reservation/' . $res['id_reservation'] . '/ticket') ?>"
                                            target="_blank"
                                            title="Imprimer le billet"
                                            class="p-1.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                        >
                                            <span class="material-symbols-outlined text-[18px]">print</span>
                                        </a>
                                        <!-- Annuler la réservation (admin peut annuler) -->
                                        <?php if (!in_array($statut, ['ANNULE', 'ANNULÉ'])): ?>
                                            <button
                                                title="Annuler la réservation"
                                                onclick="annulerReservation(<?= (int)$res['id_reservation'] ?>, '<?= esc($res['reference_reservation']) ?>')"
                                                class="p-1.5 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                            >
                                                <span class="material-symbols-outlined text-[18px]">cancel</span>
                                            </button>
                                        <?php endif; ?>
                                        <!-- Voir détail -->
                                        <button
                                            title="Voir les détails"
                                            onclick="voirDetails(<?= (int)$res['id_reservation'] ?>)"
                                            class="p-1.5 text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors"
                                        >
                                            <span class="material-symbols-outlined text-[18px]">visibility</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>

                </tbody>
            </table>
        </div>
    </section>
</main>

<!-- ══════════════════════════════════════════════════
     MODAL — Nouvelle réservation
══════════════════════════════════════════════════ -->
<div id="reservationModal" class="fixed inset-0 z-50 hidden bg-gray-900/50 backdrop-blur-sm overflow-y-auto" role="dialog" aria-modal="true" aria-labelledby="modal-title">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 sm:p-0">
        <div class="relative bg-white rounded-2xl text-left overflow-hidden shadow-xl sm:my-8 sm:max-w-2xl w-full">

            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[18px]">confirmation_number</span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900" id="modal-title">Nouvelle réservation</h3>
                </div>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-500 hover:bg-gray-100 p-1.5 rounded-full transition-colors">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <!-- Erreurs du formulaire -->
            <div id="form-errors" class="hidden mx-6 mt-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm font-medium"></div>

            <div class="px-6 py-6 space-y-5" id="modal-content">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Recherche client -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Client <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="client-search"
                            type="text"
                            placeholder="Nom ou téléphone..."
                            class="w-full rounded-xl border-gray-300 border px-4 py-2.5 text-sm shadow-sm focus:border-blue-500 outline-none"
                            autocomplete="off"
                        >
                        <div id="client-suggestions" class="absolute z-10 mt-1 w-64 bg-white border border-gray-200 rounded-xl shadow-lg hidden max-h-48 overflow-y-auto"></div>
                        <input type="hidden" id="id_client" name="id_client">
                        <p id="client-selected" class="mt-1 text-xs text-green-600 font-medium hidden"></p>
                    </div>

                    <!-- Programme -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Programme <span class="text-red-500">*</span>
                        </label>
                        <select id="id_programme" name="id_programme" class="w-full rounded-xl border-gray-300 border px-4 py-2.5 text-sm shadow-sm focus:border-blue-500 outline-none bg-white">
                            <option value="">Chargement...</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <!-- Nombre de places -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Nbre de places <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="nombre_places"
                            type="number"
                            name="nombre_places"
                            min="1"
                            value="1"
                            class="w-full rounded-xl border-gray-300 border px-4 py-2.5 text-sm shadow-sm focus:border-blue-500 outline-none"
                        >
                    </div>

                    <!-- Lieu de réservation -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Lieu de montée</label>
                        <select id="id_lieu_reservation" name="id_lieu_reservation" class="w-full rounded-xl border-gray-300 border px-4 py-2.5 text-sm shadow-sm focus:border-blue-500 outline-none bg-white">
                            <option value="">Départ principal</option>
                        </select>
                    </div>

                    <!-- Mode de paiement -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Mode de paiement</label>
                        <select id="id_mode_paiement" name="id_mode_paiement" class="w-full rounded-xl border-gray-300 border px-4 py-2.5 text-sm shadow-sm focus:border-blue-500 outline-none bg-white">
                            <option value="">Sans paiement</option>
                            <?php foreach ($modes_paiement ?? [] as $mp): ?>
                                <option value="<?= esc($mp['id_mode_paiement']) ?>"><?= esc($mp['libelle']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Notes -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Notes (optionnel)</label>
                    <textarea
                        id="notes"
                        name="notes"
                        rows="2"
                        placeholder="Informations complémentaires..."
                        class="w-full rounded-xl border-gray-300 border px-4 py-2.5 text-sm shadow-sm focus:border-blue-500 outline-none resize-none"
                    ></textarea>
                </div>

            </div>

            <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100 flex justify-end gap-3">
                <button
                    type="button"
                    onclick="closeModal()"
                    class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-50 transition-all"
                >
                    Annuler
                </button>
                <button
                    type="button"
                    id="btn-submit-reservation"
                    onclick="submitReservation()"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 shadow-sm transition-all"
                >
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

// ── Helpers fetch ──────────────────────────────────────────────────────────
async function apiFetch(url, options = {}) {
    const headers = { Accept: 'application/json', Authorization: `Bearer ${API_TOKEN}`, ...(options.headers || {}) };
    const response = await fetch(url, { ...options, headers });
    return response;
}

// ── Modal ──────────────────────────────────────────────────────────────────
function openModal() {
    document.getElementById('reservationModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    loadProgrammes();
}

function closeModal() {
    document.getElementById('reservationModal').classList.add('hidden');
    document.body.style.overflow = '';
    clearFormErrors();
}

function clearFormErrors() {
    const el = document.getElementById('form-errors');
    el.textContent = '';
    el.classList.add('hidden');
}

function showFormErrors(messages) {
    const el = document.getElementById('form-errors');
    el.innerHTML = '<ul class="list-disc pl-5 space-y-1">' + messages.map(m => `<li>${m}</li>`).join('') + '</ul>';
    el.classList.remove('hidden');
}

// ── Chargement des programmes ──────────────────────────────────────────────
async function loadProgrammes() {
    const select = document.getElementById('id_programme');
    select.innerHTML = '<option value="">Chargement...</option>';
    try {
        const today = new Date().toISOString().slice(0, 10);
        const response = await apiFetch(`${BASE_URL}api/planification?per_page=100&date_debut=${today}`);
        if (!response.ok) throw new Error();
        const json = await response.json();
        const items = json.data?.items ?? [];
        select.innerHTML = '<option value="">Sélectionner un programme</option>';
        items.forEach(p => {
            const depart = p.lieu_depart ?? '?';
            const arrivee = p.lieu_arrivee ?? '?';
            const date = p.date_programme ?? '';
            const heure = (p.heure_depart ?? '').slice(0, 5);
            const places = p.places_disponibles ?? 0;
            const opt = document.createElement('option');
            opt.value = p.id_programme;
            opt.textContent = `${date} | ${depart} → ${arrivee} ${heure} (${places} places)`;
            opt.dataset.lieux = JSON.stringify(p.arrets ?? []);
            select.appendChild(opt);
        });
        select.addEventListener('change', onProgrammeChange);
    } catch {
        select.innerHTML = '<option value="">Erreur de chargement</option>';
    }
}

function onProgrammeChange() {
    const select = document.getElementById('id_programme');
    const selected = select.options[select.selectedIndex];
    const lieux = document.getElementById('id_lieu_reservation');
    lieux.innerHTML = '<option value="">Départ principal</option>';
    try {
        const arrets = JSON.parse(selected.dataset.lieux || '[]');
        arrets.forEach(a => {
            const opt = document.createElement('option');
            opt.value = a.id_lieu;
            opt.textContent = a.nom_lieu;
            lieux.appendChild(opt);
        });
    } catch {}
}

// ── Recherche client ───────────────────────────────────────────────────────
let clientSearchTimeout = null;
document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('client-search');
    const suggestions = document.getElementById('client-suggestions');
    if (!input) return;

    input.addEventListener('input', () => {
        clearTimeout(clientSearchTimeout);
        clientSearchTimeout = setTimeout(async () => {
            const q = input.value.trim();
            if (q.length < 2) { suggestions.classList.add('hidden'); return; }
            try {
                const response = await apiFetch(`${BASE_URL}api/clients?search=${encodeURIComponent(q)}&per_page=10`);
                const json = await response.json();
                const items = json.data?.items ?? [];
                if (!items.length) { suggestions.classList.add('hidden'); return; }
                suggestions.innerHTML = items.map(c =>
                    `<div class="px-4 py-2 cursor-pointer hover:bg-blue-50 text-sm" onclick="selectClient(${c.id_client}, '${(c.nom ?? '').replace(/'/g, "\\'")}', '${(c.telephone ?? '').replace(/'/g, "\\'")}')">${c.nom} <span class="text-gray-400 text-xs">${c.telephone ?? ''}</span></div>`
                ).join('');
                suggestions.classList.remove('hidden');
            } catch {}
        }, 300);
    });

    document.addEventListener('click', (e) => {
        if (!input.contains(e.target)) suggestions.classList.add('hidden');
    });
});

function selectClient(id, nom, tel) {
    document.getElementById('id_client').value = id;
    document.getElementById('client-search').value = nom + (tel ? ` — ${tel}` : '');
    document.getElementById('client-suggestions').classList.add('hidden');
    const p = document.getElementById('client-selected');
    p.textContent = `✓ Client sélectionné : ${nom}`;
    p.classList.remove('hidden');
}

// ── Soumission ─────────────────────────────────────────────────────────────
async function submitReservation() {
    clearFormErrors();
    const btn = document.getElementById('btn-submit-reservation');
    btn.disabled = true;
    btn.classList.add('opacity-60', 'cursor-wait');

    const payload = {
        id_client:          parseInt(document.getElementById('id_client').value) || 0,
        id_programme:       parseInt(document.getElementById('id_programme').value) || 0,
        nombre_places:      parseInt(document.getElementById('nombre_places').value) || 1,
        id_lieu_reservation:parseInt(document.getElementById('id_lieu_reservation').value) || null,
        id_mode_paiement:   parseInt(document.getElementById('id_mode_paiement').value) || null,
        notes:              document.getElementById('notes').value.trim() || null,
    };

    const errors = [];
    if (!payload.id_client)    errors.push('Veuillez sélectionner un client.');
    if (!payload.id_programme) errors.push('Veuillez sélectionner un programme.');
    if (payload.nombre_places < 1) errors.push('Le nombre de places doit être au moins 1.');
    if (errors.length) { showFormErrors(errors); btn.disabled = false; btn.classList.remove('opacity-60', 'cursor-wait'); return; }

    try {
        const response = await apiFetch(`${BASE_URL}api/reservations`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload),
        });
        const json = await response.json();
        if (!response.ok || json.success === false) {
            const errs = json.errors ? Object.values(json.errors).flat() : [json.message || 'Erreur lors de la création.'];
            showFormErrors(errs);
            return;
        }
        closeModal();
        window.location.reload();
    } catch (e) {
        showFormErrors(['Une erreur réseau est survenue.']);
    } finally {
        btn.disabled = false;
        btn.classList.remove('opacity-60', 'cursor-wait');
    }
}

// ── Annulation ─────────────────────────────────────────────────────────────
async function annulerReservation(id, ref) {
    if (!confirm(`Annuler la réservation ${ref} ?\nCette action est irréversible.`)) return;
    try {
        const response = await apiFetch(`${BASE_URL}api/reservations/${id}/annuler`, { method: 'POST', headers: { 'Content-Type': 'application/json' } });
        const json = await response.json();
        if (!response.ok || json.success === false) { alert(json.message || 'Impossible d\'annuler.'); return; }
        window.location.reload();
    } catch {
        alert('Erreur réseau.');
    }
}

// ── Voir détails ───────────────────────────────────────────────────────────
function voirDetails(id) {
    window.open(`${BASE_URL}admin/reservation/${id}/ticket`, '_blank');
}

// ── Filtre local ───────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('btn-filter')?.addEventListener('click', filterTable);
    document.getElementById('search-input')?.addEventListener('keyup', filterTable);
    document.getElementById('date-filter')?.addEventListener('change', filterTable);
    document.getElementById('status-filter')?.addEventListener('change', filterTable);
});

function filterTable() {
    const search = document.getElementById('search-input')?.value.toLowerCase() ?? '';
    const date   = document.getElementById('date-filter')?.value ?? '';
    const statut = document.getElementById('status-filter')?.value.toUpperCase() ?? '';
    const rows   = document.querySelectorAll('.reservation-row');
    rows.forEach(row => {
        const matchSearch = !search || row.dataset.ref.includes(search) || row.dataset.client.includes(search);
        const matchDate   = !date   || row.dataset.date === date;
        const matchStatut = !statut || row.dataset.statut === statut;
        row.style.display = (matchSearch && matchDate && matchStatut) ? '' : 'none';
    });
}
</script>
<?= $this->endSection() ?>
