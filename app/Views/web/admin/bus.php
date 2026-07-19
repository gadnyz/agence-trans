<?= $this->extend($layout ?? 'web/layouts/admin') ?>

<?= $this->section('content') ?>

<main class="px-4 pb-12 max-w-[1600px] mx-auto">
    <!-- ── En-tête de page ── -->
    <div class="w-full mb-6 rounded-2xl bg-white border border-gray-200 shadow-sm p-4">

    <div class="flex flex-col gap-4">

        <!-- Header principal -->
        <div class="flex items-center justify-between">

            <div class="flex items-center gap-2 md:gap-3">

                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-blue-50 text-blue-600">
                    <span class="material-symbols-outlined text-[20px]">
                        directions_bus
                    </span>
                </div>

                <h2 class="text-lg font-semibold text-gray-900 tracking-tight">
                    Gestion des Bus
                </h2>

            </div>

            <button
                id="btn-open-modal"
                onclick="openCreateModal()"
                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition-all shadow-sm"
            >
                <span class="material-symbols-outlined text-[18px]">
                    add
                </span>

                <span>Ajouter</span>
            </button>

        </div>

    </div>

</div>

<section class="mb-6 bg-white p-4 rounded-2xl border border-gray-200 shadow-sm">

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

        <div class="relative">

            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">
                search
            </span>

            <input
                id="search-input"
                type="text"
                placeholder="Plaque, marque, modèle..."
                class="w-full rounded-xl border-gray-300 border pl-10 pr-4 py-2 text-sm shadow-sm focus:border-blue-500 outline-none"
            >

        </div>

        <div class="flex gap-2 md:contents">
            <select
            id="status-filter"
            class="w-full rounded-xl border-gray-300 border px-4 py-2 text-sm shadow-sm focus:border-blue-500 outline-none bg-white"
        >
            <option value="">Tous les statuts</option>
            <option value="ACTIF">ACTIF / EN SERVICE</option>
            <option value="MAINTENANCE">MAINTENANCE</option>
            <option value="INACTIF">INACTIF</option>
        </select>

        <div></div>

        <button
            id="btn-clear-filters"
            onclick="clearFilters()"
            class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-200 transition-all"
        >
            Réinitialiser
        </button>
        </div>

    </div>

