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

<div class="space-y-lg max-w-2xl mx-auto">

    <!-- ── Carte profil chauffeur ── -->
    <div class="bg-primary text-on-primary rounded-2xl p-lg shadow-md">
        <div class="flex items-center gap-lg">
            <div class="w-16 h-16 rounded-full bg-on-primary/20 flex items-center justify-center font-bold text-[24px] text-on-primary shrink-0 border-2 border-on-primary/30">
                <?= esc($initials ?: '?') ?>
            </div>
            <div class="flex-1 min-w-0">
                <h2 class="font-headline-md text-headline-md font-bold leading-tight truncate">
                    <?= esc($displayName) ?>
                </h2>
                <p class="text-on-primary/70 text-body-md mt-xs">
                    <span class="material-symbols-outlined text-[14px] align-text-bottom">event</span>
                    <?= esc($dayName) ?>, <?= esc($today) ?>
                </p>
                <div class="flex items-center gap-xs mt-sm">
                    <span class="inline-flex items-center gap-xs px-sm py-xs bg-on-primary/20 text-on-primary rounded-full text-label-sm font-medium">
                        <span class="material-symbols-outlined text-[14px]">verified</span>
                        Chauffeur actif
                    </span>
                </div>
            </div>
            <div class="hidden sm:block shrink-0">
                <span class="material-symbols-outlined text-[48px] text-on-primary/30">directions_bus</span>
            </div>
        </div>
    </div>

    <!-- ── Statistiques du jour ── -->
    <div class="grid grid-cols-3 gap-gutter">
        <?php
        $stats = [
            ['icon' => 'route',    'label' => 'Voyages du jour', 'value' => count($programmes), 'color' => 'text-primary'],
            ['icon' => 'schedule', 'label' => 'Heures de route', 'value' => '—',                'color' => 'text-secondary'],
            ['icon' => 'people',   'label' => 'Passagers',       'value' => '—',                'color' => 'text-tertiary'],
        ];
        foreach ($stats as $s): ?>
            <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-lg shadow-sm text-center">
                <span class="material-symbols-outlined <?= $s['color'] ?> text-[28px]"><?= $s['icon'] ?></span>
                <p class="text-headline-md font-bold mt-sm"><?= $s['value'] ?></p>
                <p class="text-label-sm text-outline"><?= $s['label'] ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- ── Planning du jour ── -->
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm overflow-hidden">
        <div class="flex items-center gap-md p-lg border-b border-outline-variant">
            <span class="material-symbols-outlined text-primary">event_note</span>
            <h3 class="font-title-md text-title-md text-on-surface">Mon planning — <?= esc($today) ?></h3>
        </div>

        <?php if (empty($programmes)): ?>
            <div class="flex flex-col items-center justify-center py-xl text-outline gap-md">
                <span class="material-symbols-outlined text-[56px] text-outline-variant">event_available</span>
                <div class="text-center">
                    <p class="font-medium text-on-surface">Aucun voyage planifié aujourd'hui</p>
                    <p class="text-body-sm text-outline mt-xs">Profitez de votre journée de repos !</p>
                </div>
            </div>
        <?php else: ?>
            <div class="divide-y divide-outline-variant/50">
                <?php foreach ($programmes as $prog): ?>
                    <?php
                    $statut = $prog['statut'] ?? 'Planifié';
                    $statBg = match(true) {
                        str_contains(strtolower($statut), 'ouvert')   => 'bg-green-100 text-green-700',
                        str_contains(strtolower($statut), 'planif')   => 'bg-blue-100 text-blue-700',
                        str_contains(strtolower($statut), 'suspendu') => 'bg-yellow-100 text-yellow-700',
                        str_contains(strtolower($statut), 'termin')   => 'bg-gray-100 text-gray-600',
                        default => 'bg-surface-container text-on-surface-variant',
                    };
                    ?>
                    <div class="p-lg hover:bg-surface-container-low transition-colors">
                        <div class="flex items-start justify-between gap-md">
                            <div class="flex items-start gap-lg">
                                <!-- Heure départ -->
                                <div class="text-center shrink-0 w-16 pt-xs">
                                    <p class="font-bold text-on-surface text-body-md"><?= esc(substr($prog['heure_depart'] ?? '—', 0, 5)) ?></p>
                                    <p class="text-label-sm text-outline">Départ</p>
                                </div>
                                <div class="w-px self-stretch bg-outline-variant"></div>
                                <!-- Détails trajet -->
                                <div class="flex-1 min-w-0">
                                    <p class="font-label-lg text-on-surface">
                                        <?= esc($prog['trajet'] ?? ($prog['lieu_depart'] ?? '?') . ' → ' . ($prog['lieu_arrivee'] ?? '?')) ?>
                                    </p>
                                    <div class="flex flex-wrap gap-sm mt-xs">
                                        <span class="inline-flex items-center gap-xs text-body-sm text-outline">
                                            <span class="material-symbols-outlined text-[14px]">directions_bus</span>
                                            <?= esc($prog['bus'] ?? $prog['numero_plaque'] ?? '—') ?>
                                        </span>
                                        <?php if (!empty($prog['nb_passagers'])): ?>
                                        <span class="inline-flex items-center gap-xs text-body-sm text-outline">
                                            <span class="material-symbols-outlined text-[14px]">people</span>
                                            <?= esc($prog['nb_passagers']) ?> passagers
                                        </span>
                                        <?php endif; ?>
                                        <?php if (!empty($prog['heure_arrivee'])): ?>
                                        <span class="inline-flex items-center gap-xs text-body-sm text-outline">
                                            <span class="material-symbols-outlined text-[14px]">flag</span>
                                            Arr. <?= esc(substr($prog['heure_arrivee'], 0, 5)) ?>
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

    <!-- ── Historique récent (7 derniers jours) ── -->
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm overflow-hidden" id="historique-section">
        <div class="flex items-center justify-between p-lg border-b border-outline-variant">
            <div class="flex items-center gap-md">
                <span class="material-symbols-outlined text-secondary">history</span>
                <h3 class="font-title-md text-title-md text-on-surface">Historique récent</h3>
            </div>
            <button
                id="btn-load-history"
                class="inline-flex items-center gap-xs px-md py-xs bg-surface-container border border-outline-variant rounded-lg text-label-sm hover:bg-surface-container-high transition-all"
            >
                <span class="material-symbols-outlined text-[16px]">expand_more</span>
                Charger
            </button>
        </div>

        <div id="historique-list">
            <div class="flex flex-col items-center justify-center py-xl text-outline gap-sm">
                <span class="material-symbols-outlined text-[48px] text-outline-variant">history</span>
                <p class="text-body-md">Cliquez sur "Charger" pour voir votre historique.</p>
            </div>
        </div>
    </div>

    <!-- ── Note information ── -->
    <div class="flex items-start gap-md p-lg bg-secondary-container/40 border border-secondary/20 rounded-xl text-on-surface-variant">
        <span class="material-symbols-outlined text-secondary shrink-0">info</span>
        <p class="text-body-sm">
            Pour toute modification de planning ou problème technique, contactez l'administrateur ou le réceptionniste de permanence.
        </p>
    </div>

