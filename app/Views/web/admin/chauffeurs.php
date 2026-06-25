<?= $this->extend($layout ?? 'web/layouts/admin') ?>

<?= $this->section('content') ?>

<main class="px-4 pb-12 max-w-[1600px] mx-auto">
    <!-- ── En-tête de page ── -->
    <div class="w-full h-[60px] mb-6 rounded-2xl flex items-center justify-between px-6 bg-white border border-gray-200 shadow-sm">
        <div class="flex items-center gap-3 h-full">
            <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-blue-50 text-blue-600">
                <span class="material-symbols-outlined text-[20px]">badge</span>
            </div>
            <h2 class="text-lg font-semibold text-gray-900 tracking-tight">Gestion des Chauffeurs</h2>
        </div>
        <div class="flex items-center gap-3">
            <button
                id="btn-open-modal"
                onclick="openCreateModal()"
                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition-all shadow-sm"
            >
                <span class="material-symbols-outlined text-[18px]">add</span>
                <span>Nouveau Chauffeur</span>
            </button>
        </div>
    </div>

    <!-- ── Zone d'Alerte ── -->
    <div id="page-alert" class="hidden mb-4 p-4 rounded-xl text-sm font-medium border flex items-center gap-2 transition-all"></div>

    <!-- ── Filtres ── -->
    <section class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4 bg-white p-4 rounded-2xl border border-gray-200 shadow-sm">
        <div class="relative">
            <span class="material-symbols-outlined absolute left-3 top-2.5 text-gray-400 text-[18px]">search</span>
            <input
                id="search-input"
                type="text"
                placeholder="Nom, prénom, téléphone, permis..."
                class="w-full rounded-xl border border-gray-300 pl-10 pr-4 py-2 text-sm shadow-sm focus:border-blue-500 outline-none"
            >
        </div>
        <div>
            <select
                id="status-filter"
                class="w-full rounded-xl border border-gray-300 px-4 py-2 text-sm shadow-sm focus:border-blue-500 outline-none bg-white"
            >
                <option value="">Tous les statuts</option>
                <option value="ACTIF">ACTIF</option>
                <option value="INACTIF">INACTIF</option>
            </select>
        </div>
        <button
            id="btn-clear-filters"
            onclick="clearFilters()"
            class="px-4 py-2 bg-gray-50 border border-gray-200 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-100 transition-all"
        >
            Réinitialiser les filtres
        </button>
    </section>

    <!-- ── Tableau des chauffeurs ── -->
    <section class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
            <h3 class="text-base font-semibold text-gray-900">Conducteurs enregistrés</h3>
            <span class="text-xs font-medium text-gray-500 bg-white px-2 py-1 rounded-md border border-gray-200">
                Total : <span id="chauffeurs-count"><?= count($chauffeurs ?? []) ?></span>
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left" id="chauffeurs-table">
                <thead class="text-gray-600 uppercase text-[11px] font-semibold bg-gray-50">
                    <tr>
                        <th class="px-6 py-4">Nom Complet</th>
                        <th class="px-6 py-4">Téléphone</th>
                        <th class="px-6 py-4">N° Permis</th>
                        <th class="px-6 py-4">Adresse</th>
                        <th class="px-6 py-4">Date d'embauche</th>
                        <th class="px-6 py-4 text-center">Statut</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100" id="chauffeurs-tbody">
                    <?php if (empty($chauffeurs)) : ?>
                        <tr id="empty-row">
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-3 text-gray-400">
                                    <span class="material-symbols-outlined text-[48px]">inbox</span>
                                    <p class="text-sm font-medium">Aucun chauffeur enregistré pour le moment</p>
                                </div>
                            </td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($chauffeurs as $ch) : ?>
                            <?php
                                $nomComplet = trim(($ch['prenom'] ?? '') . ' ' . ($ch['nom'] ?? '') . ' ' . ($ch['postnom'] ?? ''));
                                $statut = strtoupper($ch['statut'] ?? 'ACTIF');
                                $badgeClass = 'bg-gray-100 text-gray-600 border-gray-200';
                                if ($statut === 'ACTIF') {
                                    $badgeClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                                } elseif ($statut === 'INACTIF') {
                                    $badgeClass = 'bg-red-50 text-red-700 border-red-200';
                                }
                            ?>
                            <tr class="hover:bg-gray-50 transition-colors chauffeur-row"
                                data-id="<?= esc($ch['id_conducteur']) ?>"
                                data-nom="<?= strtolower(esc($nomComplet)) ?>"
                                data-tel="<?= strtolower(esc($ch['telephone'] ?? '')) ?>"
                                data-permis="<?= strtolower(esc($ch['numero_permis'] ?? '')) ?>"
                                data-statut="<?= esc($statut) ?>"
                            >
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs shrink-0">
                                            <?= strtoupper(substr($ch['nom'] ?? '?', 0, 1)) ?>
                                        </div>
                                        <div class="text-sm font-semibold text-gray-900">
                                            <?= esc($nomComplet) ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-700 font-medium">
                                    <?= esc($ch['telephone'] ?? '—') ?>
                                </td>
                                <td class="px-6 py-4 font-mono text-xs text-gray-900 font-medium">
                                    <?= esc($ch['numero_permis'] ?? '—') ?>
                                </td>
                                <td class="px-6 py-4 text-gray-500 text-xs max-w-[200px] truncate" title="<?= esc($ch['adresse'] ?? '') ?>">
                                    <?= esc($ch['adresse'] ?? '—') ?>
                                </td>
                                <td class="px-6 py-4 text-gray-500 text-xs">
                                    <?= esc($ch['date_embauche'] ?? '—') ?>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2.5 py-1 text-[11px] font-semibold rounded-full border <?= $badgeClass ?>">
                                        <?= esc($ch['statut'] ?? 'Actif') ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <button
                                            title="Modifier"
                                            onclick="openEditModal(<?= esc(json_encode($ch)) ?>)"
                                            class="p-1.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                        >
                                            <span class="material-symbols-outlined text-[18px]">edit</span>
                                        </button>
                                        <button
                                            title="Supprimer"
                                            onclick="deleteChauffeur(<?= (int)$ch['id_conducteur'] ?>, '<?= esc($nomComplet) ?>')"
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
     MODAL — Chauffeur Form (Création / Edition)
     ══════════════════════════════════════════════════ -->