</section>

    <!-- ── Tableau de la flotte ── -->
    <section class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
            <h3 class="text-base font-semibold text-gray-900">Flotte de véhicules</h3>
            <span class="text-xs font-medium text-gray-500 bg-white px-2 py-1 rounded-md border border-gray-200">
                Total : <span id="bus-count"><?= count($buses ?? []) ?></span>
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left" id="bus-table">
                <thead class="text-gray-600 uppercase text-[11px] font-semibold bg-gray-50">
                    <tr>
                        <th class="px-6 py-4">Plaque d'immatriculation</th>
                        <th class="px-6 py-4">Marque & Modèle</th>
                        <th class="px-6 py-4">Capacité</th>
                        <th class="px-6 py-4">Couleur / Année</th>
                        <th class="px-6 py-4 text-center">Statut</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100" id="bus-tbody">
                    <?php if (empty($buses)) : ?>
                        <tr id="empty-row">
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-3 text-gray-400">
                                    <span class="material-symbols-outlined text-[20px] md:text-[48px]">inbox</span>
                                    <p class="text-[12px] md:text-sm font-medium">Aucun véhicule enregistré pour le moment</p>
                                </div>
                            </td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($buses as $bus) : ?>
                            <?php
                                $statut = strtoupper($bus['statut'] ?? 'ACTIF');
                                $badgeClass = 'bg-gray-100 text-gray-600 border-gray-200';
                                if ($statut === 'ACTIF') {
                                    $badgeClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                                } elseif ($statut === 'MAINTENANCE') {
                                    $badgeClass = 'bg-amber-50 text-amber-700 border-amber-200';
                                } elseif ($statut === 'INACTIF') {
                                    $badgeClass = 'bg-red-50 text-red-700 border-red-200';
                                }
                            ?>
                            <tr class="hover:bg-gray-50 transition-colors bus-row"
                                data-id="<?= esc($bus['id_bus']) ?>"
                                data-plaque="<?= strtolower(esc($bus['numero_plaque'])) ?>"
                                data-marque="<?= strtolower(esc($bus['marque'] ?? '')) ?>"
                                data-modele="<?= strtolower(esc($bus['modele'] ?? '')) ?>"
                                data-statut="<?= esc($statut) ?>"
                            >
                                <td class="px-6 py-4 font-mono font-bold text-gray-900">
                                    <?= esc($bus['numero_plaque']) ?>
                                </td>
                                <td class="px-6 py-4 text-gray-900 font-medium">
                                    <?= esc($bus['marque'] ?? '—') ?>
                                    <span class="text-gray-400 font-normal text-xs ml-1"><?= esc($bus['modele'] ?? '') ?></span>
                                </td>
                                <td class="px-6 py-4 text-gray-700 font-medium">
                                    <?= esc($bus['nombre_places'] ?? '0') ?>
                                    <span class="text-gray-400 text-xs font-normal ml-0.5">sièges</span>
                                </td>
                                <td class="px-6 py-4 text-gray-500 text-xs">
                                    <?= esc($bus['couleur'] ?? '—') ?> / <?= esc($bus['annee'] ?? '—') ?>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2.5 py-1 text-[11px] font-semibold rounded-full <?= $badgeClass ?>">
                                        <?= esc(ucfirst(strtolower($bus['statut'] ?? 'Actif'))) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <button
                                            title="Modifier"
                                            onclick="openEditModal(<?= esc(json_encode($bus)) ?>)"
                                            class="p-1.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                        >
                                            <span class="material-symbols-outlined text-[18px]">edit</span>
                                        </button>
                                        <button
                                            title="Supprimer"
                                            onclick="deleteBus(<?= (int)$bus['id_bus'] ?>, '<?= esc($bus['numero_plaque']) ?>')"
                                            class="p-1.5 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                        >
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
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
     MODAL — Bus Form (Création / Edition)
     ══════════════════════════════════════════════════ -->
<div id="busModal" class="fixed inset-0 z-50 hidden bg-gray-900/50 backdrop-blur-sm overflow-y-auto" role="dialog" aria-modal="true" aria-labelledby="modal-title">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 sm:p-0">
        <div class="relative bg-white rounded-2xl text-left overflow-hidden shadow-xl sm:my-8 sm:max-w-xl w-full">

            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[18px]" id="modal-icon">directions_bus</span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900" id="modal-title">Nouveau Bus</h3>
                </div>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-500 hover:bg-gray-100 p-1.5 rounded-full transition-colors">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <!-- Erreurs du formulaire -->
            <div id="form-errors" class="hidden mx-6 mt-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm font-medium"></div>

            <div class="px-6 py-6 space-y-5" id="modal-content">
                <input type="hidden" id="id_bus" name="id_bus">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Plaque -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            N° Plaque <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="numero_plaque"
                            type="text"
                            placeholder="ex: 1234AB01"
                            class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm shadow-sm focus:border-blue-500 outline-none uppercase"
                            required
                        >
                    </div>

                    <!-- Nombre de places -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Nombre de places <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="nombre_places"
                            type="number"
                            min="1"
                            placeholder="ex: 50"
                            class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm shadow-sm focus:border-blue-500 outline-none"
                            required
                        >
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Marque -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Marque</label>
                        <input
                            id="marque"
                            type="text"
                            placeholder="ex: Mercedes"
                            class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm shadow-sm focus:border-blue-500 outline-none"
                        >
                    </div>

                    <!-- Modèle -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Modèle</label>
                        <input
                            id="modele"
                            type="text"
                            placeholder="ex: Sprinter"
                            class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm shadow-sm focus:border-blue-500 outline-none"
                        >
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <!-- Couleur -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Couleur</label>
                        <input
                            id="couleur"
                            type="text"
                            placeholder="ex: Blanc"
                            class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm shadow-sm focus:border-blue-500 outline-none"
                        >
                    </div>

                    <!-- Année -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Année</label>
                        <input
                            id="annee"
                            type="number"
                            min="1900"
                            max="2030"
                            placeholder="ex: 2018"
                            class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm shadow-sm focus:border-blue-500 outline-none"
                        >
                    </div>

                    <!-- Statut -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Statut</label>
                        <select
                            id="statut"
                            class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm shadow-sm focus:border-blue-500 outline-none bg-white"
                        >
                            <option value="Actif">Actif</option>
                            <option value="Maintenance">Maintenance</option>
                            <option value="Inactif">Inactif</option>
                        </select>
                    </div>
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
                    id="btn-submit-bus"
                    onclick="submitBus()"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 shadow-sm transition-all"
                >
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    <span id="btn-submit-text">Enregistrer</span>
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

