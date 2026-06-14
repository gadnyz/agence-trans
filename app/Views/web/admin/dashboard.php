<?= $this->extend($layout ?? 'web/layouts/admin') ?>

<?= $this->section('content') ?>

<?php
$user        = session()->get('user') ?? [];
$prenom      = $user['prenom'] ?? '';
$displayName = trim($prenom ?: ($user['username'] ?? 'Admin'));
$dateAujourd = date('d/m/Y');
$summary     = $summary ?? [];
?>

<div class="space-y-lg">

    {{-- ── En-tête ── --}}
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-end gap-sm">
        <div>
            <h2 class="font-headline-lg text-headline-lg text-on-surface">
                Bonjour, <?= esc($displayName) ?> 👋
            </h2>
            <p class="text-body-lg text-outline">Tableau de bord — <?= esc($dateAujourd) ?></p>
        </div>
    </div>

    {{-- ── KPIs ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-gutter">

        <?php
        $kpis = [
            [
                'icon'    => 'book_online',
                'label'   => 'Réservations',
                'value'   => number_format($summary['reservations'] ?? 0),
                'badge'   => 'Aujourd\'hui',
                'bg'      => 'bg-primary-fixed text-on-primary-fixed',
                'badgeCls'=> 'bg-primary-fixed text-on-primary-fixed-variant',
            ],
            [
                'icon'    => 'payments',
                'label'   => 'Encaissements',
                'value'   => number_format($summary['encaissements'] ?? 0) . ' FC',
                'badge'   => 'Ce mois',
                'bg'      => 'bg-secondary-fixed text-on-secondary-fixed',
                'badgeCls'=> 'bg-secondary-fixed text-on-secondary-fixed-variant',
            ],
            [
                'icon'    => 'directions_bus',
                'label'   => 'Bus actifs',
                'value'   => $summary['bus'] ?? 0,
                'badge'   => 'Fleet',
                'bg'      => 'bg-tertiary-fixed text-on-tertiary-fixed',
                'badgeCls'=> 'bg-tertiary-fixed text-on-tertiary-fixed-variant',
            ],
            [
                'icon'    => 'groups',
                'label'   => 'Clients',
                'value'   => number_format($summary['clients'] ?? 0),
                'badge'   => '+' . ($summary['nouveaux_clients'] ?? 0) . ' nouveaux',
                'bg'      => 'bg-surface-container text-on-surface',
                'badgeCls'=> 'bg-surface-container-high text-on-surface-variant',
            ],
        ];
        foreach ($kpis as $kpi): ?>
            <div class="bg-surface-container-lowest p-lg rounded-xl border border-outline-variant shadow-sm hover:shadow-md transition-shadow">
                <div class="flex justify-between items-start mb-md">
                    <div class="p-sm <?= $kpi['bg'] ?> rounded-lg">
                        <span class="material-symbols-outlined"><?= $kpi['icon'] ?></span>
                    </div>
                    <span class="text-label-sm font-bold <?= $kpi['badgeCls'] ?> px-sm py-xs rounded">
                        <?= $kpi['badge'] ?>
                    </span>
                </div>
                <h3 class="text-label-lg text-outline"><?= $kpi['label'] ?></h3>
                <p class="text-headline-md font-bold mt-xs"><?= $kpi['value'] ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    {{-- ── Réservations récentes ── --}}
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-lg shadow-sm">
        <div class="flex justify-between items-center mb-lg">
            <h3 class="font-title-md text-title-md text-on-surface">Réservations récentes</h3>
            <a href="<?= base_url('admin/reservation') ?>" class="text-primary font-label-md hover:underline">
                Voir tout
            </a>
        </div>

        <?php if (empty($details['reservations'])): ?>
            <div class="flex flex-col items-center justify-center py-xl text-outline gap-sm">
                <span class="material-symbols-outlined text-[48px] text-outline-variant">inbox</span>
                <p class="text-body-md">Aucune réservation pour le moment.</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="border-b border-outline-variant">
                        <tr>
                            <th class="py-sm px-md text-label-md text-outline">Ref.</th>
                            <th class="py-sm px-md text-label-md text-outline">Client</th>
                            <th class="py-sm px-md text-label-md text-outline">Trajet</th>
                            <th class="py-sm px-md text-label-md text-outline text-right">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/50">
                        <?php foreach ($details['reservations'] as $res): ?>
                            <tr class="hover:bg-surface-container-low transition-colors">
                                <td class="py-md px-md text-label-lg font-bold"><?= esc($res['ref'] ?? '—') ?></td>
                                <td class="py-md px-md text-body-md"><?= esc($res['client'] ?? '—') ?></td>
                                <td class="py-md px-md text-body-md"><?= esc($res['trajet'] ?? '—') ?></td>
                                <td class="py-md px-md text-right">
                                    <span class="px-sm py-xs bg-primary-fixed text-on-primary-fixed-variant text-label-sm rounded-full">
                                        <?= esc($res['statut'] ?? '—') ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

</div>

<?= $this->endSection() ?>