<div id="chauffeurModal" class="fixed inset-0 z-50 hidden bg-gray-900/50 backdrop-blur-sm overflow-y-auto" role="dialog" aria-modal="true" aria-labelledby="modal-title">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 sm:p-0">
        <div class="relative bg-white rounded-2xl text-left overflow-hidden shadow-xl sm:my-8 sm:max-w-2xl w-full">

            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[18px]" id="modal-icon">badge</span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900" id="modal-title">Nouveau Chauffeur</h3>
                </div>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-500 hover:bg-gray-100 p-1.5 rounded-full transition-colors">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <!-- Erreurs du formulaire -->
            <div id="form-errors" class="hidden mx-6 mt-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm font-medium"></div>

            <div class="px-6 py-6 space-y-5" id="modal-content">
                <input type="hidden" id="id_conducteur" name="id_conducteur">

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <!-- Prénom -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Prénom</label>
                        <input
                            id="prenom"
                            type="text"
                            placeholder="ex: Jean"
                            class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm shadow-sm focus:border-blue-500 outline-none"
                        >
                    </div>

                    <!-- Nom -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Nom <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="nom"
                            type="text"
                            placeholder="ex: Kabamba"
                            class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm shadow-sm focus:border-blue-500 outline-none"
                            required
                        >
                    </div>

                    <!-- Postnom -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Postnom</label>
                        <input
                            id="postnom"
                            type="text"
                            placeholder="ex: Mukendi"
                            class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm shadow-sm focus:border-blue-500 outline-none"
                        >
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Téléphone -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Téléphone</label>
                        <input
                            id="telephone"
                            type="tel"
                            placeholder="ex: +243810000000"
                            class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm shadow-sm focus:border-blue-500 outline-none"
                        >
                    </div>

                    <!-- Numéro de Permis -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">N° de Permis de conduire</label>
                        <input
                            id="numero_permis"
                            type="text"
                            placeholder="ex: PE-12345-6789"
                            class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm shadow-sm focus:border-blue-500 outline-none uppercase"
                        >
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Date d'embauche -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Date d'embauche</label>
                        <input
                            id="date_embauche"
                            type="date"
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
                            <option value="Inactif">Inactif</option>
                        </select>
                    </div>
                </div>

                <!-- Adresse -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Adresse physique</label>
                    <textarea
                        id="adresse"
                        rows="2"
                        placeholder="ex: 12, Av. de l'Equateur, Kinshasa/Gombe"
                        class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm shadow-sm focus:border-blue-500 outline-none resize-none"
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
                    id="btn-submit-chauffeur"
                    onclick="submitChauffeur()"
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
    document.getElementById('modal-title').textContent = 'Nouveau Chauffeur';
    document.getElementById('btn-submit-text').textContent = 'Enregistrer';
    document.getElementById('chauffeurModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function openEditModal(ch) {
    clearForm();
    document.getElementById('modal-title').textContent = 'Modifier le Chauffeur';
    document.getElementById('btn-submit-text').textContent = 'Mettre à jour';
    
    document.getElementById('id_conducteur').value = ch.id_conducteur;
    document.getElementById('nom').value = ch.nom;
    document.getElementById('postnom').value = ch.postnom ?? '';
    document.getElementById('prenom').value = ch.prenom ?? '';
    document.getElementById('telephone').value = ch.telephone ?? '';
    document.getElementById('numero_permis').value = ch.numero_permis ?? '';
    document.getElementById('date_embauche').value = ch.date_embauche ?? '';
    document.getElementById('statut').value = ch.statut ?? 'Actif';
    document.getElementById('adresse').value = ch.adresse ?? '';
    
    document.getElementById('chauffeurModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    document.getElementById('chauffeurModal').classList.add('hidden');
    document.body.style.overflow = '';
    clearFormErrors();
}

function clearForm() {
    document.getElementById('id_conducteur').value = '';
    document.getElementById('nom').value = '';
    document.getElementById('postnom').value = '';
    document.getElementById('prenom').value = '';
    document.getElementById('telephone').value = '';
    document.getElementById('numero_permis').value = '';
    document.getElementById('date_embauche').value = '';
    document.getElementById('statut').value = 'Actif';
    document.getElementById('adresse').value = '';
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
async function submitChauffeur() {
    clearFormErrors();
    const btn = document.getElementById('btn-submit-chauffeur');
    btn.disabled = true;
    btn.classList.add('opacity-60', 'cursor-wait');

    const id = document.getElementById('id_conducteur').value;
    const isEdit = !!id;

    const payload = {
        nom:           document.getElementById('nom').value.trim(),
        postnom:       document.getElementById('postnom').value.trim() || null,
        prenom:        document.getElementById('prenom').value.trim() || null,
        telephone:     document.getElementById('telephone').value.trim() || null,
        numero_permis: document.getElementById('numero_permis').value.trim().toUpperCase() || null,
        date_embauche: document.getElementById('date_embauche').value || null,
        statut:        document.getElementById('statut').value,
        adresse:       document.getElementById('adresse').value.trim() || null,
    };

    const errors = [];
    if (!payload.nom) errors.push('Le nom du chauffeur est requis.');
    
    if (errors.length) {
        showFormErrors(errors);
        btn.disabled = false;
        btn.classList.remove('opacity-60', 'cursor-wait');
        return;
    }

    const url = isEdit ? `${BASE_URL}api/conducteurs/${id}` : `${BASE_URL}api/conducteurs`;
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
        showAlert(isEdit ? 'Chauffeur mis à jour avec succès.' : 'Chauffeur créé avec succès.', 'success');
        setTimeout(() => window.location.reload(), 1000);
    } catch (e) {
        showFormErrors(['Une erreur réseau est survenue.']);
    } finally {
        btn.disabled = false;
        btn.classList.remove('opacity-60', 'cursor-wait');
    }
}

// ── Delete logic ───────────────────────────────────────────────────────────
async function deleteChauffeur(id, nomComplet) {
    if (!confirm(`Supprimer le chauffeur ${nomComplet} ?`)) return;
    try {
        const response = await apiFetch(`${BASE_URL}api/conducteurs/${id}`, { method: 'DELETE' });
        const json = await response.json();
        
        if (!response.ok || json.success === false) {
            showAlert(json.message || 'Impossible de supprimer ce chauffeur.', 'error');
            return;
        }
        
        showAlert('Chauffeur supprimé avec succès.', 'success');
        setTimeout(() => window.location.reload(), 1000);
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
    const rows = document.querySelectorAll('.chauffeur-row');
    let visibleCount = 0;

    rows.forEach(row => {
        const matchSearch = !search || 
            row.dataset.nom.includes(search) || 
            row.dataset.tel.includes(search) || 
            row.dataset.permis.includes(search);
            
        const matchStatus = !status || row.dataset.statut === status;

        if (matchSearch && matchStatus) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    document.getElementById('chauffeurs-count').textContent = visibleCount;
    
    const emptyRow = document.getElementById('empty-row');
    if (visibleCount === 0) {
        if (!emptyRow) {
            const tbody = document.getElementById('chauffeurs-tbody');
            const tr = document.createElement('tr');
            tr.id = 'empty-row-search';
            tr.innerHTML = `<td colspan="7" class="px-6 py-12 text-center text-gray-400">Aucun résultat trouvé pour votre recherche</td>`;
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
