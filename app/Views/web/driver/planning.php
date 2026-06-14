<?= $this->extend($layout ?? 'web/layouts/driver') ?>

<?= $this->section('content') ?>

<?php
$user        = session()->get('user') ?? [];
$prenom      = $user['prenom'] ?? '';
$nom         = $user['nom']    ?? '';
$displayName = trim("$prenom $nom") ?: ($user['username'] ?? 'Chauffeur');
$today       = date('d/m/Y');
$dayName     = ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'][date('w')];
$programmes  = $programmes ?? [];
?>

<div class="space-y-lg">

    {{-- ── Carte identité chauffeur ── --}}
    <div class="bg-primary text-on-primary rounded-2xl p-lg shadow-md flex items-center gap-lg">
        <div class="w-14 h-14 rounded-full bg-on-primary/20 flex items-center justify-center text-on-primary font-bold text-h1 shrink-0">
            <?= esc(strtoupper(substr($displayName, 0, 1))) ?>
        </div>
        <div>
            <h2 class="font-headline-md text-headline-md font-bold leading-tight">
                <?= esc($displayName) ?>
            </h2>
            <p class="text-on-primary/70 text-body-md mt-xs">
                <?= esc($dayName) ?>, <?= esc($today) ?>
            </p>
        </div>
        <div class="ml-auto text-right hidden sm:block">
            <span class="material-symbols-outlined text-[40px] text-on-primary/30">directions_bus</span>
        </div>
    </div>

    {{-- ── Résumé du jour ── --}}
    <div class="grid grid-cols-3 gap-gutter">
        <?php
        $stats = [
            ['icon' => 'route',         'label' => 'Voyages',  'value' => count($programmes)],
            ['icon' => 'schedule',      'label' => 'Heures',   'value' => '—'],
            ['icon' => 'people',        'label' => 'Passagers','value' => '—'],
        ];
        foreach ($stats as $s): ?>
            <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-lg shadow-sm text-center">
                <span class="material-symbols-outlined text-primary text-[28px]"><?= $s['icon'] ?></span>
                <p class="text-headline-md font-bold mt-sm"><?= $s['value'] ?></p>
                <p class="text-label-sm text-outline"><?= $s['label'] ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    {{-- ── Planning du jour ── --}}
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
                    <p class="text-body-sm text-outline mt-xs">Profitez de votre journée de repos ! 🎉</p>
                </div>
            </div>
        <?php else: ?>
            <div class="divide-y divide-outline-variant/50">
                <?php foreach ($programmes as $prog): ?>
                    <div class="flex items-center gap-lg p-lg hover:bg-surface-container-low transition-colors">
                        {{-- Heure --}}
                        <div class="text-center shrink-0 w-16">
                            <p class="font-bold text-on-surface text-body-md"><?= esc($prog['heure_depart'] ?? '—') ?></p>
                            <p class="text-label-sm text-outline">Départ</p>
                        </div>
                        <div class="w-px h-10 bg-outline-variant"></div>
                        {{-- Trajet --}}
                        <div class="flex-1 min-w-0">
                            <p class="font-label-lg text-on-surface truncate">
                                <?= esc($prog['trajet'] ?? '—') ?>
                            </p>
                            <p class="text-body-sm text-outline">
                                Bus : <?= esc($prog['bus'] ?? '—') ?>
                                &nbsp;·&nbsp;
                                <?= esc($prog['nb_passagers'] ?? 0) ?> passagers
                            </p>
                        </div>
                        {{-- Statut --}}
                        <span class="px-sm py-xs bg-primary-fixed text-on-primary-fixed-variant text-label-sm rounded-full shrink-0">
                            <?= esc($prog['statut'] ?? 'Planifié') ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    {{-- ── Note informative ── --}}
    <div class="flex items-start gap-md p-lg bg-secondary-container/40 border border-secondary/20 rounded-xl text-on-surface-variant">
        <span class="material-symbols-outlined text-secondary shrink-0">info</span>
        <p class="text-body-sm">
            Pour toute modification de planning ou problème technique, contactez l'administrateur ou le réceptionniste de permanence.
        </p>
    </div>

</div>

<?= $this->endSection() ?>
