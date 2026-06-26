<?= $this->extend($layout ?? 'web/layouts/super_admin') ?>

<?= $this->section('content') ?>

<main class="px-4 pb-12 max-w-[1600px] mx-auto">
    <!-- ── En-tête de page ── -->
    <div class="w-full h-[60px] mb-6 rounded-2xl flex items-center justify-between px-6 bg-white border border-gray-200 shadow-sm">
        <div class="flex items-center gap-3 h-full">
            <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-blue-50 text-blue-600">
                <span class="material-symbols-outlined text-[20px]">manage_accounts</span>
            </div>
            <h2 class="text-lg font-semibold text-gray-900 tracking-tight">Gestion des Agents</h2>
        </div>
        <div class="flex items-center gap-3">
            <button
                id="btn-open-modal"
                onclick="openCreateModal()"
                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition-all shadow-sm"
            >
                <span class="material-symbols-outlined text-[18px]">add</span>
                <span>Ajouter un utilisateur</span>
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
                placeholder="Nom, nom d'utilisateur, email..."
                class="w-full rounded-xl border border-gray-300 pl-10 pr-4 py-2 text-sm shadow-sm focus:border-blue-500 outline-none"
            >
        </div>
        <div>
            <select
                id="role-filter"
                class="w-full rounded-xl border border-gray-300 px-4 py-2 text-sm shadow-sm focus:border-blue-500 outline-none bg-white"
            >
                <option value="">Tous les rôles</option>
                <?php foreach ($roles as $role): ?>
                    <option value="<?= esc(strtoupper($role['libelle'])) ?>"><?= esc($role['libelle']) ?></option>
                <?php endforeach; ?>
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

    <!-- ── Tableau des agents ── -->
    <section class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
            <h3 class="text-base font-semibold text-gray-900">Agents de l'agence</h3>
            <span class="text-xs font-medium text-gray-500 bg-white px-2 py-1 rounded-md border border-gray-200">
                Total : <span id="agents-count"><?= count($utilisateurs ?? []) ?></span>
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left shadow-sm" id="agents-table">
                <thead class="text-gray-600 uppercase text-[11px] font-semibold bg-gray-50">
                    <tr>
                        <th class="px-6 py-4">Nom de l'utilisateur</th>
                        <th class="px-6 py-4">Nom d'utilisateur</th>
                        <th class="px-6 py-4">Rôle</th>
                        <th class="px-6 py-4">Téléphone</th>
                        <th class="px-6 py-4 text-center">Statut</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100" id="agents-tbody">
                    <?php if (empty($utilisateurs)) : ?>
                        <tr id="empty-row">
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-3 text-gray-400">
                                    <span class="material-symbols-outlined text-[48px]">inbox</span>
                                    <p class="text-sm font-medium">Aucun agent enregistré pour le moment</p>
                                </div>
                            </td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($utilisateurs as $u) : ?>
                            <?php
                                $nomComplet = trim(($u['prenom'] ?? '') . ' ' . ($u['nom'] ?? '') . ' ' . ($u['postnom'] ?? ''));
                                $statut = strtoupper($u['statut'] ?? 'ACTIF');
                                $badgeClass = 'bg-gray-100 text-gray-600 border-gray-200';
                                if ($statut === 'ACTIF') {
                                    $badgeClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                                } else {
                                    $badgeClass = 'bg-red-50 text-red-700 border-red-200';
                                }

                                $roleLabel = $u['role_libelle'] ?? 'Utilisateur';
                                $roleClass = 'bg-blue-50 text-blue-700 border-blue-100';
                                if (str_contains(strtolower($roleLabel), 'super')) {
                                    $roleClass = 'bg-purple-50 text-purple-700 border-purple-100';
                                } elseif (str_contains(strtolower($roleLabel), 'recept')) {
                                    $roleClass = 'bg-amber-50 text-amber-700 border-amber-100';
                                }
                            ?>
                            <tr class="hover:bg-gray-50 transition-colors agent-row"
                                data-id="<?= esc($u['id_utilisateur']) ?>"
                                data-nom="<?= strtolower(esc($nomComplet)) ?>"
                                data-username="<?= strtolower(esc($u['username'])) ?>"
                                data-email="<?= strtolower(esc($u['email'] ?? '')) ?>"
                                data-role="<?= esc(strtoupper($roleLabel)) ?>"
                                data-statut="<?= esc($statut) ?>"
                            >
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs shrink-0">
                                            <?= strtoupper(substr($u['nom'] ?? '?', 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-gray-900"><?= esc($nomComplet) ?></div>
                                            <div class="text-xs text-gray-500"><?= esc($u['email'] ?? '—') ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-mono text-xs text-gray-700 font-semibold">
                                    <?= esc($u['username']) ?>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-0.5 text-[11px] font-semibold rounded-full border <?= $roleClass ?>">
                                        <?= esc($roleLabel) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-700 text-xs">
                                    <?= esc($u['telephone'] ?? '—') ?>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2.5 py-1 text-[11px] font-semibold rounded-full <?= $badgeClass ?>">
                                        <?= esc($u['statut'] ?? 'Actif') ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <button
                                            title="Modifier"
                                            onclick="openEditModal(<?= esc(json_encode($u)) ?>)"
                                            class="p-1.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                        >
                                            <span class="material-symbols-outlined text-[18px]">edit</span>
                                        </button>
                                        <button
                                            title="Supprimer"
                                            onclick="deleteAgent(<?= (int)$u['id_utilisateur'] ?>, '<?= esc($nomComplet) ?>')"
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
     MODAL — User Form (Création / Edition)
     ══════════════════════════════════════════════════ -->
<div id="agentModal" class="fixed inset-0 z-50 hidden bg-gray-900/50 backdrop-blur-sm overflow-y-auto" role="dialog" aria-modal="true" aria-labelledby="modal-title">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 sm:p-0">
        <div class="relative bg-white rounded-2xl text-left overflow-hidden shadow-xl sm:my-8 sm:max-w-2xl w-full">

            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[18px]" id="modal-icon">manage_accounts</span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900" id="modal-title">Nouvel Agent</h3>
                </div>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-500 hover:bg-gray-100 p-1.5 rounded-full transition-colors">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <!-- Erreurs du formulaire -->
            <div id="form-errors" class="hidden mx-6 mt-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm font-medium"></div>

            <div class="px-6 py-6 space-y-5" id="modal-content">
                <input type="hidden" id="id_utilisateur" name="id_utilisateur">

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
                    <!-- Username -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Nom d'utilisateur <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="username"
                            type="text"
                            placeholder="ex: j.kabamba"
                            class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm shadow-sm focus:border-blue-500 outline-none"
                            required
                        >
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                        <input
                            id="email"
                            type="email"
                            placeholder="ex: agent@kashala.cd"
                            class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm shadow-sm focus:border-blue-500 outline-none"
                        >
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
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

                    <!-- Rôle -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Rôle affecté <span class="text-red-500">*</span>
                        </label>
                        <select id="id_role" name="id_role" class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm shadow-sm focus:border-blue-500 outline-none bg-white" required>
                            <option value="">Sélectionner le rôle</option>
                            <?php foreach ($roles as $role) : ?>
                                <option value="<?= esc($role['id_role']) ?>"><?= esc($role['libelle']) ?></option>
                            <?php endforeach; ?>
                        </select>
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

                <!-- Mot de passe -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5" id="password-label">
                        Mot de passe <span class="text-red-500" id="password-required-star">*</span>
                    </label>
                    <input
                        id="mot_de_passe"
                        type="password"
                        placeholder="Laisser vide pour ne pas modifier (en édition)"
                        class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm shadow-sm focus:border-blue-500 outline-none"
                    >
                    <p class="text-[10px] text-gray-400 mt-1" id="password-help-text">Le mot de passe sera hashé de manière sécurisée en base de données.</p>
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
                    id="btn-submit-agent"
                    onclick="submitAgent()"
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
    document.getElementById('modal-title').textContent = 'Nouveau Compte Agent';
    document.getElementById('btn-submit-text').textContent = 'Enregistrer';
    document.getElementById('password-required-star').classList.remove('hidden');
    document.getElementById('mot_de_passe').placeholder = 'Saisir le mot de passe initial';
    
    document.getElementById('agentModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function openEditModal(u) {
    clearForm();
    document.getElementById('modal-title').textContent = 'Modifier le Compte Agent';
    document.getElementById('btn-submit-text').textContent = 'Mettre à jour';
    document.getElementById('password-required-star').classList.add('hidden');
    document.getElementById('mot_de_passe').placeholder = 'Laisser vide pour ne pas modifier';
    
    document.getElementById('id_utilisateur').value = u.id_utilisateur;
    document.getElementById('nom').value = u.nom;
    document.getElementById('postnom').value = u.postnom ?? '';
    document.getElementById('prenom').value = u.prenom ?? '';
    document.getElementById('username').value = u.username;
    document.getElementById('email').value = u.email ?? '';
    document.getElementById('telephone').value = u.telephone ?? '';
    document.getElementById('id_role').value = u.id_role;
    document.getElementById('statut').value = u.statut ?? 'Actif';
    
    document.getElementById('agentModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    document.getElementById('agentModal').classList.add('hidden');
    document.body.style.overflow = '';
    clearFormErrors();
}

function clearForm() {
    document.getElementById('id_utilisateur').value = '';
    document.getElementById('nom').value = '';
    document.getElementById('postnom').value = '';
    document.getElementById('prenom').value = '';
    document.getElementById('username').value = '';
    document.getElementById('email').value = '';
    document.getElementById('telephone').value = '';
    document.getElementById('id_role').value = '';
    document.getElementById('statut').value = 'Actif';
    document.getElementById('mot_de_passe').value = '';
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
async function submitAgent() {
    clearFormErrors();
    const btn = document.getElementById('btn-submit-agent');
    btn.disabled = true;
    btn.classList.add('opacity-60', 'cursor-wait');

    const id = document.getElementById('id_utilisateur').value;
    const isEdit = !!id;

    const payload = {
        nom:           document.getElementById('nom').value.trim(),
        postnom:       document.getElementById('postnom').value.trim() || null,
        prenom:        document.getElementById('prenom').value.trim() || null,
        username:      document.getElementById('username').value.trim(),
        email:         document.getElementById('email').value.trim() || null,
        telephone:     document.getElementById('telephone').value.trim() || null,
        id_role:       parseInt(document.getElementById('id_role').value) || null,
        statut:        document.getElementById('statut').value,
    };

    const password = document.getElementById('mot_de_passe').value;
    if (password !== '') {
        payload.mot_de_passe = password;
    }

    const errors = [];
    if (!payload.nom) errors.push('Le nom de famille est requis.');
    if (!payload.username) errors.push("Le nom d'utilisateur est requis.");
    if (!payload.id_role) errors.push("L'affectation d'un rôle est requise.");
    if (!isEdit && !password) errors.push('Le mot de passe initial est requis pour un nouveau compte.');

    if (errors.length) {
        showFormErrors(errors);
        btn.disabled = false;
        btn.classList.remove('opacity-60', 'cursor-wait');
        return;
    }

    const url = isEdit ? `${BASE_URL}api/utilisateurs/${id}` : `${BASE_URL}api/utilisateurs`;
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
        showAlert(isEdit ? 'Compte agent mis à jour avec succès.' : 'Compte agent créé avec succès.', 'success');
        setTimeout(() => window.location.reload(), 1000);
    } catch (e) {
        showFormErrors(['Une erreur réseau est survenue.']);
    } finally {
        btn.disabled = false;
        btn.classList.remove('opacity-60', 'cursor-wait');
    }
}

// ── Delete logic ───────────────────────────────────────────────────────────
async function deleteAgent(id, nomComplet) {
    if (!confirm(`Désactiver et supprimer le compte de ${nomComplet} ?`)) return;
    try {
        const response = await apiFetch(`${BASE_URL}api/utilisateurs/${id}`, { method: 'DELETE' });
        const json = await response.json();
        
        if (!response.ok || json.success === false) {
            showAlert(json.message || 'Impossible de supprimer cet agent.', 'error');
            return;
        }
        
        showAlert('Compte agent supprimé avec succès.', 'success');
        setTimeout(() => window.location.reload(), 1000);
    } catch {
        showAlert('Erreur réseau.', 'error');
    }
}

// ── Search & Filter ────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('search-input')?.addEventListener('input', filterTable);
    document.getElementById('role-filter')?.addEventListener('change', filterTable);
});

function filterTable() {
    const search = document.getElementById('search-input').value.toLowerCase().trim();
    const role = document.getElementById('role-filter').value.toUpperCase();
    const rows = document.querySelectorAll('.agent-row');
    let visibleCount = 0;

    rows.forEach(row => {
        const matchSearch = !search || 
            row.dataset.nom.includes(search) || 
            row.dataset.username.includes(search) ||
            row.dataset.email.includes(search);
            
        const matchRole = !role || row.dataset.role === role;

        if (matchSearch && matchRole) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    document.getElementById('agents-count').textContent = visibleCount;
    
    const emptyRow = document.getElementById('empty-row');
    if (visibleCount === 0) {
        if (!emptyRow) {
            const tbody = document.getElementById('agents-tbody');
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
    document.getElementById('role-filter').value = '';
    filterTable();
}
</script>
<?= $this->endSection() ?>