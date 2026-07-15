<?= $this->extend($layout ?? 'web/layouts/super_admin') ?>

<?= $this->section('styles') ?>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/locales-all.global.min.js"></script>
<style>
    /* FullCalendar - Design Ultra-Moderne & Minimaliste */
    .fc {
        --fc-border-color: #e5e7eb;
        --fc-button-bg-color: #ffffff;
        --fc-button-border-color: #d1d5db;
        --fc-button-text-color: #374151;
        --fc-button-hover-bg-color: #f9fafb;
        --fc-button-hover-border-color: #d1d5db;
        --fc-button-active-bg-color: #f3f4f6;
        --fc-button-active-border-color: #d1d5db;
        --fc-today-bg-color: #eff6ff;
        font-family: inherit;
        font-size: 0.875rem;
    }

    .fc-theme-standard th {
        padding: 12px 0;
        background-color: #f8fafc;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        color: #64748b;
        border-bottom: 1px solid #e5e7eb;
    }

    .fc .fc-toolbar-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #0f172a;
    }

    .fc .fc-button {
        border-radius: 0.5rem;
        box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        text-transform: capitalize;
        font-weight: 500;
        padding: 0.4rem 1rem;
        transition: all 0.2s;
    }

    .fc-event {
        border-radius: 4px;
        padding: 2px 6px;
        font-size: 0.75rem;
        font-weight: 500;
        border: none;
        box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        cursor: pointer;
    }

    .fc .fc-past-disabled {
        background: #f8fafc;
        cursor: not-allowed;
    }

    .fc .fc-past-disabled .fc-daygrid-day-number {
        opacity: 0.4;
        color: #94a3b8;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>



<main class="px-4 pb-12 max-w-[1600px] mx-auto">
    <div class="w-full mb-6 rounded-2xl bg-white border border-gray-200 shadow-sm p-4">
    <div class="flex items-center justify-between">

        <div class="flex items-center gap-3">
            <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-blue-50 text-blue-600">
                <span class="material-symbols-outlined text-[20px]">
                    calendar_month
                </span>
            </div>

            <h2 class="text-lg font-semibold text-gray-900 tracking-tight">
                Planification
            </h2>
        </div>

        <div class="hidden sm:flex items-center gap-2 text-sm text-gray-500 bg-gray-50 px-4 py-1.5 rounded-full border border-gray-200 shadow-sm">
            <span class="material-symbols-outlined text-[16px]">
                touch_app
            </span>
            Cliquer sur une date pour planifier
        </div>

    </div>
</div>

    <div id="planning-alert" class="hidden mb-6 border rounded-xl p-4 text-sm font-medium transition-all"></div>

    <section class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <h3 class="text-base font-semibold text-gray-900">Calendrier des programmes</h3>
            
            <div class="flex flex-wrap items-center gap-4">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-600 shadow-sm"></span>
                    <span class="text-xs font-medium text-gray-600">Planifié</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-green-500 shadow-sm"></span>
                    <span class="text-xs font-medium text-gray-600">Ouvert</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-yellow-500 shadow-sm"></span>
                    <span class="text-xs font-medium text-gray-600">Suspendu</span>
                </div>
            </div>
        </div>
        
        <div class="p-6">
            <div id="planning-calendar"></div>
        </div>
    </section>
</main>

<div id="planning-modal" class="fixed inset-0 z-[100] hidden">
    <div id="planning-modal-backdrop" class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity"></div>
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <section class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl border border-gray-200">
                
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-white">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-50 text-blue-600">
                            <span class="material-symbols-outlined text-[18px]">event</span>
                        </div>
                        <h3 id="planning-modal-title" class="text-lg font-semibold text-gray-900">Nouvelle planification</h3>
                    </div>
                    <button id="planning-modal-close" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-1.5 rounded-lg transition-colors" type="button">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                </div>

                <form id="planning-form" class="px-6 py-6 space-y-5">
                    <div id="planning-form-errors" class="hidden bg-red-50 border border-red-200 text-red-700 rounded-xl p-4 text-sm font-medium"></div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Date début</label>
                            <input id="date_debut" class="w-full rounded-xl border-gray-300 border px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all shadow-sm text-gray-700" type="date" name="date_debut" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Date fin</label>
                            <input id="date_fin" class="w-full rounded-xl border-gray-300 border px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all shadow-sm text-gray-700" type="date" name="date_fin" required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Trajet</label>
                        <select class="w-full rounded-xl border-gray-300 border px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all shadow-sm bg-white text-gray-700" name="id_trajet" required>
                            <option value="">Sélectionner un trajet</option>
                            <?php foreach ($trajets as $trajet): ?>
                                <?php
                                $depart = $lieux_map[$trajet['id_lieu_depart']] ?? 'Départ inconnu';
                                $arrivee = $lieux_map[$trajet['id_lieu_arrivee']] ?? 'Arrivée inconnue';
                                $horaire = $horaires_map[$trajet['id_horaire']] ?? 'Horaire non défini';
                                ?>
                                <option value="<?= esc($trajet['id_trajet']) ?>">
                                    <?= esc($depart . ' ➔ ' . $arrivee . ' | ' . $horaire) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Bus assigné</label>
                            <select class="w-full rounded-xl border-gray-300 border px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all shadow-sm bg-white text-gray-700" name="id_bus" required>
                                <option value="">Sélectionner un bus</option>
                                <?php foreach ($bus as $item): ?>
                                    <option value="<?= esc($item['id_bus']) ?>" data-places="<?= esc($item['nombre_places'] ?? '') ?>">
                                        <?= esc($item['numero_plaque'] . ' - ' . ($item['nombre_places'] ?? 0) . ' places') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Conducteur</label>
                            <select class="w-full rounded-xl border-gray-300 border px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all shadow-sm bg-white text-gray-700" name="id_conducteur" required>
                                <option value="">Sélectionner un conducteur</option>
                                <?php foreach ($conducteurs as $conducteur): ?>
                                    <?php $nom = trim(($conducteur['nom'] ?? '') . ' ' . ($conducteur['postnom'] ?? '') . ' ' . ($conducteur['prenom'] ?? '')); ?>
                                    <option value="<?= esc($conducteur['id_conducteur']) ?>"><?= esc($nom) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Places disponibles</label>
                            <input class="w-full rounded-xl border-gray-300 border px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all shadow-sm text-gray-700" type="number" name="places_disponibles" min="1" placeholder="Par défaut : places du bus">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Statut</label>
                            <select class="w-full rounded-xl border-gray-300 border px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all shadow-sm bg-white text-gray-700" name="statut">
                                <option value="Planifie">Planifié</option>
                                <option value="Ouvert">Ouvert</option>
                                <option value="Suspendu">Suspendu</option>
                                <option value="Annule">Annulé</option>
                                <option value="Termine">Terminé</option>
                            </select>
                        </div>
                    </div>

                    <fieldset id="planning-days-fieldset" class="pt-2">
                        <legend class="block text-sm font-medium text-gray-700 mb-3">Jours de récurrence</legend>
                        <div class="grid grid-cols-4 sm:grid-cols-7 gap-2">
                            <?php $days = [1 => 'Lun', 2 => 'Mar', 3 => 'Mer', 4 => 'Jeu', 5 => 'Ven', 6 => 'Sam', 7 => 'Dim']; ?>
                            <?php foreach ($days as $value => $label): ?>
                                <label class="relative flex items-center justify-center cursor-pointer">
                                    <input class="peer sr-only" type="checkbox" name="jours_semaine[]" value="<?= esc($value) ?>" checked>
                                    <div class="w-full py-2 text-center text-sm font-medium rounded-xl border border-gray-200 text-gray-600 bg-white transition-all peer-checked:bg-blue-50 peer-checked:border-blue-600 peer-checked:text-blue-700 hover:bg-gray-50 shadow-sm">
                                        <?= esc($label) ?>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </fieldset>

                    <div class="mt-8 pt-5 border-t border-gray-100 flex items-center justify-end gap-3">
                        <button id="planning-delete" class="hidden px-5 py-2.5 text-sm font-medium text-red-600 bg-white border border-red-200 rounded-xl hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all shadow-sm" type="button">
                            Supprimer
                        </button>
                        <button id="planning-cancel" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all shadow-sm" type="button">
                            Annuler
                        </button>
                        <button id="planning-submit" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-xl hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all shadow-sm" type="submit">
                            <span class="material-symbols-outlined text-[18px]">event_available</span>
                            <span>Planifier</span>
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        let token = <?= json_encode((string) ($api_token ?? ''), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
        const calendarEl = document.getElementById('planning-calendar');
        const modal = document.getElementById('planning-modal');
        const modalTitle = document.getElementById('planning-modal-title');
        const form = document.getElementById('planning-form');
        const formErrors = document.getElementById('planning-form-errors');
        const pageAlert = document.getElementById('planning-alert');
        const submitButton = document.getElementById('planning-submit');
        const deleteButton = document.getElementById('planning-delete');
        const daysFieldset = document.getElementById('planning-days-fieldset');

        const busSelect = document.querySelector('select[name="id_bus"]');
        const placesInput = document.querySelector('input[name="places_disponibles"]');

        busSelect.addEventListener('change', (e) => {
            const selectedOption = e.target.options[e.target.selectedIndex];
            const places = selectedOption.getAttribute('data-places');
            
            // Si on a un nombre de places et qu'on est en mode "création" (ou si le champ est vide)
            if (places && !editingProgrammeId) {
                placesInput.value = places;
            }
        });
        
        const localDateIso = (date) => {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        };
        const todayIso = localDateIso(new Date());
        let editingProgrammeId = null;

        // Mise à jour du style de l'alerte
        const showPageAlert = (message, type = 'success') => {
            pageAlert.className = 'mb-6 border rounded-xl p-4 text-sm font-medium transition-all ' + (
                type === 'success'
                    ? 'bg-emerald-50 border-emerald-200 text-emerald-800'
                    : 'bg-red-50 border-red-200 text-red-800'
            );
            pageAlert.textContent = message;
            pageAlert.classList.remove('hidden');
        };

        const refreshSession = async () => {
            const response = await fetch('<?= base_url('session/refresh') ?>', {
                method: 'POST',
                headers: { Accept: 'application/json' }
            });
            const json = await response.json();
            if (!response.ok || json.success === false || !json.data?.access_token) {
                throw new Error(json.message || 'Session expirée. Veuillez vous reconnecter.');
            }
            token = json.data.access_token;
        };

        const apiFetch = async (url, options = {}, retry = true) => {
            const headers = {
                Accept: 'application/json',
                ...(options.headers || {}),
                Authorization: `Bearer ${token}`
            };
            let response = await fetch(url, { ...options, headers });
            if (response.status === 401 && retry) {
                await refreshSession();
                response = await apiFetch(url, options, false);
            }
            return response;
        };

        const clearFormErrors = () => {
            formErrors.innerHTML = '';
            formErrors.classList.add('hidden');
        };

        const showFormErrors = (messages) => {
            const list = document.createElement('ul');
            list.className = 'list-disc pl-5 space-y-1';
            messages.forEach((message) => {
                const item = document.createElement('li');
                item.textContent = message;
                list.appendChild(item);
            });
            formErrors.innerHTML = '';
            formErrors.appendChild(list);
            formErrors.classList.remove('hidden');
        };

        const errorMessages = (payload) => {
            if (!payload || typeof payload !== 'object') return ['Opération impossible.'];
            const errors = payload.errors;
            if (!errors || typeof errors !== 'object') return [payload.message || 'Opération impossible.'];
            
            if (Array.isArray(errors.conflicts)) {
                return errors.conflicts.map((conflict) => {
                    const conducteur = [conflict.conducteur_nom, conflict.conducteur_postnom, conflict.conducteur_prenom]
                        .filter(Boolean).join(' ').trim();
                    return `Conflit le ${conflict.date_programme || '-'} : bus ${conflict.numero_plaque || conflict.id_bus || '-'}, conducteur ${conducteur || conflict.id_conducteur || '-'}.`;
                });
            }
            return Object.values(errors).flat().map(String);
        };

        const isPastDate = (date) => date < todayIso;

        // Mise à jour pour correspondre à la nouvelle structure HTML du bouton
        const setMode = (mode) => {
            const isEdit = mode === 'edit';
            modalTitle.textContent = isEdit ? 'Modifier la planification' : 'Nouvelle planification';
            submitButton.querySelector('span:last-child').textContent = isEdit ? 'Modifier' : 'Planifier';
            deleteButton.classList.toggle('hidden', !isEdit);
            daysFieldset.classList.toggle('hidden', isEdit);
            form.elements.date_fin.closest('div').classList.toggle('hidden', isEdit);
        };

        const openModal = (date) => {
            form.reset();
            editingProgrammeId = null;
            setMode('create');
            clearFormErrors();
            form.elements.date_debut.min = todayIso;
            form.elements.date_fin.min = todayIso;
            form.elements.date_debut.value = date;
            form.elements.date_fin.value = date;
            form.querySelectorAll('input[name="jours_semaine[]"]').forEach((checkbox) => {
                checkbox.checked = true;
            });
            modal.classList.remove('hidden');
        };

        const openEditModal = (event) => {
            const props = event.extendedProps;
            const date = (props.date_programme || event.startStr || '').slice(0, 10);
            editingProgrammeId = event.id;
            setMode('edit');
            clearFormErrors();
            form.reset();
            form.elements.date_debut.min = todayIso;
            form.elements.date_fin.min = todayIso;
            form.elements.date_debut.value = date;
            form.elements.date_fin.value = date;
            form.elements.id_trajet.value = props.id_trajet || '';
            form.elements.id_bus.value = props.id_bus || '';
            form.elements.id_conducteur.value = props.id_conducteur || '';
            form.elements.places_disponibles.value = props.places_disponibles || '';
            form.elements.statut.value = props.statut || 'Planifie';
            modal.classList.remove('hidden');
        };

        const closeModal = () => modal.classList.add('hidden');

        document.getElementById('planning-modal-close').addEventListener('click', closeModal);
        document.getElementById('planning-modal-backdrop').addEventListener('click', closeModal);
        document.getElementById('planning-cancel').addEventListener('click', closeModal);
        
        form.elements.date_debut.min = todayIso;
        form.elements.date_fin.min = todayIso;

        const calendar = new FullCalendar.Calendar(calendarEl, {
            locale: 'fr',
            initialView: 'dayGridMonth',
            height: 'auto',
            firstDay: 1,
            selectable: true,
            nowIndicator: true,
            validRange: { start: todayIso },
            dayCellClassNames: (arg) => localDateIso(arg.date) < todayIso ? ['fc-past-disabled'] : [],
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
            },
            buttonText: {
                today: "Aujourd'hui",
                month: 'Mois',
                week: 'Semaine',
                day: 'Jour',
                list: 'Liste'
            },
            events: async (info, successCallback, failureCallback) => {
                try {
                    const url = new URL('<?= base_url('api/planification/calendar') ?>');
                    url.searchParams.set('start', info.startStr);
                    url.searchParams.set('end', info.endStr);

                    const response = await apiFetch(url);
                    if (!response.ok) throw new Error('Chargement du calendrier impossible.');
                    
                    successCallback(await response.json());
                } catch (error) {
                    showPageAlert(error.message, 'error');
                    failureCallback(error);
                }
            },
            dateClick: (info) => {
                if (isPastDate(info.dateStr)) {
                    showPageAlert('Impossible de planifier avant la date actuelle.', 'error');
                    return;
                }
                openModal(info.dateStr);
            },
            eventClick: (info) => openEditModal(info.event)
        });

        calendar.render();

        deleteButton.addEventListener('click', async () => {
            if (!editingProgrammeId || !confirm('Supprimer cette planification ?')) return;

            deleteButton.disabled = true;
            try {
                const response = await apiFetch(`<?= base_url('api/planification') ?>/${editingProgrammeId}`, {
                    method: 'DELETE',
                    headers: { Accept: 'application/json' }
                });
                const json = await response.json();

                if (!response.ok || json.success === false) {
                    showFormErrors(errorMessages(json));
                    return;
                }

                closeModal();
                calendar.refetchEvents();
                showPageAlert('Planification supprimée.');
            } catch (error) {
                showFormErrors([error.message || 'Opération impossible.']);
            } finally {
                deleteButton.disabled = false;
            }
        });

        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            clearFormErrors();
            submitButton.disabled = true;
            submitButton.classList.add('opacity-60', 'cursor-wait');

            const formData = new FormData(form);
            const payload = {
                id_trajet: Number(formData.get('id_trajet')),
                id_bus: Number(formData.get('id_bus')),
                id_conducteur: Number(formData.get('id_conducteur')),
                places_disponibles: formData.get('places_disponibles') || null,
                statut: formData.get('statut') || 'Planifie'
            };

            if (editingProgrammeId) {
                payload.date_programme = formData.get('date_debut');
            } else {
                payload.date_debut = formData.get('date_debut');
                payload.date_fin = formData.get('date_fin');
                payload.jours_semaine = formData.getAll('jours_semaine[]').map(Number);
            }

            try {
                const response = await apiFetch(editingProgrammeId ? `<?= base_url('api/planification') ?>/${editingProgrammeId}` : '<?= base_url('api/planification') ?>', {
                    method: editingProgrammeId ? 'PUT' : 'POST',
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });
                const json = await response.json();

                if (!response.ok || json.success === false) {
                    showFormErrors(errorMessages(json));
                    return;
                }

                closeModal();
                calendar.refetchEvents();
                showPageAlert(editingProgrammeId ? 'Planification mise à jour.' : `${json.data.created_count || 0} programme(s) créé(s).`);
            } catch (error) {
                showFormErrors([error.message || 'Opération impossible.']);
            } finally {
                submitButton.disabled = false;
                submitButton.classList.remove('opacity-60', 'cursor-wait');
            }
        });
    });
</script>
<?= $this->endSection() ?>