<?= $this->extend('web/layouts/app') ?>

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
$money = static fn ($value): string => number_format((float) ($value ?? 0), 2, ',', ' ');
$number = static fn ($value): string => number_format((float) ($value ?? 0), 0, ',', ' ');
$selected = static fn ($left, $right): string => (string) $left === (string) $right ? 'selected' : '';
$driverName = static fn (array $driver): string => trim(($driver['nom'] ?? '') . ' ' . ($driver['postnom'] ?? '') . ' ' . ($driver['prenom'] ?? '')) ?: '-';
$routeLabel = static fn (array $route): string => trim(($route['lieu_depart'] ?? '-') . ' - ' . ($route['lieu_arrivee'] ?? '-') . ' | ' . substr((string) ($route['heure_depart'] ?? ''), 0, 5));
$programmeLabel = static fn (array $programme): string => '#' . ($programme['id_programme'] ?? '-') . ' | ' . ($programme['date_programme'] ?? '-') . ' | ' . ($programme['lieu_depart'] ?? '-') . ' - ' . ($programme['lieu_arrivee'] ?? '-') . ' | ' . substr((string) ($programme['heure_depart'] ?? ''), 0, 5) . ' | ' . ($programme['numero_plaque'] ?? '-');
$periodLabel = 'Du ' . $filters['date_debut'] . ' au ' . $filters['date_fin'];
$pageTitle = $pageTitle ?? 'Rapports';
$reportActionUrl = $reportActionUrl ?? base_url('rapports');
?>

<div
    class="no-print fixed top-[48px] left-0 w-full h-[56px] z-40 flex items-center px-gutter justify-between bg-surface-container-low border-b border-outline-variant">
    <div class="flex items-center gap-md h-full">
        <h2 class="font-h2 text-h2 text-on-surface"><?= esc($pageTitle) ?></h2>
    </div>
    <button class="h-10 px-md bg-white border border-outline-variant text-secondary rounded font-medium hover:bg-surface-container-highest transition-colors flex items-center gap-xs" type="button" onclick="window.print()">
        <span class="material-symbols-outlined text-[20px]">print</span>
        Imprimer
    </button>
</div>