</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
const API_TOKEN = <?= json_encode((string)($api_token ?? ''), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
const BASE_URL  = '<?= base_url() ?>';
const TODAY     = '<?= $todayIso ?>';

async function apiFetch(url, options = {}) {
    const headers = { Accept: 'application/json', Authorization: `Bearer ${API_TOKEN}`, ...(options.headers || {}) };
    return fetch(url, { ...options, headers });
}

function esc(str) {
    const d = document.createElement('div');
    d.textContent = str ?? '';
    return d.innerHTML;
}

document.getElementById('btn-load-history').addEventListener('click', async () => {
    const container = document.getElementById('historique-list');
    container.innerHTML = '<div class="flex justify-center items-center py-xl"><div class="animate-spin w-8 h-8 border-2 border-primary border-t-transparent rounded-full"></div></div>';

    try {
        // 7 derniers jours
        const dateDebut = new Date();
        dateDebut.setDate(dateDebut.getDate() - 7);
        const dateDebutStr = dateDebut.toISOString().slice(0, 10);
        const response = await apiFetch(`${BASE_URL}api/planification?per_page=30&date_debut=${dateDebutStr}&date_fin=${TODAY}&sort=date_programme`);
        const json     = await response.json();
        const items    = json.data?.items ?? [];

        const past = items.filter(p => p.date_programme < TODAY);

        if (!past.length) {
            container.innerHTML = `
                <div class="flex flex-col items-center justify-center py-xl text-outline gap-sm">
                    <span class="material-symbols-outlined text-[48px] text-outline-variant">history</span>
                    <p class="text-body-md">Aucun voyage dans les 7 derniers jours.</p>
                </div>`;
            return;
        }

        container.innerHTML = '<div class="divide-y divide-outline-variant/50">' +
            past.map(p => {
                const depart  = p.lieu_depart ?? '?';
                const arrivee = p.lieu_arrivee ?? '?';
                const heure   = (p.heure_depart ?? '').slice(0, 5);
                return `
                <div class="flex items-center gap-lg p-lg">
                    <div class="w-12 text-center shrink-0">
                        <p class="text-label-sm font-bold text-on-surface">${esc(p.date_programme ?? '')}</p>
                        <p class="text-label-sm text-outline">${esc(heure)}</p>
                    </div>
                    <div class="w-px h-10 bg-outline-variant shrink-0"></div>
                    <div class="flex-1 min-w-0">
                        <p class="font-label-lg text-on-surface truncate">${esc(depart)} → ${esc(arrivee)}</p>
                        <p class="text-body-sm text-outline">${esc(p.numero_plaque ?? '—')} · ${p.places_disponibles ?? 0} places</p>
                    </div>
                    <span class="px-sm py-xs text-label-sm rounded-full bg-surface-container text-on-surface-variant shrink-0">
                        ${esc(p.statut ?? 'Terminé')}
                    </span>
                </div>`;
            }).join('') + '</div>';

    } catch {
        container.innerHTML = '<div class="p-lg text-center text-error text-sm">Erreur lors du chargement.</div>';
    }
});
</script>
<?= $this->endSection() ?>
