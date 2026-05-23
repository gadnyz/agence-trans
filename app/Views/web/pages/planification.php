<?= $this->extend('web/layouts/app') ?>

<?= $this->section('styles') ?>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/locales-all.global.min.js"></script>
<style>
    .fc {
        --fc-border-color: #c4c7c8;
        --fc-button-bg-color: #ffffff;
        --fc-button-border-color: #c4c7c8;
        --fc-button-text-color: #444748;
        --fc-button-active-bg-color: #2563eb;
        --fc-button-active-border-color: #2563eb;
        --fc-today-bg-color: #eff6ff;
        font-size: 14px;
    }

    .fc .fc-toolbar-title {
        font-size: 18px;
        font-weight: 600;
    }

    .fc .fc-button {
        border-radius: 4px;
        box-shadow: none;
        text-transform: none;
    }

    .fc-event {
        border-radius: 4px;
        padding: 2px 4px;
    }

    .fc .fc-past-disabled {
        background: #f3f4f6;
        color: #9ca3af;
        cursor: not-allowed;
    }

    .fc .fc-past-disabled .fc-daygrid-day-number {
        opacity: .55;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div
    class="fixed top-[48px] left-0 w-full h-[56px] z-40 flex items-center px-gutter justify-between bg-surface-container-low border-b border-outline-variant">
    <div class="flex items-center gap-md h-full">
        <h2 class="font-h2 text-h2 text-on-surface">Planification</h2>
    </div>
    <div class="hidden sm:flex items-center gap-xs text-body-sm text-on-surface-variant">
        <span class="material-symbols-outlined text-[18px]">touch_app</span>
        Cliquer sur une date pour planifier
    </div>
</div>

<main class="pt-[128px] px-gutter pb-xl max-w-[1600px] mx-auto">
    <div id="planning-alert" class="hidden mb-md border rounded p-md text-body-sm"></div>

    <section class="bg-white border border-outline-variant rounded custom-shadow overflow-hidden">
        <div class="px-md py-sm border-b border-outline-variant bg-surface-container-lowest flex items-center justify-between">
            <h3 class="font-h2 text-h2">Calendrier des programmes</h3>
            <div class="flex items-center gap-sm">
                <span class="inline-flex items-center gap-xs text-body-sm text-on-surface-variant">
                    <span class="w-3 h-3 rounded-sm bg-[#2563eb]"></span> Planifié
                </span>
                <span class="inline-flex items-center gap-xs text-body-sm text-on-surface-variant">
                    <span class="w-3 h-3 rounded-sm bg-[#16a34a]"></span> Ouvert
                </span>
                <span class="inline-flex items-center gap-xs text-body-sm text-on-surface-variant">
                    <span class="w-3 h-3 rounded-sm bg-[#ca8a04]"></span> Suspendu
                </span>
            </div>
        </div>
        <div class="p-md">
            <div id="planning-calendar"></div>
        </div>
    </section>
</main>

<div id="planning-modal" class="fixed inset-0 z-[80] hidden">
    <div id="planning-modal-backdrop" class="absolute inset-0 bg-black/40"></div>
    <div class="relative min-h-full flex items-center justify-center p-md">
        <section class="w-full max-w-2xl bg-white border border-outline-variant rounded shadow-xl overflow-hidden">
            <div class="px-md py-sm border-b border-outline-variant bg-surface-container-lowest flex items-center justify-between">
                <h3 id="planning-modal-title" class="font-h2 text-h2">Nouvelle planification</h3>
                <button id="planning-modal-close" class="material-symbols-outlined text-on-surface-variant p-xs hover:bg-surface-container-high rounded" type="button">close</button>
            </div>
            <form id="planning-form" class="p-md space-y-md">
                <div id="planning-form-errors" class="hidden bg-red-50 border border-red-200 text-red-800 rounded p-md text-body-sm"></div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-md">
                    <label class="block">
                        <span class="block text-body-sm font-medium text-on-surface-variant mb-xs">Date début</span>
                        <input id="date_debut" class="w-full rounded border-outline-variant text-body-md" type="date" name="date_debut" required>
                    </label>
                    <label class="block">
                        <span class="block text-body-sm font-medium text-on-surface-variant mb-xs">Date fin</span>
                        <input id="date_fin" class="w-full rounded border-outline-variant text-body-md" type="date" name="date_fin" required>
                    </label>
                </div>

                <label class="block">
                    <span class="block text-body-sm font-medium text-on-surface-variant mb-xs">Trajet</span>
                    <select class="w-full rounded border-outline-variant text-body-md" name="id_trajet" required>
                        <option value="">Sélectionner</option>
                        <?php foreach ($trajets as $trajet): ?>
                            <?php
                            $depart = $lieux_map[$trajet['id_lieu_depart']] ?? 'Départ inconnu';
                            $arrivee = $lieux_map[$trajet['id_lieu_arrivee']] ?? 'Arrivée inconnue';
                            $horaire = $horaires_map[$trajet['id_horaire']] ?? 'Horaire non défini';
                            ?>
                            <option value="<?= esc($trajet['id_trajet']) ?>">
                                <?= esc($depart . ' - ' . $arrivee . ' | ' . $horaire) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-md">
                    <label class="block">
                        <span class="block text-body-sm font-medium text-on-surface-variant mb-xs">Bus</span>
                        <select class="w-full rounded border-outline-variant text-body-md" name="id_bus" required>
                            <option value="">Sélectionner</option>
                            <?php foreach ($bus as $item): ?>
                                <option value="<?= esc($item['id_bus']) ?>" data-places="<?= esc($item['nombre_places'] ?? '') ?>">
                                    <?= esc($item['numero_plaque'] . ' - ' . ($item['nombre_places'] ?? 0) . ' places') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label class="block">
                        <span class="block text-body-sm font-medium text-on-surface-variant mb-xs">Conducteur</span>
                        <select class="w-full rounded border-outline-variant text-body-md" name="id_conducteur" required>
                            <option value="">Sélectionner</option>
                            <?php foreach ($conducteurs as $conducteur): ?>
                                <?php $nom = trim(($conducteur['nom'] ?? '') . ' ' . ($conducteur['postnom'] ?? '') . ' ' . ($conducteur['prenom'] ?? '')); ?>
                                <option value="<?= esc($conducteur['id_conducteur']) ?>"><?= esc($nom) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-md">
                    <label class="block">
                        <span class="block text-body-sm font-medium text-on-surface-variant mb-xs">Places</span>
                        <input class="w-full rounded border-outline-variant text-body-md" type="number" 
                            name="places_disponibles" min="1" placeholder="Par defaut bus">
                    </label>
                    <label class="block">
                        <span class="block text-body-sm font-medium text-on-surface-variant mb-xs">Statut</span>
                        <select class="w-full rounded border-outline-variant text-body-md" name="statut">
                            <option value="Planifie">Planifie</option>
                            <option value="Ouvert">Ouvert</option>
                            <option value="Suspendu">Suspendu</option>
                            <option value="Annule">Annule</option>
                            <option value="Termine">Termine</option>
                        </select>
                    </label>
                </div>

                <fieldset id="planning-days-fieldset">
                    <legend class="block text-body-sm font-medium text-on-surface-variant mb-xs">Jours</legend>
                    <div class="grid grid-cols-4 sm:grid-cols-7 gap-xs">
                        <?php $days = [1 => 'Lun', 2 => 'Mar', 3 => 'Mer', 4 => 'Jeu', 5 => 'Ven', 6 => 'Sam', 7 => 'Dim']; ?>
                        <?php foreach ($days as $value => $label): ?>
                            <label class="flex items-center justify-center gap-xs px-xs py-xs border border-outline-variant rounded bg-white text-body-sm">
                                <input class="rounded border-outline-variant" type="checkbox" name="jours_semaine[]" value="<?= esc($value) ?>" checked>
                                <?= esc($label) ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </fieldset>

                <div class="flex justify-end gap-sm pt-sm">
                    <button id="planning-delete" class="hidden h-10 px-md bg-white border border-red-200 text-red-700 rounded font-medium hover:bg-red-50 transition-colors" type="button">Supprimer</button>
                    <button id="planning-cancel" class="h-10 px-md bg-white border border-outline-variant text-secondary rounded font-medium hover:bg-surface-container-highest transition-colors" type="button">Annuler</button>
                    <button id="planning-submit" class="h-10 px-md bg-[#2563EB] text-white rounded font-medium flex items-center gap-xs hover:opacity-90 active:scale-[0.99] transition-all" type="submit">
                        <span class="material-symbols-outlined text-[20px]">event_available</span>
                        Planifier
                    </button>
                </div>
            </form>
        </section>
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
        const localDateIso = (date) => {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');

            return `${year}-${month}-${day}`;
        };
        const todayIso = localDateIso(new Date());
        let editingProgrammeId = null;

        const showPageAlert = (message, type = 'success') => {
            pageAlert.className = 'mb-md border rounded p-md text-body-sm ' + (
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
                headers: {
                    Accept: 'application/json'
                }
            });
            const json = await response.json();

            if (!response.ok || json.success === false || !json.data?.access_token) {
                throw new Error(json.message || 'Session expiree. Veuillez vous reconnecter.');
            }

            token = json.data.access_token;
        };

        const apiFetch = async (url, options = {}, retry = true) => {
            const headers = {
                Accept: 'application/json',
                ...(options.headers || {}),
                Authorization: `Bearer ${token}`
            };
            let response = await fetch(url, {
                ...options,
                headers
            });

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
            list.className = 'list-disc pl-lg space-y-xs';
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
            if (!payload || typeof payload !== 'object') {
                return ['Operation impossible.'];
            }

            const errors = payload.errors;

            if (!errors || typeof errors !== 'object') {
                return [payload.message || 'Operation impossible.'];
            }

            if (Array.isArray(errors.conflicts)) {
                return errors.conflicts.map((conflict) => {
                    const conducteur = [conflict.conducteur_nom, conflict.conducteur_postnom, conflict.conducteur_prenom]
                        .filter(Boolean)
                        .join(' ')
                        .trim();

                    return `Conflit le ${conflict.date_programme || '-'} : bus ${conflict.numero_plaque || conflict.id_bus || '-'}, conducteur ${conducteur || conflict.id_conducteur || '-'}.`;
                });
            }

            return Object.values(errors).flat().map(String);
        };

        const isPastDate = (date) => date < todayIso;

        const setMode = (mode) => {
            const isEdit = mode === 'edit';
            modalTitle.textContent = isEdit ? 'Modifier la planification' : 'Nouvelle planification';
            submitButton.lastChild.textContent = isEdit ? ' Modifier' : ' Planifier';
            deleteButton.classList.toggle('hidden', !isEdit);
            daysFieldset.classList.toggle('hidden', isEdit);
            form.elements.date_fin.closest('label').classList.toggle('hidden', isEdit);
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

        const closeModal = () => {
            modal.classList.add('hidden');
        };

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
            validRange: {
                start: todayIso
            },
            dayCellClassNames: (arg) => {
                return localDateIso(arg.date) < todayIso ? ['fc-past-disabled'] : [];
            },
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
            },
            buttonText: {
                today: 'Aujourd’hui',
                month: 'Mois',
                week: 'Semaine',
                day: 'Jour',
                list: 'Liste'
            },
            events: async (info, successCallback, failureCallback) => {
                try {
                    const url = new URL('<?= base_url('api/planifications/calendar') ?>');
                    url.searchParams.set('start', info.startStr);
                    url.searchParams.set('end', info.endStr);

                    const response = await apiFetch(url);

                    if (!response.ok) {
                        throw new Error('Chargement du calendrier impossible.');
                    }

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
            eventClick: (info) => {
                openEditModal(info.event);
            }
        });

        calendar.render();

        deleteButton.addEventListener('click', async () => {
            if (!editingProgrammeId || !confirm('Supprimer cette planification ?')) {
                return;
            }

            deleteButton.disabled = true;

            try {
                const response = await apiFetch(`<?= base_url('api/planifications') ?>/${editingProgrammeId}`, {
                    method: 'DELETE',
                    headers: {
                        Accept: 'application/json'
                    }
                });
                const json = await response.json();

                if (!response.ok || json.success === false) {
                    showFormErrors(errorMessages(json));
                    return;
                }

                closeModal();
                calendar.refetchEvents();
                showPageAlert('Planification supprimee.');
            } catch (error) {
                showFormErrors([error.message || 'Operation impossible.']);
            } finally {
                deleteButton.disabled = false;
            }
        });

        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            clearFormErrors();
            submitButton.disabled = true;
            submitButton.classList.add('opacity-60');

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
                const response = await apiFetch(editingProgrammeId ? `<?= base_url('api/planifications') ?>/${editingProgrammeId}` : '<?= base_url('api/planifications') ?>', {
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
                showPageAlert(editingProgrammeId ? 'Planification mise a jour.' : `${json.data.created_count || 0} programme(s) cree(s).`);
            } catch (error) {
                showFormErrors([error.message || 'Operation impossible.']);
            } finally {
                submitButton.disabled = false;
                submitButton.classList.remove('opacity-60');
            }
        });
    });
</script>
<?= $this->endSection() ?>