<main class="pt-[128px] px-gutter pb-xl max-w-[1900px] mx-auto">
    <form class="no-print report-card bg-white border border-outline-variant rounded custom-shadow p-md mb-gutter" method="get" action="<?= esc($reportActionUrl) ?>">
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-md">
            <label class="block">
                <span class="block text-body-sm font-medium text-on-surface-variant mb-xs">Date début</span>
                <input class="w-full rounded border-outline-variant text-body-md" type="date" name="date_debut" value="<?= esc($filters['date_debut']) ?>">
            </label>
            <label class="block">
                <span class="block text-body-sm font-medium text-on-surface-variant mb-xs">Date fin</span>
                <input class="w-full rounded border-outline-variant text-body-md" type="date" name="date_fin" value="<?= esc($filters['date_fin']) ?>">
            </label>
            <label class="block">
                <span class="block text-body-sm font-medium text-on-surface-variant mb-xs">Base période</span>
                <select class="w-full rounded border-outline-variant text-body-md" name="date_source">
                    <option value="reservation" <?= $selected($filters['date_source'], 'reservation') ?>>Date réservation</option>
                    <option value="programme" <?= $selected($filters['date_source'], 'programme') ?>>Date course</option>
                </select>
            </label>
            <label class="block">
                <span class="block text-body-sm font-medium text-on-surface-variant mb-xs">Mode paiement</span>
                <select class="w-full rounded border-outline-variant text-body-md" name="id_mode_paiement">
                    <option value="">Tous</option>
                    <?php foreach ($options['modesPaiement'] as $mode): ?>
                        <option value="<?= esc($mode['id_mode_paiement']) ?>" <?= $selected($filters['id_mode_paiement'], $mode['id_mode_paiement']) ?>><?= esc($mode['libelle']) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label class="block">
                <span class="block text-body-sm font-medium text-on-surface-variant mb-xs">Client</span>
                <select class="w-full rounded border-outline-variant text-body-md" name="id_client">
                    <option value="">Tous</option>
                    <?php foreach ($options['clients'] as $client): ?>
                        <option value="<?= esc($client['id_client']) ?>" <?= $selected($filters['id_client'], $client['id_client']) ?>><?= esc(($client['nom'] ?? '-') . ' | ' . ($client['telephone'] ?? '-')) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label class="block">
                <span class="block text-body-sm font-medium text-on-surface-variant mb-xs">Conducteur</span>
                <select class="w-full rounded border-outline-variant text-body-md" name="id_conducteur">
                    <option value="">Tous</option>
                    <?php foreach ($options['conducteurs'] as $driver): ?>
                        <option value="<?= esc($driver['id_conducteur']) ?>" <?= $selected($filters['id_conducteur'], $driver['id_conducteur']) ?>><?= esc($driverName($driver)) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label class="block">
                <span class="block text-body-sm font-medium text-on-surface-variant mb-xs">Trajet</span>
                <select class="w-full rounded border-outline-variant text-body-md" name="id_trajet">
                    <option value="">Tous</option>
                    <?php foreach ($options['trajets'] as $route): ?>
                        <option value="<?= esc($route['id_trajet']) ?>" <?= $selected($filters['id_trajet'], $route['id_trajet']) ?>><?= esc($routeLabel($route)) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label class="block">
                <span class="block text-body-sm font-medium text-on-surface-variant mb-xs">Course</span>
                <select class="w-full rounded border-outline-variant text-body-md" name="id_programme">
                    <option value="">Toutes</option>
                    <?php foreach ($options['programmes'] as $programme): ?>
                        <option value="<?= esc($programme['id_programme']) ?>" <?= $selected($filters['id_programme'], $programme['id_programme']) ?>><?= esc($programmeLabel($programme)) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label class="block">
                <span class="block text-body-sm font-medium text-on-surface-variant mb-xs">Bus</span>
                <select class="w-full rounded border-outline-variant text-body-md" name="id_bus">
                    <option value="">Tous</option>
                    <?php foreach ($options['bus'] as $bus): ?>
                        <option value="<?= esc($bus['id_bus']) ?>" <?= $selected($filters['id_bus'], $bus['id_bus']) ?>><?= esc($bus['numero_plaque']) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <div class="flex items-end gap-sm">
                <button class="h-10 px-md bg-[#2563EB] text-white rounded font-medium hover:opacity-90" type="submit">Appliquer</button>
                <a class="h-10 px-md bg-white border border-outline-variant text-secondary rounded font-medium flex items-center" href="<?= esc($reportActionUrl) ?>">Réinitialiser</a>
            </div>
        </div>
    </form>

    <section class="mb-gutter">
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-sm mb-md">
            <div>
                <h1 class="text-h1 font-black text-on-surface">Rapport des réservations</h1>
                <p class="text-body-md text-on-surface-variant"><?= esc($periodLabel) ?> · <?= $filters['date_source'] === 'programme' ? 'base date course' : 'base date réservation' ?></p>
            </div>
            <div class="text-body-sm text-on-surface-variant">
                Généré le <?= esc(date('Y-m-d H:i')) ?>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-6 gap-gutter">
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
            <div class="report-card bg-white border border-outline-variant rounded custom-shadow p-md">
                <span class="block text-label-caps text-on-surface-variant">Montant réservé</span>
                <strong class="block text-h2 text-primary mt-xs"><?= esc($money($summary['montant_reservations'])) ?></strong>
                <span class="text-body-sm text-on-surface-variant">valeur brute</span>
            </div>
        </div>
    </section>

    <section class="report-card bg-white border border-outline-variant rounded custom-shadow overflow-hidden">
        <div class="px-md py-sm bg-surface-container-lowest border-b border-outline-variant">
            <h3 class="font-h2 text-h2">Détail des réservations</h3>
            <p class="text-body-sm text-on-surface-variant">Liste imprimable des réservations correspondant aux filtres</p>
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
                        <th class="text-right">Réservé</th>
                        <th class="text-right">Payé</th>
                        <th>Paiement</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($details['reservations'] === []): ?>
                        <tr><td colspan="10" class="text-on-surface-variant">Aucune réservation pour les filtres sélectionnés.</td></tr>
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
                            <td class="text-right"><?= esc($money($row['montant_final'])) ?></td>
                            <td class="text-right font-medium"><?= esc($money($row['montant_paye'])) ?></td>
                            <td><?= esc($row['modes_paiement'] ?: '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>
<?= $this->endSection() ?>
