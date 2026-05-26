<?= $this->extend('web/layouts/app') ?>

<?= $this->section('content') ?>

<div
    class="fixed top-[48px] left-0 w-full h-[56px] z-40 flex items-center px-gutter justify-between bg-surface-container-low border-b border-outline-variant">
    <div class="flex items-center gap-md h-full">
        <h2 class="font-h2 text-h2 text-on-surface">Réservations</h2>
    </div>
    <div class="hidden sm:flex items-center gap-xs text-body-sm text-on-surface-variant">
        <span class="material-symbols-outlined text-[18px]">point_of_sale</span>
        Vente ticket et manifeste
    </div>
</div>

<main class="pt-[128px] px-gutter pb-xl max-w-[1600px] mx-auto">
    <div id="reservation-alert" class="hidden mb-md border rounded p-md text-body-sm"></div>

    <div class="grid grid-cols-1 xl:grid-cols-[minmax(0,1fr)_360px] gap-gutter">
        <section class="bg-white border border-outline-variant rounded custom-shadow overflow-hidden">
            <div class="px-md py-sm border-b border-outline-variant bg-surface-container-lowest flex items-center justify-between">
                <h3 class="font-h2 text-h2">Nouvelle réservation</h3>
                <span id="programme-count" class="text-body-sm text-on-surface-variant">Aucun programme chargé</span>
            </div>

            <form id="reservation-form" class="p-md space-y-lg">
                <div id="reservation-form-errors" class="hidden bg-red-50 border border-red-200 text-red-800 rounded p-md text-body-sm"></div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-md">
                    <label class="block">
                        <span class="block text-body-sm font-medium text-on-surface-variant mb-xs">Date voyage</span>
                        <input id="date_programme" class="w-full rounded border-outline-variant text-body-md" type="date" name="date_programme" min="<?= esc($today) ?>" value="<?= esc($today) ?>" required>
                    </label>

                    <label class="block md:col-span-2">
                        <span class="block text-body-sm font-medium text-on-surface-variant mb-xs">Programme disponible</span>
                        <select id="id_programme" class="w-full rounded border-outline-variant text-body-md" name="id_programme" required>
                            <option value="">Sélectionner une date</option>
                        </select>
                    </label>
                </div>

                <div id="programme-details" class="hidden grid grid-cols-1 md:grid-cols-4 gap-md p-md bg-surface-container-low border border-outline-variant rounded">
                    <div>
                        <span class="block text-label-caps text-on-surface-variant">Trajet</span>
                        <strong id="summary-trajet" class="text-body-md">-</strong>
                    </div>
                    <div>
                        <span class="block text-label-caps text-on-surface-variant">Horaire</span>
                        <strong id="summary-horaire" class="text-body-md">-</strong>
                    </div>
                    <div>
                        <span class="block text-label-caps text-on-surface-variant">Bus</span>
                        <strong id="summary-bus" class="text-body-md">-</strong>
                    </div>
                    <div>
                        <span class="block text-label-caps text-on-surface-variant">Places</span>
                        <strong id="summary-places" class="text-body-md">-</strong>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-md">
                    <label class="block">
                        <span class="block text-body-sm font-medium text-on-surface-variant mb-xs">Lieu de descente</span>
                        <select id="id_lieu_reservation" class="w-full rounded border-outline-variant text-body-md" name="id_lieu_reservation">
                            <option value="">Terminus destination</option>
                        </select>
                    </label>

                    <label class="block">
                        <span class="block text-body-sm font-medium text-on-surface-variant mb-xs">Nombre de places</span>
                        <input id="nombre_places" class="w-full rounded border-outline-variant text-body-md" type="number" min="1" value="1" name="nombre_places" required>
                    </label>
                </div>

                <div class="border-t border-outline-variant pt-md">
                    <h4 class="font-h2 text-h2 mb-md">Client</h4>
                    <div class="grid grid-cols-1 md:grid-cols-[1fr_auto] gap-md">
                        <label class="block">
                            <span class="block text-body-sm font-medium text-on-surface-variant mb-xs">Téléphone</span>
                            <input id="telephone" class="w-full rounded border-outline-variant text-body-md" type="tel" name="telephone" placeholder="+243..." required>
                        </label>
                        <button id="search-client" class="self-end h-10 px-md bg-white border border-outline-variant text-secondary rounded font-medium hover:bg-surface-container-highest transition-colors flex items-center justify-center gap-xs" type="button">
                            <span class="material-symbols-outlined text-[20px]">search</span>
                            Rechercher
                        </button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-md mt-md">
                        <label class="block">
                            <span class="block text-body-sm font-medium text-on-surface-variant mb-xs">Nom client</span>
                            <input id="nom" class="w-full rounded border-outline-variant text-body-md" type="text" name="nom" placeholder="Obligatoire si nouveau client">
                        </label>
                        <label class="block">
                            <!-- <span class="block text-body-sm font-medium text-on-surface-variant mb-xs">Email</span> -->
                            <input id="email" class="w-full rounded border-outline-variant text-body-md" type="email" name="email" placeholder="Optionnel" hidden>
                        </label>
                    </div>
                    <p id="client-status" class="mt-sm text-body-sm text-on-surface-variant">Le client sera recherché par téléphone avant création.</p>
                </div>

                <div class="border-t border-outline-variant pt-md">
                    <h4 class="font-h2 text-h2 mb-md">Paiement</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-md">
                        <label class="block">
                            <span class="block text-body-sm font-medium text-on-surface-variant mb-xs">Mode paiement</span>
                            <select id="id_mode_paiement" class="w-full rounded border-outline-variant text-body-md" name="id_mode_paiement" required>
                                <option value="">Sélectionner</option>
                                <?php foreach ($modes_paiement as $mode): ?>
                                    <option value="<?= esc($mode['id_mode_paiement']) ?>"><?= esc($mode['libelle']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <div class="p-md bg-surface-container-low border border-outline-variant rounded">
                            <span class="block text-label-caps text-on-surface-variant">Total à payer</span>
                            <strong id="summary-total" class="text-h1 text-primary">0</strong>
                        </div>
                    </div>

                    <div id="external-payment-fields" class="hidden grid grid-cols-1 md:grid-cols-2 gap-md mt-md">
                        <label class="block">
                            <span class="block text-body-sm font-medium text-on-surface-variant mb-xs">Référence prestataire</span>
                            <input id="provider_reference" class="w-full rounded border-outline-variant text-body-md" type="text" name="provider_reference">
                        </label>
                        <label class="block">
                            <span class="block text-body-sm font-medium text-on-surface-variant mb-xs">Retour prestataire</span>
                            <select id="provider_status" class="w-full rounded border-outline-variant text-body-md" name="provider_status">
                                <option value="">En attente</option>
                                <option value="success">Success</option>
                                <option value="failed">Failed</option>
                            </select>
                        </label>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row justify-end gap-sm pt-sm">
                    <a id="manifest-link" class="hidden h-10 px-md bg-white border border-outline-variant text-secondary rounded font-medium hover:bg-surface-container-highest transition-colors items-center justify-center gap-xs" href="#" target="_blank">
                        <span class="material-symbols-outlined text-[20px]">print</span>
                        Manifeste A4
                    </a>
                    <button id="reservation-submit" class="h-10 px-md bg-[#2563EB] text-white rounded font-medium flex items-center justify-center gap-xs hover:opacity-90 active:scale-[0.99] transition-all" type="submit">
                        <span class="material-symbols-outlined text-[20px]">confirmation_number</span>
                        Valider et générer ticket
                    </button>
                </div>
            </form>
        </section>

        <aside class="space-y-gutter">
            <section class="bg-white border border-outline-variant rounded custom-shadow overflow-hidden">
                <div class="px-md py-sm border-b border-outline-variant bg-surface-container-lowest">
                    <h3 class="font-h2 text-h2">Dernière réservation</h3>
                </div>
                <div id="last-reservation" class="p-md text-body-sm text-on-surface-variant">
                    Aucune réservation dans cette session.
                </div>
            </section>
            <section class="bg-white border border-outline-variant rounded custom-shadow overflow-hidden">
                <div class="px-md py-sm border-b border-outline-variant bg-surface-container-lowest">
                    <h3 class="font-h2 text-h2">Programmes et manifestes</h3>
                    <p class="text-body-sm text-on-surface-variant">Inclut les programmes complets.</p>
                </div>
                <div id="programme-list" class="divide-y divide-outline-variant text-body-sm"></div>
            </section>
        </aside>
    </div>
</main>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        let token = <?= json_encode((string) ($api_token ?? ''), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
        let programmes = [];
        let programmeManifestes = [];
        let selectedProgramme = null;

        const form = document.getElementById('reservation-form');
        const formErrors = document.getElementById('reservation-form-errors');
        const pageAlert = document.getElementById('reservation-alert');
        const dateInput = document.getElementById('date_programme');
        const programmeSelect = document.getElementById('id_programme');
        const arretSelect = document.getElementById('id_lieu_reservation');
        const nombrePlacesInput = document.getElementById('nombre_places');
        const modePaiementSelect = document.getElementById('id_mode_paiement');
        const externalPaymentFields = document.getElementById('external-payment-fields');
        const submitButton = document.getElementById('reservation-submit');
        const manifestLink = document.getElementById('manifest-link');

        const normalize = (value) => String(value || '')
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '');

        const formatMoney = (amount, programme = selectedProgramme) => {
            const value = Number(amount || 0).toLocaleString('fr-FR');
            const currency = programme?.symbole || programme?.code_currency || '';

            return `${value} ${currency}`.trim();
        };

        const showPageAlert = (message, type = 'success') => {
            pageAlert.className = 'mb-md border rounded p-md text-body-sm ' + (
                type === 'success'
                    ? 'bg-emerald-50 border-emerald-200 text-emerald-800'
                    : 'bg-red-50 border-red-200 text-red-800'
            );
            pageAlert.innerHTML = message;
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
                return ['Opération impossible.'];
            }

            if (!payload.errors || typeof payload.errors !== 'object') {
                return [payload.message || 'Opération impossible.'];
            }

            return Object.values(payload.errors).flat().map(String);
        };

        const updateSummary = () => {
            const places = Math.max(1, Number(nombrePlacesInput.value || 1));
            const total = selectedProgramme ? Number(selectedProgramme.prix || 0) * places : 0;

            document.getElementById('summary-trajet').textContent = selectedProgramme
                ? `${selectedProgramme.lieu_depart} - ${selectedProgramme.lieu_arrivee}`
                : '-';
            document.getElementById('summary-horaire').textContent = selectedProgramme
                ? `${String(selectedProgramme.heure_depart).slice(0, 5)} - ${String(selectedProgramme.heure_arrivee).slice(0, 5)}`
                : '-';
            document.getElementById('summary-bus').textContent = selectedProgramme
                ? `${selectedProgramme.numero_plaque} (${selectedProgramme.marque || ''} ${selectedProgramme.modele || ''})`.trim()
                : '-';
            document.getElementById('summary-places').textContent = selectedProgramme
                ? `${selectedProgramme.places_disponibles} disponibles`
                : '-';
            document.getElementById('summary-total').textContent = formatMoney(total);
            document.getElementById('programme-details').classList.toggle('hidden', !selectedProgramme);
            manifestLink.classList.toggle('hidden', !selectedProgramme);
            manifestLink.classList.toggle('flex', !!selectedProgramme);

            if (selectedProgramme) {
                manifestLink.href = `<?= base_url('programmes') ?>/${selectedProgramme.id_programme}/manifeste`;
                nombrePlacesInput.max = selectedProgramme.places_disponibles;
            }
        };

        const renderProgrammeList = () => {
            const list = document.getElementById('programme-list');
            list.innerHTML = '';

            if (programmeManifestes.length === 0) {
                list.innerHTML = '<div class="p-md text-on-surface-variant">Aucun programme planifié pour cette date.</div>';
                return;
            }

            programmeManifestes.forEach((programme) => {
                const placesDisponibles = Number(programme.places_disponibles || 0);
                const capacite = Number(programme.nombre_places || 0);
                const isComplet = placesDisponibles <= 0;
                const canReserve = !isComplet && programmes.some((item) => String(item.id_programme) === String(programme.id_programme));
                const badgeLabel = isComplet ? 'Complet' : (canReserve ? 'Disponible' : (programme.statut || 'Indisponible'));
                const manifestUrl = programme.manifeste_url || `<?= base_url('programmes') ?>/${programme.id_programme}/manifeste`;
                const item = document.createElement('div');
                item.className = `p-md ${isComplet ? 'bg-red-50' : 'hover:bg-surface-container-low'} transition-colors`;
                item.innerHTML = `
                    <div class="flex items-start justify-between gap-sm">
                        <div>
                            <strong class="block text-on-surface">${programme.lieu_depart} - ${programme.lieu_arrivee}</strong>
                            <span class="block text-on-surface-variant">${String(programme.heure_depart).slice(0, 5)} | ${programme.numero_plaque}</span>
                            <span class="block ${isComplet ? 'text-red-700' : 'text-on-surface-variant'}">${placesDisponibles} place(s) disponible(s)${capacite ? ' / ' + capacite : ''}</span>
                        </div>
                        <span class="shrink-0 text-[11px] px-xs py-[2px] rounded ${canReserve ? 'bg-emerald-50 text-emerald-700' : 'bg-red-100 text-red-800'}">${badgeLabel}</span>
                    </div>
                    <div class="flex gap-sm mt-sm">
                        ${canReserve ? '<button type="button" data-action="select" class="h-8 px-sm bg-white border border-outline-variant rounded text-secondary hover:bg-surface-container-highest">Réserver</button>' : ''}
                        <a class="h-8 px-sm bg-[#2563EB] text-white rounded flex items-center" href="${manifestUrl}" target="_blank">Manifeste</a>
                    </div>
                `;
                item.querySelector('[data-action="select"]')?.addEventListener('click', () => {
                    programmeSelect.value = programme.id_programme;
                    programmeSelect.dispatchEvent(new Event('change'));
                });
                list.appendChild(item);
            });
        };

        const loadProgrammeManifestes = async () => {
            const url = new URL('<?= base_url('api/reservations/programmes-manifestes') ?>');
            url.searchParams.set('date', dateInput.value);

            const response = await apiFetch(url);
            const json = await response.json();

            if (!response.ok || json.success === false) {
                throw new Error(json.message || 'Chargement des manifestes impossible.');
            }

            programmeManifestes = json.data.items || [];
            renderProgrammeList();
        };

        const loadProgrammes = async () => {
            clearFormErrors();
            selectedProgramme = null;
            programmeManifestes = [];
            programmeSelect.innerHTML = '<option value="">Chargement...</option>';
            arretSelect.innerHTML = '<option value="">Terminus destination</option>';
            document.getElementById('programme-list').innerHTML = '<div class="p-md text-on-surface-variant">Chargement des programmes...</div>';
            updateSummary();

            const url = new URL('<?= base_url('api/reservations/programmes') ?>');
            url.searchParams.set('date', dateInput.value);

            try {
                const response = await apiFetch(url);
                const json = await response.json();

                if (!response.ok || json.success === false) {
                    throw new Error(json.message || 'Chargement impossible.');
                }

                programmes = json.data.items || [];
                document.getElementById('programme-count').textContent = `${programmes.length} programme(s) disponible(s)`;
                programmeSelect.innerHTML = '<option value="">Sélectionner</option>';
                programmes.forEach((programme) => {
                    const option = document.createElement('option');
                    option.value = programme.id_programme;
                    option.textContent = `${String(programme.heure_depart).slice(0, 5)} | ${programme.lieu_depart} - ${programme.lieu_arrivee} | ${programme.numero_plaque} | ${programme.places_disponibles} places`;
                    programmeSelect.appendChild(option);
                });
                await loadProgrammeManifestes();
            } catch (error) {
                programmes = [];
                programmeManifestes = [];
                programmeSelect.innerHTML = '<option value="">Aucun programme</option>';
                renderProgrammeList();
                showPageAlert(error.message, 'error');
            }
        };

        const loadArrets = async () => {
            arretSelect.innerHTML = '<option value="">Terminus destination</option>';

            if (!selectedProgramme) {
                return;
            }

            const response = await apiFetch(`<?= base_url('api/reservations/programmes') ?>/${selectedProgramme.id_programme}/arrets`);
            const json = await response.json();

            if (!response.ok || json.success === false) {
                showPageAlert(json.message || 'Chargement des arrêts impossible.', 'error');
                return;
            }

            (json.data.items || []).forEach((arret) => {
                const option = document.createElement('option');
                option.value = arret.id_lieu;
                option.textContent = `${arret.nom_lieu}${arret.temps_estime ? ' - ' + arret.temps_estime : ''}`;
                arretSelect.appendChild(option);
            });
        };

        const searchClient = async () => {
            const telephone = document.getElementById('telephone').value.trim();
            const status = document.getElementById('client-status');

            if (!telephone) {
                status.textContent = 'Indiquer le téléphone avant la recherche.';
                status.className = 'mt-sm text-body-sm text-red-700';
                return;
            }

            const url = new URL('<?= base_url('api/reservations/client') ?>');
            url.searchParams.set('telephone', telephone);
            const response = await apiFetch(url);
            const json = await response.json();

            if (!response.ok || json.success === false) {
                status.textContent = json.message || 'Recherche impossible.';
                status.className = 'mt-sm text-body-sm text-red-700';
                return;
            }

            if (json.data.exists && json.data.client) {
                document.getElementById('nom').value = json.data.client.nom || '';
                document.getElementById('email').value = json.data.client.email || '';
                status.textContent = 'Client existant trouvé.';
                status.className = 'mt-sm text-body-sm text-emerald-700';
                return;
            }

            status.textContent = 'Nouveau client : compléter le nom avant validation.';
            status.className = 'mt-sm text-body-sm text-on-surface-variant';
        };

        programmeSelect.addEventListener('change', () => {
            selectedProgramme = programmes.find((programme) => String(programme.id_programme) === programmeSelect.value) || null;
            updateSummary();
            loadArrets();
        });

        dateInput.addEventListener('change', loadProgrammes);
        nombrePlacesInput.addEventListener('input', updateSummary);
        document.getElementById('search-client').addEventListener('click', searchClient);
        document.getElementById('telephone').addEventListener('blur', searchClient);
        modePaiementSelect.addEventListener('change', () => {
            const selectedText = modePaiementSelect.options[modePaiementSelect.selectedIndex]?.textContent || '';
            const isCash = ['cash', 'comptant', 'espece'].some((word) => normalize(selectedText).includes(word));
            externalPaymentFields.classList.toggle('hidden', isCash || !selectedText);
        });

        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            clearFormErrors();
            submitButton.disabled = true;
            submitButton.classList.add('opacity-60');

            const formData = new FormData(form);
            const payload = {
                id_programme: Number(formData.get('id_programme')),
                id_lieu_reservation: formData.get('id_lieu_reservation') || null,
                nombre_places: Number(formData.get('nombre_places')),
                id_mode_paiement: Number(formData.get('id_mode_paiement')),
                client: {
                    telephone: formData.get('telephone'),
                    nom: formData.get('nom'),
                    email: formData.get('email')
                },
                payment: {
                    provider_reference: formData.get('provider_reference'),
                    provider_status: formData.get('provider_status')
                }
            };

            try {
                const response = await apiFetch('<?= base_url('api/reservations') ?>', {
                    method: 'POST',
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

                const reservation = json.data.reservation;
                document.getElementById('last-reservation').innerHTML = `
                    <strong class="block text-on-surface">${reservation.reference_reservation}</strong>
                    <span class="block">${reservation.client_nom} - ${reservation.client_telephone}</span>
                    <span class="block">${reservation.lieu_depart} - ${reservation.lieu_arrivee}</span>
                    <span class="block">${formatMoney(reservation.montant_final, reservation)}</span>
                    <div class="flex gap-sm mt-md">
                        <a class="h-9 px-sm bg-[#2563EB] text-white rounded flex items-center" href="${json.data.ticket_url}" target="_blank">Ticket</a>
                        <a class="h-9 px-sm bg-white border border-outline-variant rounded flex items-center" href="${json.data.manifeste_url}" target="_blank">Manifeste</a>
                    </div>
                `;
                showPageAlert(`Réservation validée. <a class="underline font-medium" href="${json.data.ticket_url}" target="_blank">Imprimer le ticket</a>`);
                form.reset();
                dateInput.value = '<?= esc($today) ?>';
                await loadProgrammes();
            } catch (error) {
                showFormErrors([error.message || 'Opération impossible.']);
            } finally {
                submitButton.disabled = false;
                submitButton.classList.remove('opacity-60');
            }
        });

        loadProgrammes();
    });
</script>
<?= $this->endSection() ?>
