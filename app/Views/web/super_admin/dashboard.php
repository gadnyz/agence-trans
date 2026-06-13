<?= $this->extend('web/layouts/super_admin') ?>

<?= $this->section('styles') ?>
<style>
    .report-table {
        border-collapse: collapse;
        width: 100%;
    }

    .report-table th,
    .report-table td {
        border-bottom: 1px solid #E5E7EB;
        padding: 0.55rem 0.65rem;
        vertical-align: top;
        white-space: nowrap;
    }

    .report-table th {
        background: #F8FAFC;
        color: #475569;
        font-weight: 700;
        text-align: left;
    }

    .report-action {
        height: 2.25rem;
        min-width: 2.25rem;
    }

    @media print {
        @page {
            size: A4 landscape;
            margin: 10mm;
        }

        header,
        aside,
        .no-print {
            display: none !important;
        }

        body {
            background: #FFFFFF !important;
        }

        main {
            max-width: none !important;
            padding: 0 !important;
        }

        .report-card {
            border: 1px solid #CBD5E1 !important;
            box-shadow: none !important;
        }

        .report-table {
            font-size: 10px;
        }

        .report-table th,
        .report-table td {
            padding: 4px 5px;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php 
$filters = $filters ?? [
    'date_debut' => date('Y-m-01'),
    'date_fin'   => date('Y-m-d'),
    'id_trajet'  => '',
    'id_agent'   => ''
];

$pagination = $pagination ?? [
    'page' => 1, 'per_page' => 10, 'total' => 0, 'total_pages' => 1, 'from' => 0, 'to' => 0
];

$summary = $summary ?? [
    'encaissements' => 0, 'paiements' => 0, 'reservations' => 0, 'sieges' => 0,
    'clients' => 0, 'nouveaux_clients' => 0, 'courses' => 0, 'conducteurs' => 0, 'bus' => 0
];

$options = $options ?? ['trajets' => [], 'agents' => []];
$details = $details ?? ['reservations' => []];

$money = static fn ($value): string => number_format((float) ($value ?? 0), 2, ',', ' ');
$number = static fn ($value): string => number_format((float) ($value ?? 0), 0, ',', ' ');
$selected = static fn ($left, $right): string => (string) $left === (string) $right ? 'selected' : '';
$routeLabel = static fn (array $route): string => trim(($route['lieu_depart'] ?? '-') . ' - ' . ($route['lieu_arrivee'] ?? '-') . ' | ' . substr((string) ($route['heure_depart'] ?? ''), 0, 5));

$agentLabel = static function (array $agent): string {
    $name = trim(($agent['nom'] ?? '') . ' ' . ($agent['postnom'] ?? '') . ' ' . ($agent['prenom'] ?? ''));
    $username = (string) ($agent['username'] ?? '-');
    return $name !== '' ? $name . ' | ' . $username : $username;
};

$periodLabel = 'Du ' . $filters['date_debut'] . ' au ' . $filters['date_fin'];
$pageTitle = $pageTitle ?? 'Tableau de bord';
$reportActionUrl = $reportActionUrl ?? base_url('dashboard');
$baseParams = [
    'date_debut' => $filters['date_debut'],
    'date_fin'   => $filters['date_fin'],
    'id_trajet'  => $filters['id_trajet'] ?: null,
    'id_agent'   => $filters['id_agent'] ?: null,
    'per_page'   => $pagination['per_page'],
];

$cleanParams = static fn (array $params): array => array_filter($params, static fn ($value): bool => $value !== null && $value !== '');
$pageUrl = static fn (int $page): string => $reportActionUrl . '?' . http_build_query($cleanParams(array_merge($baseParams, ['page' => $page])));
$pageStart = max(1, (int) $pagination['page'] - 2);
$pageEnd = min((int) $pagination['total_pages'], (int) $pagination['page'] + 2);
?>

<div class="no-print fixed top-[48px] left-0 w-full z-40 bg-surface-container-low border-b border-outline-variant">
    <div class="px-gutter py-xs flex flex-col gap-xs lg:flex-row lg:items-center lg:justify-between">
        <div class="flex items-center gap-sm min-w-0">
            <h2 class="font-h2 text-h2 text-on-surface truncate"><?= esc($pageTitle) ?></h2>
            <span class="hidden sm:inline text-body-sm text-on-surface-variant"><?= esc($number($pagination['total'])) ?> ligne(s)</span>
        </div>

        <form class="flex items-center gap-xs overflow-x-auto pb-xs lg:pb-0" method="get" action="<?= esc($reportActionUrl) ?>">
            <input type="hidden" name="page" value="1">
            <input type="hidden" name="per_page" value="<?= esc($pagination['per_page']) ?>">

            <input
                class="h-9 min-w-[132px] rounded border-outline-variant text-body-sm"
                type="date"
                name="date_debut"
                value="<?= esc($filters['date_debut']) ?>"
                aria-label="Date début"
                title="Date début"
            >
            <input
                class="h-9 min-w-[132px] rounded border-outline-variant text-body-sm"
                type="date"
                name="date_fin"
                value="<?= esc($filters['date_fin']) ?>"
                aria-label="Date fin"
                title="Date fin"
            >
            <select
                class="h-9 min-w-[190px] max-w-[240px] rounded border-outline-variant text-body-sm"
                name="id_trajet"
                aria-label="Trajet"
                title="Trajet"
            >
                <option value="">Tous les trajets</option>
                <?php foreach ($options['trajets'] as $route): ?>
                    <option value="<?= esc($route['id_trajet']) ?>" <?= $selected($filters['id_trajet'], $route['id_trajet']) ?>><?= esc($routeLabel($route)) ?></option>
                <?php endforeach; ?>
            </select>
            <select
                class="h-9 min-w-[160px] max-w-[220px] rounded border-outline-variant text-body-sm"
                name="id_agent"
                aria-label="Agent"
                title="Agent"
            >
                <option value="">Tous les agents</option>
                <?php foreach ($options['agents'] as $agent): ?>
                    <option value="<?= esc($agent['id_utilisateur']) ?>" <?= $selected($filters['id_agent'], $agent['id_utilisateur']) ?>><?= esc($agentLabel($agent)) ?></option>
                <?php endforeach; ?>
            </select>

            <button class="report-action rounded bg-[#2563EB] text-white flex items-center justify-center hover:opacity-90" type="submit" title="Appliquer" aria-label="Appliquer">
                <span class="material-symbols-outlined text-[20px]">filter_alt</span>
            </button>
            <a class="report-action rounded bg-white border border-outline-variant text-secondary flex items-center justify-center hover:bg-surface-container-highest" href="<?= esc($reportActionUrl) ?>" title="Réinitialiser" aria-label="Réinitialiser">
                <span class="material-symbols-outlined text-[20px]">restart_alt</span>
            </a>
            <button class="h-9 px-sm rounded bg-white border border-outline-variant text-secondary flex items-center gap-xs hover:bg-surface-container-highest" type="button" onclick="window.print()">
                <span class="material-symbols-outlined text-[20px]">print</span>
                <span class="hidden sm:inline">Imprimer</span>
            </button>
        </form>
    </div>
</div>

<main class="pt-[160px] lg:pt-[128px] px-gutter pb-xl max-w-[1900px] mx-auto">
    <section class="mb-gutter">
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-sm mb-md">
            <div>
                <h1 class="text-h1 font-black text-on-surface">Rapport des réservations</h1>
                <p class="text-body-md text-on-surface-variant"><?= esc($periodLabel) ?></p>
            </div>
            <div class="text-body-sm text-on-surface-variant">
                Généré le <?= esc(date('Y-m-d H:i')) ?>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-gutter">
            <div class="report-card bg-white border border-outline-variant rounded custom-shadow p-md">
                <span class="block text-label-caps text-on-surface-variant">Encaissements</span>
                <strong class="block text-h2 text-primary mt-xs"><?= esc($money($summary['encaissements'])) ?></strong>
                <span class="text-body-sm text-on-surface-variant"><?= esc($number($summary['paiements'])) ?> paiement(s)</span>
            </div>
            <div class="report-card bg-white border border-outline-variant rounded custom-shadow p-md">
                <span class="block text-label-caps text-on-surface-variant">Réservations</span>
                <strong class="block text-h2 text-primary mt-xs"><?= esc($number($summary['reservations'])) ?></strong>
                <span class="text-body-sm text-on-surface-variant"><?= esc($number($summary['sieges'])) ?> siège(s)</span>
            </div>
            <div class="report-card bg-white border border-outline-variant rounded custom-shadow p-md">
                <span class="block text-label-caps text-on-surface-variant">Clients</span>
                <strong class="block text-h2 text-primary mt-xs"><?= esc($number($summary['clients'])) ?></strong>
                <span class="text-body-sm text-on-surface-variant"><?= esc($number($summary['nouveaux_clients'])) ?> nouveau(x)</span>
            </div>
            <div class="report-card bg-white border border-outline-variant rounded custom-shadow p-md">
                <span class="block text-label-caps text-on-surface-variant">Courses</span>
                <strong class="block text-h2 text-primary mt-xs"><?= esc($number($summary['courses'])) ?></strong>
                <span class="text-body-sm text-on-surface-variant"><?= esc($number($summary['conducteurs'])) ?> conducteur(s)</span>
            </div>
            <div class="report-card bg-white border border-outline-variant rounded custom-shadow p-md">
                <span class="block text-label-caps text-on-surface-variant">Bus</span>
                <strong class="block text-h2 text-primary mt-xs"><?= esc($number($summary['bus'])) ?></strong>
                <span class="text-body-sm text-on-surface-variant">utilisé(s)</span>
            </div>
        </div>
    </section>

    <section class="report-card bg-white border border-outline-variant rounded custom-shadow overflow-hidden">
        <div class="px-md py-sm bg-surface-container-lowest border-b border-outline-variant flex flex-col gap-sm md:flex-row md:items-center md:justify-between">
            <div>
                <h3 class="font-h2 text-h2">Détail des réservations</h3>
                <p class="text-body-sm text-on-surface-variant">
                    <?= esc($number($pagination['from'])) ?>-<?= esc($number($pagination['to'])) ?> sur <?= esc($number($pagination['total'])) ?> · Page <?= esc($pagination['page']) ?>/<?= esc($pagination['total_pages']) ?>
                </p>
            </div>

            <form class="no-print flex items-center gap-xs" method="get" action="<?= esc($reportActionUrl) ?>">
                <input type="hidden" name="date_debut" value="<?= esc($filters['date_debut']) ?>">
                <input type="hidden" name="date_fin" value="<?= esc($filters['date_fin']) ?>">
                <?php if ($filters['id_trajet']): ?>
                    <input type="hidden" name="id_trajet" value="<?= esc($filters['id_trajet']) ?>">
                <?php endif; ?>
                <?php if ($filters['id_agent']): ?>
                    <input type="hidden" name="id_agent" value="<?= esc($filters['id_agent']) ?>">
                <?php endif; ?>
                <input type="hidden" name="page" value="1">
                <select class="h-9 rounded border-outline-variant text-body-sm" name="per_page" onchange="this.form.submit()" aria-label="Lignes par page">
                    <?php foreach ([10, 25, 50, 100] as $size): ?>
                        <option value="<?= esc($size) ?>" <?= $selected($pagination['per_page'], $size) ?>><?= esc($size) ?>/page</option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="report-table text-body-sm">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Date</th>
                        <th>Course</th>
                        <th>Client</th>
                        <th>Agent</th>
                        <th>Statut</th>
                        <th class="text-right">Sièges</th>
                        <th class="text-right">Payé</th>
                        <th>Paiement</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($details['reservations'] === []): ?>
                        <tr><td colspan="9" class="text-on-surface-variant">Aucune réservation pour les filtres sélectionnés.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($details['reservations'] as $row): ?>
                        <tr>
                            <td class="font-medium"><?= esc($row['reference_reservation']) ?></td>
                            <td><?= esc($row['date_reservation']) ?></td>
                            <td>
                                #<?= esc($row['id_programme']) ?> · <?= esc($row['date_programme']) ?> · <?= esc(substr((string) $row['heure_depart'], 0, 5)) ?>
                                <span class="block text-on-surface-variant"><?= esc($row['trajet']) ?> · <?= esc($row['numero_plaque']) ?></span>
                            </td>
                            <td><?= esc($row['client'] ?: '-') ?><span class="block text-on-surface-variant"><?= esc($row['telephone'] ?: '-') ?></span></td>
                            <td><?= esc($row['agent'] ?: '-') ?></td>
                            <td><?= esc($row['statut_reservation']) ?></td>
                            <td class="text-right"><?= esc($number($row['nombre_places'])) ?></td>
                            <td class="text-right font-medium"><?= esc($money($row['montant_paye'])) ?></td>
                            <td><?= esc($row['modes_paiement'] ?: '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <nav class="no-print px-md py-sm border-t border-outline-variant flex flex-col gap-sm sm:flex-row sm:items-center sm:justify-between" aria-label="Pagination">
            <div class="text-body-sm text-on-surface-variant">
                <?= esc($number($pagination['from'])) ?>-<?= esc($number($pagination['to'])) ?> sur <?= esc($number($pagination['total'])) ?>
            </div>
            <div class="flex items-center gap-xs">
                <?php if ($pagination['page'] > 1): ?>
                    <a class="h-9 px-sm rounded border border-outline-variant bg-white text-secondary flex items-center gap-xs" href="<?= esc($pageUrl($pagination['page'] - 1)) ?>">
                        <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                        Préc.
                    </a>
                <?php else: ?>
                    <span class="h-9 px-sm rounded border border-outline-variant text-on-surface-variant flex items-center gap-xs opacity-50">
                        <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                        Préc.
                    </span>
                <?php endif; ?>

                <div class="hidden sm:flex items-center gap-xs">
                    <?php for ($page = $pageStart; $page <= $pageEnd; $page++): ?>
                        <?php if ($page === (int) $pagination['page']): ?>
                            <span class="h-9 min-w-9 px-sm rounded bg-[#2563EB] text-white flex items-center justify-center"><?= esc($page) ?></span>
                        <?php else: ?>
                            <a class="h-9 min-w-9 px-sm rounded border border-outline-variant bg-white text-secondary flex items-center justify-center" href="<?= esc($pageUrl($page)) ?>"><?= esc($page) ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>

                <span class="sm:hidden h-9 px-sm rounded border border-outline-variant bg-white flex items-center">
                    <?= esc($pagination['page']) ?>/<?= esc($pagination['total_pages']) ?>
                </span>

                <?php if ($pagination['page'] < $pagination['total_pages']): ?>
                    <a class="h-9 px-sm rounded border border-outline-variant bg-white text-secondary flex items-center gap-xs" href="<?= esc($pageUrl($pagination['page'] + 1)) ?>">
                        Suiv.
                        <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                    </a>
                <?php else: ?>
                    <span class="h-9 px-sm rounded border border-outline-variant text-on-surface-variant flex items-center gap-xs opacity-50">
                        Suiv.
                        <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                    </span>
                <?php endif; ?>
            </div>
        </nav>
    </section>
</main>
<?= $this->endSection() ?>