function showAlert(message, type = 'success') {
    const el = document.getElementById('page-alert');
    el.textContent = '';
    el.className = 'mb-4 p-4 rounded-xl text-sm font-medium border flex items-center gap-2 transition-all';
    
    let icon = 'check_circle';
    if (type === 'success') {
        el.classList.add('bg-green-50', 'border-green-200', 'text-green-700');
    } else {
        el.classList.add('bg-red-50', 'border-red-200', 'text-red-700');
        icon = 'error';
    }
    
    el.innerHTML = `<span class="material-symbols-outlined text-[18px]">${icon}</span> <span>${message}</span>`;
    el.classList.remove('hidden');
    
    setTimeout(() => {
        el.classList.add('hidden');
    }, 5000);
}

// ── Modal Actions ──────────────────────────────────────────────────────────
function openCreateModal() {
    clearForm();
    document.getElementById('modal-title').textContent = 'Nouveau Bus';
    document.getElementById('btn-submit-text').textContent = 'Enregistrer';
    document.getElementById('busModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function openEditModal(bus) {
    clearForm();
    document.getElementById('modal-title').textContent = 'Modifier le Bus';
    document.getElementById('btn-submit-text').textContent = 'Mettre à jour';
    
    document.getElementById('id_bus').value = bus.id_bus;
    document.getElementById('numero_plaque').value = bus.numero_plaque;
    document.getElementById('nombre_places').value = bus.nombre_places;
    document.getElementById('marque').value = bus.marque ?? '';
    document.getElementById('modele').value = bus.modele ?? '';
    document.getElementById('couleur').value = bus.couleur ?? '';
    document.getElementById('annee').value = bus.annee ?? '';
    document.getElementById('statut').value = bus.statut ?? 'Actif';
    
    document.getElementById('busModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    document.getElementById('busModal').classList.add('hidden');
    document.body.style.overflow = '';
    clearFormErrors();
}

function clearForm() {
    document.getElementById('id_bus').value = '';
    document.getElementById('numero_plaque').value = '';
    document.getElementById('nombre_places').value = '';
    document.getElementById('marque').value = '';
    document.getElementById('modele').value = '';
    document.getElementById('couleur').value = '';
    document.getElementById('annee').value = '';
    document.getElementById('statut').value = 'Actif';
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

// ── Submit logic ───────────────────────────────────────────────────────────
async function submitBus() {
    clearFormErrors();
    const btn = document.getElementById('btn-submit-bus');
    btn.disabled = true;
    btn.classList.add('opacity-60', 'cursor-wait');

    const id = document.getElementById('id_bus').value;
    const isEdit = !!id;

    const payload = {
        numero_plaque: document.getElementById('numero_plaque').value.trim().toUpperCase(),
        nombre_places: parseInt(document.getElementById('nombre_places').value) || null,
        marque:        document.getElementById('marque').value.trim() || null,
        modele:        document.getElementById('modele').value.trim() || null,
        couleur:       document.getElementById('couleur').value.trim() || null,
        annee:         parseInt(document.getElementById('annee').value) || null,
        statut:        document.getElementById('statut').value,
    };

    const errors = [];
    if (!payload.numero_plaque) errors.push('Le numéro de plaque est requis.');
    if (!payload.nombre_places || payload.nombre_places < 1) errors.push('Le nombre de places doit être supérieur à 0.');
    
    if (errors.length) {
        showFormErrors(errors);
        btn.disabled = false;
        btn.classList.remove('opacity-60', 'cursor-wait');
        return;
    }

    const url = isEdit ? `${BASE_URL}api/bus/${id}` : `${BASE_URL}api/bus`;
    const method = isEdit ? 'PUT' : 'POST';

    try {
        const response = await apiFetch(url, {
            method: method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload),
        });
        const json = await response.json();
        
        if (!response.ok || json.success === false) {
            const errs = json.errors ? Object.values(json.errors).flat() : [json.message || 'Une erreur est survenue.'];
            showFormErrors(errs);
            return;
        }

        closeModal();
        showAlert(isEdit ? 'Bus mis à jour avec succès.' : 'Bus créé avec succès.', 'success');
        
        const bus = json.data;
        const statut = (bus.statut || 'ACTIF').toUpperCase();
        let badgeClass = 'bg-gray-100 text-gray-600 border-gray-200';
        if (statut === 'ACTIF') {
            badgeClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
        } else if (statut === 'MAINTENANCE') {
            badgeClass = 'bg-amber-50 text-amber-700 border-amber-200';
        } else if (statut === 'INACTIF') {
            badgeClass = 'bg-red-50 text-red-700 border-red-200';
        }

        if (isEdit) {
            const row = document.querySelector(`.bus-row[data-id="${id}"]`);
            if (row) {
                row.dataset.plaque = bus.numero_plaque.toLowerCase();
                row.dataset.marque = (bus.marque || '').toLowerCase();
                row.dataset.modele = (bus.modele || '').toLowerCase();
                row.dataset.statut = statut;

                const cols = row.querySelectorAll('td');
                cols[0].textContent = bus.numero_plaque;
                cols[1].innerHTML = `${escapeHtml(bus.marque || '—')} <span class="text-gray-400 font-normal text-xs ml-1">${escapeHtml(bus.modele || '')}</span>`;
                cols[2].innerHTML = `${escapeHtml(bus.nombre_places || '0')} <span class="text-gray-400 text-xs font-normal ml-0.5">sièges</span>`;
                cols[3].textContent = `${escapeHtml(bus.couleur || '—')} / ${escapeHtml(bus.annee || '—')}`;
                cols[4].innerHTML = `
                    <span class="px-2.5 py-1 text-[11px] font-semibold rounded-full ${badgeClass}">
                        ${escapeHtml(bus.statut.charAt(0).toUpperCase() + bus.statut.slice(1).toLowerCase())}
                    </span>
                `;
                
                const editBtn = cols[5].querySelector('button[title="Modifier"]');
                if (editBtn) {
                    editBtn.setAttribute('onclick', `openEditModal(${JSON.stringify(bus).replace(/"/g, '&quot;')})`);
                }
            }
        } else {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50 transition-colors bus-row';
            tr.dataset.id = bus.id_bus;
            tr.dataset.plaque = bus.numero_plaque.toLowerCase();
            tr.dataset.marque = (bus.marque || '').toLowerCase();
            tr.dataset.modele = (bus.modele || '').toLowerCase();
            tr.dataset.statut = statut;

            tr.innerHTML = `
                <td class="px-6 py-4 font-mono font-bold text-gray-900">${escapeHtml(bus.numero_plaque)}</td>
                <td class="px-6 py-4 text-gray-900 font-medium">
                    ${escapeHtml(bus.marque || '—')}
                    <span class="text-gray-400 font-normal text-xs ml-1">${escapeHtml(bus.modele || '')}</span>
                </td>
                <td class="px-6 py-4 text-gray-700 font-medium">
                    ${escapeHtml(bus.nombre_places || '0')}
                    <span class="text-gray-400 text-xs font-normal ml-0.5">sièges</span>
                </td>
                <td class="px-6 py-4 text-gray-500 text-xs">${escapeHtml(bus.couleur || '—')} / ${escapeHtml(bus.annee || '—')}</td>
                <td class="px-6 py-4 text-center">
                    <span class="px-2.5 py-1 text-[11px] font-semibold rounded-full ${badgeClass}">
                        ${escapeHtml(bus.statut.charAt(0).toUpperCase() + bus.statut.slice(1).toLowerCase())}
                    </span>
                </td>
                <td class="px-6 py-4 text-right">
                    <div class="flex items-center justify-end gap-1">
                        <button
                            title="Modifier"
                            onclick="openEditModal(${JSON.stringify(bus).replace(/"/g, '&quot;')})"
                            class="p-1.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                        >
                            <span class="material-symbols-outlined text-[18px]">edit</span>
                        </button>
                        <button
                            title="Supprimer"
                            onclick="deleteBus(${parseInt(bus.id_bus)}, '${escapeHtml(bus.numero_plaque)}')"
                            class="p-1.5 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                        >
                            <span class="material-symbols-outlined text-[18px]">delete</span>
                        </button>
                    </div>
                </td>
            `;

            const tbody = document.getElementById('bus-tbody');
            const emptyRow = document.getElementById('empty-row');
            if (emptyRow) emptyRow.remove();

            tbody.prepend(tr);
        }
        filterTable();
    } catch (e) {
        showFormErrors(['Une erreur réseau est survenue.']);
    } finally {
        btn.disabled = false;
        btn.classList.remove('opacity-60', 'cursor-wait');
    }
}

function escapeHtml(str) {
    if (!str) return '';
    return str.toString()
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

// ── Delete logic ───────────────────────────────────────────────────────────
async function deleteBus(id, plaque) {
    if (!confirm(`Supprimer le bus ${plaque} ?`)) return;
    try {
        const response = await apiFetch(`${BASE_URL}api/bus/${id}`, { method: 'DELETE' });
        const json = await response.json();
        
        if (!response.ok || json.success === false) {
            showAlert(json.message || 'Impossible de supprimer ce bus.', 'error');
            return;
        }
        
        showAlert('Bus supprimé avec succès.', 'success');
        const row = document.querySelector(`.bus-row[data-id="${id}"]`);
        if (row) {
            row.remove();
        }
        
        const tbody = document.getElementById('bus-tbody');
        if (tbody && tbody.querySelectorAll('.bus-row').length === 0) {
            const emptyRow = document.createElement('tr');
            emptyRow.id = 'empty-row';
            emptyRow.innerHTML = `
                <td colspan="6" class="px-6 py-12 text-center">
                    <div class="flex flex-col items-center gap-3 text-gray-400">
                        <span class="material-symbols-outlined text-[20px] md:text-[48px]">inbox</span>
                        <p class="text-[12px] md:text-sm font-medium">Aucun véhicule enregistré pour le moment</p>
                    </div>
                </td>
            `;
            tbody.appendChild(emptyRow);
        }
        filterTable();
    } catch {
        showAlert('Erreur réseau.', 'error');
    }
}

// ── Search & Filter ────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('search-input')?.addEventListener('input', filterTable);
    document.getElementById('status-filter')?.addEventListener('change', filterTable);
});

function filterTable() {
    const search = document.getElementById('search-input').value.toLowerCase().trim();
    const status = document.getElementById('status-filter').value.toUpperCase();
    const rows = document.querySelectorAll('.bus-row');
    let visibleCount = 0;

    rows.forEach(row => {
        const matchSearch = !search || 
            row.dataset.plaque.includes(search) || 
            row.dataset.marque.includes(search) || 
            row.dataset.modele.includes(search);
            
        const matchStatus = !status || row.dataset.statut === status;

        if (matchSearch && matchStatus) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    document.getElementById('bus-count').textContent = visibleCount;
    
    const emptyRow = document.getElementById('empty-row');
    if (visibleCount === 0) {
        if (!emptyRow) {
            const tbody = document.getElementById('bus-tbody');
            const tr = document.createElement('tr');
            tr.id = 'empty-row-search';
            tr.innerHTML = `<td colspan="6" class="px-6 py-12 text-center text-gray-400">Aucun résultat trouvé pour votre recherche</td>`;
            tbody.appendChild(tr);
        }
    } else {
        document.getElementById('empty-row-search')?.remove();
    }
}

function clearFilters() {
    document.getElementById('search-input').value = '';
    document.getElementById('status-filter').value = '';
    filterTable();
}
</script>
<?= $this->endSection() ?>
