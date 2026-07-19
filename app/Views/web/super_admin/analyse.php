<?= $this->extend($layout ?? 'web/layouts/super_admin') ?>

<?= $this->section('styles') ?>
<style>
    @media print {
        @page { size: A4 landscape; margin: 10mm; }
        header, aside, .no-print { display: none !important; }
        body { background: #FFFFFF !important; }
        main { max-width: none !important; padding: 0 !important; }
        .report-card { border: 1px solid #E5E7EB !important; box-shadow: none !important; }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$money       = static fn ($value): string => number_format((float) ($value ?? 0), 2, '.', ' ');
$number      = static fn ($value): string => number_format((float) ($value ?? 0), 0, '.', ' ');
$selected    = static fn ($left, $right): string => (string) $left === (string) $right ? 'selected' : '';
$routeLabel  = static fn (array $route): string => trim(($route['lieu_depart'] ?? '-') . ' - ' . ($route['lieu_arrivee'] ?? '-') . ' | ' . substr((string) ($route['heure_depart'] ?? ''), 0, 5));
$periodLabel = 'Du ' . $filters['date_debut'] . ' au ' . $filters['date_fin'];
$pageTitle   = $pageTitle ?? 'Analyse';
$analyseActionUrl = $analyseActionUrl ?? base_url('super-admin/analyse');

// Chart max value for scaling
$maxRouteMontant = 0;
foreach ($routesReport as $route) {
    $maxRouteMontant = max($maxRouteMontant, (float) ($route['montant'] ?? 0));
}
$maxRouteMontant = $maxRouteMontant ?: 1;

// Weekday demand max
$maxWeekdayReservations = 0;
foreach ($demandByWeekday as $day) {
    $maxWeekdayReservations = max($maxWeekdayReservations, (int) ($day['reservations'] ?? 0));
}
$maxWeekdayReservations = $maxWeekdayReservations ?: 1;
?>

<main class="px-4 pb-12 max-w-[1600px] mx-auto">

    <!-- ── En-tête + Filtres ── -->
    <div class="no-print w-full mb-6 rounded-2xl bg-white border border-gray-200 shadow-sm p-4">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

            <!-- Titre -->
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-blue-50 text-blue-600">
                    <span class="material-symbols-outlined text-[20px]">analytics</span>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 tracking-tight"><?= esc($pageTitle) ?></h2>
                    <p class="text-xs text-gray-500"><?= esc($periodLabel) ?></p>
                </div>
            </div>

            <!-- Filtres -->
            <div class="w-full lg:w-auto overflow-x-auto">
                <form class="flex items-center gap-2 min-w-max" method="get" action="<?= esc($analyseActionUrl) ?>">
                    <input class="h-10 px-3 rounded-xl border-gray-300 border text-sm shadow-sm focus:border-blue-500 outline-none" type="date" name="date_debut" value="<?= esc($filters['date_debut']) ?>" title="Date début">
                    <input class="h-10 px-3 rounded-xl border-gray-300 border text-sm shadow-sm focus:border-blue-500 outline-none" type="date" name="date_fin" value="<?= esc($filters['date_fin']) ?>" title="Date fin">

                    <button class="h-10 w-10 rounded-xl bg-blue-600 text-white flex items-center justify-center hover:bg-blue-700 transition-all shadow-sm" type="submit" title="Appliquer">
                        <span class="material-symbols-outlined text-[20px]">filter_alt</span>
                    </button>
                    <a class="h-10 w-10 rounded-xl bg-white border border-gray-300 text-gray-700 flex items-center justify-center hover:bg-gray-50 transition-all shadow-sm" href="<?= esc($analyseActionUrl) ?>" title="Réinitialiser">
                        <span class="material-symbols-outlined text-[20px]">restart_alt</span>
                    </a>
                    <button class="h-10 px-4 rounded-xl bg-white border border-gray-300 text-gray-700 flex items-center gap-2 hover:bg-gray-50 transition-all shadow-sm" type="button" onclick="window.print()">
                        <span class="material-symbols-outlined text-[18px]">print</span>
                        <span class="hidden sm:inline text-sm font-medium">Imprimer</span>
                    </button>
                </form>
            </div>

        </div>
    </div>

    <!-- ── KPI Summary Cards ── -->
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <div class="report-card bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
            <span class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Encaissements</span>
            <strong class="block text-xl font-bold text-gray-900 mt-2"><?= esc($money($summary['encaissements'])) ?> <span class="text-xs text-gray-500 font-normal">CDF</span></strong>
            <span class="block text-xs text-gray-500 mt-1"><?= esc($number($summary['paiements'])) ?> paiement(s)</span>
        </div>
        <div class="report-card bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
            <span class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Réservations</span>
            <strong class="block text-xl font-bold text-gray-900 mt-2"><?= esc($number($summary['reservations'])) ?></strong>
            <span class="block text-xs text-gray-500 mt-1"><?= esc($number($summary['sieges'])) ?> siège(s)</span>
        </div>
        <div class="report-card bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
            <span class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Clients</span>
            <strong class="block text-xl font-bold text-gray-900 mt-2"><?= esc($number($summary['clients'])) ?></strong>
            <span class="block text-xs text-gray-500 mt-1"><?= esc($number($summary['nouveaux_clients'])) ?> nouveau(x)</span>
        </div>
        <div class="report-card bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
            <span class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Courses</span>
            <strong class="block text-xl font-bold text-gray-900 mt-2"><?= esc($number($summary['courses'])) ?></strong>
            <span class="block text-xs text-gray-500 mt-1"><?= esc($number($summary['conducteurs'])) ?> conducteur(s)</span>
        </div>
        <div class="report-card bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
            <span class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Bus</span>
            <strong class="block text-xl font-bold text-gray-900 mt-2"><?= esc($number($summary['bus'])) ?></strong>
            <span class="block text-xs text-gray-500 mt-1">utilisé(s)</span>
        </div>
    </section>

    <!-- ── Bar Chart: Recettes par trajet ── -->
    <section class="report-card bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
            <div>
                <h3 class="text-base font-semibold text-gray-900">Recettes par trajet</h3>
                <p class="text-xs text-gray-500 mt-0.5">Montant total des réservations par destination</p>
            </div>
            <div class="flex items-center gap-2">
                <div class="flex items-center gap-1.5">
                    <div class="w-2.5 h-2.5 bg-blue-600 rounded-full"></div>
                    <span class="text-xs text-gray-500">Revenu</span>
                </div>
            </div>
        </div>
        <div class="p-6">
            <?php if (empty($routesReport)): ?>
                <div class="flex flex-col items-center justify-center py-12 text-gray-400">
                    <span class="material-symbols-outlined text-[36px] mb-2">bar_chart</span>
                    <p class="text-sm font-medium">Aucune donnée pour la période sélectionnée.</p>
                </div>
            <?php else: ?>
                <div class="h-64 flex items-end gap-3 border-b border-gray-100 pb-2 pt-4">
                    <?php foreach ($routesReport as $i => $route): ?>
                        <?php
                        $montant = (float) ($route['montant'] ?? 0);
                        $heightPercent = ($montant / $maxRouteMontant) * 100;
                        $opacity = max(40, min(100, 40 + ($heightPercent * 0.6)));
                        ?>
                        <div class="flex-1 flex flex-col items-center gap-1.5 min-w-0">
                            <div
                                class="w-full bg-blue-600 rounded-t-lg relative group cursor-pointer transition-all hover:bg-blue-700"
                                style="height: <?= round($heightPercent) ?>%; opacity: <?= $opacity / 100 ?>;"
                            >
                                <div class="absolute -top-9 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-[10px] px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-10 shadow-lg">
                                    <?= esc($money($montant)) ?> $
                                </div>
                            </div>
                            <span class="text-[10px] font-semibold text-gray-500 text-center leading-tight truncate w-full" title="<?= esc($route['trajet']) ?>">
                                <?= esc($route['trajet']) ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- ── Bar Chart: Demande par jour de semaine ── -->
    <section class="report-card bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
            <div>
                <h3 class="text-base font-semibold text-gray-900">Demande par jour de la semaine</h3>
                <p class="text-xs text-gray-500 mt-0.5">Nombre de réservations par jour</p>
            </div>
            <div class="flex items-center gap-2">
                <div class="flex items-center gap-1.5">
                    <div class="w-2.5 h-2.5 bg-emerald-500 rounded-full"></div>
                    <span class="text-xs text-gray-500">Réservations</span>
                </div>
            </div>
        </div>
        <div class="p-6">
            <?php if (empty($demandByWeekday)): ?>
                <div class="flex flex-col items-center justify-center py-12 text-gray-400">
                    <span class="material-symbols-outlined text-[36px] mb-2">calendar_today</span>
                    <p class="text-sm font-medium">Aucune donnée pour la période sélectionnée.</p>
                </div>
            <?php else: ?>
                <div class="h-48 flex items-end gap-4 border-b border-gray-100 pb-2 pt-4">
                    <?php foreach ($demandByWeekday as $day): ?>
                        <?php
                        $reservations = (int) ($day['reservations'] ?? 0);
                        $heightPercent = ($reservations / $maxWeekdayReservations) * 100;
                        ?>
                        <div class="flex-1 flex flex-col items-center gap-1.5">
                            <div
                                class="w-full bg-emerald-500 rounded-t-lg relative group cursor-pointer transition-all hover:bg-emerald-600"
                                style="height: <?= max(4, round($heightPercent)) ?>%;"
                            >
                                <div class="absolute -top-9 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-[10px] px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-10 shadow-lg">
                                    <?= esc($number($reservations)) ?> rés. · <?= esc($number($day['sieges'] ?? 0)) ?> sièges
                                </div>
                            </div>
                            <span class="text-[10px] font-semibold text-gray-500 text-center"><?= esc($day['jour'] ?? '') ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- ── Data Tables ── -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-6">

        <!-- Trajets Table -->
        <section class="report-card bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-900">Détail par trajet</h3>
                <span class="text-xs font-medium text-gray-500 bg-white px-2 py-1 rounded-md border border-gray-200">
                    <?= count($routesReport) ?> trajet(s)
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-gray-600 uppercase text-[11px] font-semibold bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4">Trajet</th>
                            <th class="px-6 py-4 text-right">Courses</th>
                            <th class="px-6 py-4 text-right">Rés.</th>
                            <th class="px-6 py-4 text-right">Sièges</th>
                            <th class="px-6 py-4 text-right">Montant</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if (empty($routesReport)): ?>
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-400">
                                    <span class="material-symbols-outlined text-[24px]">inbox</span>
                                    <p class="text-sm mt-1">Aucune donnée</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                        <?php foreach ($routesReport as $route): ?>
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 font-medium text-gray-900"><?= esc($route['trajet']) ?></td>
                                <td class="px-6 py-4 text-right text-gray-600"><?= esc($number($route['courses'])) ?></td>
                                <td class="px-6 py-4 text-right text-gray-600"><?= esc($number($route['reservations'])) ?></td>
                                <td class="px-6 py-4 text-right text-gray-600"><?= esc($number($route['sieges'])) ?></td>
                                <td class="px-6 py-4 text-right font-semibold text-gray-900"><?= esc($money($route['montant'])) ?> $</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Chauffeurs Table -->
        <section class="report-card bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-900">Performance chauffeurs</h3>
                <span class="text-xs font-medium text-gray-500 bg-white px-2 py-1 rounded-md border border-gray-200">
                    <?= count($driversReport) ?> conducteur(s)
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-gray-600 uppercase text-[11px] font-semibold bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4">Conducteur</th>
                            <th class="px-6 py-4 text-right">Courses</th>
                            <th class="px-6 py-4 text-right">Rés.</th>
                            <th class="px-6 py-4 text-right">Sièges</th>
                            <th class="px-6 py-4 text-right">Montant</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if (empty($driversReport)): ?>
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-400">
                                    <span class="material-symbols-outlined text-[24px]">inbox</span>
                                    <p class="text-sm mt-1">Aucune donnée</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                        <?php foreach ($driversReport as $driver): ?>
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 font-medium text-gray-900"><?= esc($driver['conducteur']) ?></td>
                                <td class="px-6 py-4 text-right text-gray-600"><?= esc($number($driver['courses'])) ?></td>
                                <td class="px-6 py-4 text-right text-gray-600"><?= esc($number($driver['reservations'])) ?></td>
                                <td class="px-6 py-4 text-right text-gray-600"><?= esc($number($driver['sieges'])) ?></td>
                                <td class="px-6 py-4 text-right font-semibold text-gray-900"><?= esc($money($driver['montant'])) ?> $</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <!-- Flotte de Bus Table (full width) -->
    <section class="report-card bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
            <h3 class="text-base font-semibold text-gray-900">Utilisation de la flotte</h3>
            <span class="text-xs font-medium text-gray-500 bg-white px-2 py-1 rounded-md border border-gray-200">
                <?= count($busesReport) ?> bus
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-gray-600 uppercase text-[11px] font-semibold bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">Plaque</th>
                        <th class="px-6 py-4 text-right">Capacité</th>
                        <th class="px-6 py-4 text-right">Courses</th>
                        <th class="px-6 py-4 text-right">Rés.</th>
                        <th class="px-6 py-4 text-right">Passagers</th>
                        <th class="px-6 py-4 text-right">Montant</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (empty($busesReport)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-400">
                                <span class="material-symbols-outlined text-[24px]">inbox</span>
                                <p class="text-sm mt-1">Aucune donnée</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($busesReport as $bus): ?>
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 font-semibold text-gray-900"><?= esc($bus['numero_plaque']) ?></td>
                            <td class="px-6 py-4 text-right text-gray-600"><?= esc($number($bus['capacite'])) ?> pl.</td>
                            <td class="px-6 py-4 text-right text-gray-600"><?= esc($number($bus['courses'])) ?></td>
                            <td class="px-6 py-4 text-right text-gray-600"><?= esc($number($bus['reservations'])) ?></td>
                            <td class="px-6 py-4 text-right text-gray-600"><?= esc($number($bus['passagers'])) ?></td>
                            <td class="px-6 py-4 text-right font-semibold text-gray-900"><?= esc($money($bus['montant'])) ?> $</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <div class="text-xs text-gray-400 text-right">Généré le <?= esc(date('d/m/Y H:i')) ?></div>

</main>
<?= $this->endSection() ?>