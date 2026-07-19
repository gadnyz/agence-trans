<?= $this->extend($layout ?? 'web/layouts/driver') ?>

<?= $this->section('content') ?>

<?php
$user        = session()->get('user') ?? [];
$prenom      = $user['prenom'] ?? '';
$nom         = $user['nom']    ?? '';
$displayName = trim("$prenom $nom") ?: ($user['username'] ?? 'Chauffeur');
$initials    = strtoupper(substr($prenom ?: $displayName, 0, 1) . substr($nom, 0, 1));
$today       = date('d/m/Y');
$todayIso    = date('Y-m-d');
$dayName     = ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'][date('w')];
$programmes  = $programmes ?? [];
?>

<!-- ── Top bar (Design System Admin) ── -->
<div class="max-w-[1600px] mx-auto px-4 mt-4">
    <div class="w-full rounded-2xl mb-6 bg-white border border-gray-200 shadow-sm p-4">
        <div class="flex flex-col gap-4">
            <!-- Header principal -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 md:gap-3">
                    <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-blue-50 text-blue-600">
                        <span class="material-symbols-outlined text-[20px]">calendar_today</span>
                    </div>
                    <h2 class="text-lg font-semibold text-gray-900 tracking-tight">Mon Planning</h2>
                </div>
                <div class="hidden sm:flex items-center gap-2 text-sm text-primary bg-primary-container/20 px-4 py-1.5 rounded-full border border-primary/20 shadow-sm whitespace-nowrap">
                    <span class="material-symbols-outlined text-[16px] text-primary">verified</span>
                    <span class="text-on-primary-container font-semibold">Chauffeur Actif</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ── Main Grid Layout ── -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-lg max-w-[1600px] mx-auto px-4 pb-12">
    
    <!-- Left Column: Stats & Schedules (spans 2 columns on large screens) -->
    <div class="lg:col-span-2 space-y-lg">
        
        <!-- ── Stats Grid (Design System Admin) ── -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-gutter">
            <!-- Stat 1: Today's Trips -->
            <div class="bg-surface-container-lowest border border-gray-100 rounded-2xl p-md shadow-soft flex items-center gap-md">
                <div class="w-12 h-12 rounded-xl bg-primary-container text-primary flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[24px]">route</span>
                </div>
                <div>
                    <p class="text-label-sm text-outline font-medium">Voyages du jour</p>
                    <p class="text-title-lg font-bold text-on-surface"><?= count($programmes) ?></p>
                </div>
            </div>

            <!-- Stat 2: Total Trips Completed -->
            <div class="bg-surface-container-lowest border border-gray-100 rounded-2xl p-md shadow-soft flex items-center gap-md">
                <div class="w-12 h-12 rounded-xl bg-success-container text-success flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[24px]">local_shipping</span>
                </div>
                <div>
                    <p class="text-label-sm text-outline font-medium">Total Voyages</p>
                    <p class="text-title-lg font-bold text-on-surface"><?= esc($driverStats['total_voyages_assures'] ?? 0) ?></p>
                </div>
            </div>

            <!-- Stat 3: Different Bus Driven -->
            <div class="bg-surface-container-lowest border border-gray-100 rounded-2xl p-md shadow-soft flex items-center gap-md">
                <div class="w-12 h-12 rounded-xl bg-secondary-container text-secondary flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[24px]">directions_bus</span>
                </div>
                <div>
                    <p class="text-label-sm text-outline font-medium">Bus différents</p>
                    <p class="text-title-lg font-bold text-on-surface"><?= esc($driverStats['nombre_bus_differents_utilises'] ?? 0) ?></p>
                </div>
            </div>
        </div>

        <!-- ── Current Planning Card ── -->
        <div class="bg-surface-container-lowest border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-outline-variant/60 bg-surface-container-low/30 flex items-center gap-md">
                <span class="material-symbols-outlined text-primary">event_note</span>
                <h3 class="font-title-md text-title-md text-on-surface font-bold">Planning d'aujourd'hui — <?= esc($dayName) ?> <?= esc($today) ?></h3>
            </div>

            <?php if (empty($programmes)): ?>
                <div class="flex flex-col items-center justify-center py-12 text-outline gap-md">
                    <span class="material-symbols-outlined text-[56px] text-outline-variant">event_available</span>
                    <div class="text-center">
                        <p class="font-semibold text-on-surface text-base">Aucun voyage planifié aujourd'hui</p>
                        <p class="text-body-sm text-outline mt-1">Profitez de votre journée de repos !</p>
                    </div>
                </div>
            <?php else: ?>
                <div class="divide-y divide-outline-variant/40">
                    <?php foreach ($programmes as $prog): ?>
                        <?php
                        $statut = $prog['statut'] ?? 'Planifié';
                        $statBg = match(true) {
                            str_contains(strtolower($statut), 'ouvert')   => 'bg-success-container text-success border border-success/10',
                            str_contains(strtolower($statut), 'planif')   => 'bg-primary-container text-primary border border-primary/10',
                            str_contains(strtolower($statut), 'suspendu') => 'bg-warning-container text-warning border border-warning/10',
                            str_contains(strtolower($statut), 'termin')   => 'bg-surface-container-high text-on-surface-variant',
                            default => 'bg-surface-container text-on-surface-variant',
                        };
                        ?>
                        <div class="p-6 hover:bg-surface-container-low/20 transition-colors">
                            <div class="flex items-start justify-between gap-md">
                                <div class="flex items-start gap-lg">
                                    <!-- Heure départ -->
                                    <div class="text-center shrink-0 w-16 pt-xs">
                                        <p class="font-bold text-on-surface text-body-md"><?= esc(substr($prog['heure_depart'] ?? '—', 0, 5)) ?></p>
                                        <p class="text-label-sm text-outline">Départ</p>
                                    </div>
                                    <div class="w-px h-12 bg-outline-variant/60 shrink-0 self-stretch"></div>
                                    <!-- Détails trajet -->
                                    <div class="flex-1 min-w-0">
                                        <p class="font-title-sm text-title-sm text-on-surface font-semibold text-base">
                                            <?= esc(($prog['lieu_depart'] ?? '?') . ' → ' . ($prog['lieu_arrivee'] ?? '?')) ?>
                                        </p>
                                        <div class="flex flex-wrap gap-sm mt-xs">
                                            <span class="inline-flex items-center gap-xs text-body-sm text-outline">
                                                <span class="material-symbols-outlined text-[14px]">directions_bus</span>
                                                Bus: <span class="font-semibold text-on-surface-variant"><?= esc($prog['numero_plaque'] ?? '—') ?></span>
                                            </span>
                                            <span class="h-3 w-px bg-outline-variant/60 self-center"></span>
                                            <span class="inline-flex items-center gap-xs text-body-sm text-outline">
                                                <span class="material-symbols-outlined text-[14px]">people</span>
                                                <?= esc($prog['nb_passagers']) ?> passagers
                                            </span>
                                            <?php if (!empty($prog['heure_arrivee'])): ?>
                                            <span class="h-3 w-px bg-outline-variant/60 self-center"></span>
                                            <span class="inline-flex items-center gap-xs text-body-sm text-outline">
                                                <span class="material-symbols-outlined text-[14px]">flag</span>
                                                Arrivée estimée: <span class="font-semibold text-on-surface-variant"><?= esc(substr($prog['heure_arrivee'], 0, 5)) ?></span>
                                            </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <span class="px-sm py-xs text-label-sm rounded-full font-medium shrink-0 <?= $statBg ?>">
                                    <?= esc($statut) ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- ── Recent History Card ── -->
        <div class="bg-surface-container-lowest border border-gray-100 rounded-2xl shadow-sm overflow-hidden" id="upcoming-section">
            <div class="flex items-center justify-between px-6 py-4 border-b border-outline-variant/60 bg-surface-container-low/30">
                <div class="flex items-center gap-md">
                    <span class="material-symbols-outlined text-secondary">schedule</span>
                    <h3 class="font-title-md text-title-md text-on-surface font-bold">Programmes à venir</h3>
                </div>
                <button
                    id="btn-refresh-upcoming"
                    class="inline-flex items-center gap-xs px-md py-1.5 bg-surface-container border border-outline-variant rounded-xl text-label-sm font-semibold hover:bg-surface-container-high transition-all shadow-soft"
                >
                    <span class="material-symbols-outlined text-[16px]">refresh</span>
                    Actualiser
                </button>
            </div>

            <div id="upcoming-list">
                <div class="flex justify-center items-center py-12">
                    <div class="animate-spin w-8 h-8 border-2 border-primary border-t-transparent rounded-full"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Sidebar Widgets (spans 1 column on large screens) -->
    <div class="space-y-lg">
        
        <!-- ── Driver Profile Card ── -->
        <div class="bg-surface-container-lowest border border-gray-100 rounded-2xl shadow-sm p-6 space-y-md">
            <h3 class="text-base font-bold text-on-surface border-b border-outline-variant/60 pb-3 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-[20px]">badge</span>
                Profil Chauffeur
            </h3>
            
            <?php if ($conducteur): ?>
                <div class="flex flex-col items-center text-center py-2 border-b border-outline-variant/40 pb-4">
                    <div class="w-20 h-20 rounded-full bg-primary-container text-primary flex items-center justify-center font-bold text-[28px] shadow-sm mb-3 border border-primary/10">
                        <?= esc($initials) ?>
                    </div>
                    <h4 class="text-title-md font-bold text-on-surface"><?= esc($conducteur['prenom'] . ' ' . $conducteur['nom'] . ' ' . ($conducteur['postnom'] ?? '')) ?></h4>
                    <p class="text-body-sm text-outline font-medium mt-0.5">Chauffeur Kashala Trans</p>
                </div>
                
                <div class="space-y-sm text-body-sm pt-2">
                    <div class="flex justify-between py-1.5 border-b border-outline-variant/30">
                        <span class="text-outline">Téléphone</span>
                        <span class="font-semibold text-on-surface"><?= esc($conducteur['telephone'] ?: '—') ?></span>
                    </div>
                    <div class="flex justify-between py-1.5 border-b border-outline-variant/30">
                        <span class="text-outline">N° Permis</span>
                        <span class="font-semibold text-on-surface"><?= esc($conducteur['numero_permis'] ?: '—') ?></span>
                    </div>
                    <div class="flex justify-between py-1.5 border-b border-outline-variant/30">
                        <span class="text-outline">Date d'embauche</span>
                        <span class="font-semibold text-on-surface"><?= esc(!empty($conducteur['date_embauche']) ? date('d/m/Y', strtotime($conducteur['date_embauche'])) : '—') ?></span>
                    </div>
                    <div class="flex flex-col gap-0.5 py-1.5">
                        <span class="text-outline">Adresse domicile</span>
                        <span class="font-semibold text-on-surface leading-tight mt-0.5"><?= esc($conducteur['adresse'] ?: '—') ?></span>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center py-6">
                    <span class="material-symbols-outlined text-[48px] text-outline-variant mb-2">person_off</span>
                    <p class="text-body-md text-on-surface font-medium">Aucun profil associé</p>
                    <p class="text-body-sm text-outline mt-1">Veuillez contacter le support pour relier votre compte utilisateur.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- ── Note Card ── -->
        <!-- <div class="flex items-start gap-md p-lg bg-primary-container/10 border border-primary/20 rounded-2xl text-on-surface-variant">
            <span class="material-symbols-outlined text-primary shrink-0">info</span>
            <div class="space-y-1">
                <h4 class="text-body-md font-semibold text-on-primary-container leading-tight">Note de service</h4>
                <p class="text-body-sm text-on-primary-container/80 leading-normal">
                    Pour toute modification de planning, incident sur la route ou retard, veuillez contacter le répartiteur de garde.
                </p>
            </div>
        </div> -->
        
    </div>

</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
const API_TOKEN = <?= json_encode((string)($api_token ?? ''), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
const BASE_URL  = '<?= base_url() ?>';
const TODAY     = '<?= $todayIso ?>';
const ID_CONDUCTEUR = <?= json_encode($conducteur ? (int)$conducteur['id_conducteur'] : null) ?>;

async function apiFetch(url, options = {}) {
    const headers = { Accept: 'application/json', Authorization: `Bearer ${API_TOKEN}`, ...(options.headers || {}) };
    return fetch(url, { ...options, headers });
}

function esc(str) {
    const d = document.createElement('div');
    d.textContent = str ?? '';
    return d.innerHTML;
}

async function loadUpcomingProgrammes() {
    const container = document.getElementById('upcoming-list');
    container.innerHTML = '<div class="flex justify-center items-center py-xl"><div class="animate-spin w-8 h-8 border-2 border-primary border-t-transparent rounded-full"></div></div>';

    try {
        // On calcule la date de demain et celle dans 30 jours
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        const dateDebutStr = tomorrow.toISOString().slice(0, 10);
        
        const nextMonth = new Date();
        nextMonth.setDate(nextMonth.getDate() + 30);
        const dateFinStr = nextMonth.toISOString().slice(0, 10);
        
        // Appel API pour les voyages futurs
        let url = `${BASE_URL}api/planification?per_page=30&date_debut=${dateDebutStr}&date_fin=${dateFinStr}&sort=date_programme`;
        if (ID_CONDUCTEUR) {
            url += `&id_conducteur=${ID_CONDUCTEUR}`;
        }
        
        const response = await apiFetch(url);
        const json     = await response.json();
        const items    = json.data?.items ?? [];

        // Sécurité supplémentaire : s'assurer que c'est strictement après aujourd'hui
        const upcoming = items.filter(p => p.date_programme > TODAY);

        if (!upcoming.length) {
            container.innerHTML = `
                <div class="flex flex-col items-center justify-center py-12 text-outline gap-sm">
                    <span class="material-symbols-outlined text-[48px] text-outline-variant">event_busy</span>
                    <p class="text-body-md text-on-surface font-medium">Aucun voyage planifié prochainement.</p>
                </div>`;
            return;
        }

        container.innerHTML = '<div class="divide-y divide-outline-variant/40">' +
            upcoming.map(p => {
                const depart  = p.lieu_depart ?? '?';
                const arrivee = p.lieu_arrivee ?? '?';
                const heure   = (p.heure_depart ?? '').slice(0, 5);
                const dateParts = (p.date_programme ?? '').split('-');
                const formattedDate = dateParts.length === 3 ? `${dateParts[2]}/${dateParts[1]}` : (p.date_programme ?? '');
                
                const statut = p.statut ?? 'Planifié';
                const statBg = statut.toLowerCase().includes('ouvert')   ? 'bg-success-container text-success border border-success/10' :
                               statut.toLowerCase().includes('planif')   ? 'bg-primary-container text-primary border border-primary/10' :
                               statut.toLowerCase().includes('suspendu') ? 'bg-warning-container text-warning border border-warning/10' :
                               'bg-surface-container-high text-on-surface-variant';

                return `
                <div class="p-6 hover:bg-surface-container-low/20 transition-colors">
                    <div class="flex items-start justify-between gap-md">
                        <div class="flex items-start gap-lg">
                            <div class="text-center shrink-0 w-16 pt-xs">
                                <p class="font-bold text-on-surface text-body-md">${esc(formattedDate)}</p>
                                <p class="text-label-sm text-outline">${esc(heure)}</p>
                            </div>
                            <div class="w-px h-10 bg-outline-variant/60 shrink-0 self-stretch"></div>
                            <div class="flex-1 min-w-0">
                                <p class="font-title-sm text-title-sm text-on-surface font-semibold text-base">${esc(depart)} → ${esc(arrivee)}</p>
                                <p class="text-body-sm text-outline mt-0.5">
                                    Bus: <span class="font-semibold text-on-surface-variant">${esc(p.numero_plaque ?? '—')}</span>
                                    &middot;
                                    ${p.places_disponibles ?? 0} places disp.
                                </p>
                            </div>
                        </div>
                        <span class="px-sm py-xs text-label-sm rounded-full font-medium shrink-0 ${statBg}">
                            ${esc(statut)}
                        </span>
                    </div>
                </div>`;
            }).join('') + '</div>';

    } catch {
        container.innerHTML = '<div class="p-6 text-center text-error text-sm font-semibold">Erreur lors du chargement des programmes à venir.</div>';
    }
}

// 1. Relier le bouton Actualiser à la fonction
document.getElementById('btn-refresh-upcoming').addEventListener('click', loadUpcomingProgrammes);

// 2. Charger les données automatiquement quand la page s'ouvre
window.addEventListener('load', loadUpcomingProgrammes);
</script>
<?= $this->endSection() ?>
