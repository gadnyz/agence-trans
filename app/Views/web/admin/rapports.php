<?= $this->extend($layout ?? 'web/layouts/admin') ?>

<?= $this->section('styles') ?>
<style>
    /* Styles spécifiques pour l'impression propre du rapport A4 */
    @media print {
        @page { size: A4 landscape; margin: 10mm; }
        header, aside, .no-print { display: none !important; }
        body { background: #FFFFFF !important; }
        main { max-width: none !important; padding: 0 !important; }
        .report-card { border: 1px solid #E5E7EB !important; box-shadow: none !important; rounded: 0 !important; }
        .report-table { font-size: 10px; }
        .report-table th, .report-table td { padding: 6px 8px !important; }
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
$pageTitle   = $pageTitle ?? 'Rapports';
$reportActionUrl = $reportActionUrl ?? base_url('admin/rapports');
$baseParams  = [
    'date_debut' => $filters['date_debut'],
    'date_fin'   => $filters['date_fin'],
    'id_trajet'  => $filters['id_trajet'] ?: null,
    'per_page'   => $pagination['per_page'],
];
$cleanParams = static fn (array $params): array => array_filter($params, static fn ($value): bool => $value !== null && $value !== '');
$pageUrl     = static fn (int $page): string => $reportActionUrl . '?' . http_build_query($cleanParams(array_merge($baseParams, ['page' => $page])));
$pageStart   = max(1, (int) $pagination['page'] - 2);
$pageEnd     = min((int) $pagination['total_pages'], (int) $pagination['page'] + 2);
?>

<main class="px-4 pb-12 max-w-[1600px] mx-auto">

    <div class="no-print w-full min-h-[60px] py-3 mb-6 rounded-2xl flex flex-col lg:flex-row lg:items-center lg:justify-between px-6 bg-white border border-gray-200 shadow-sm gap-4">
        <div class="flex items-center gap-3">
            <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-blue-50 text-blue-600">
                <span class="material-symbols-outlined text-[20px]">analytics</span>
            </div>
            <div>
                <h2 class="text-lg font-semibold text-gray-900 tracking-tight"><?= esc($pageTitle) ?></h2>
                <p class="text-xs text-gray-500 sm:hidden"><?= esc($number($pagination['total'])) ?> ligne(s)</p>
            </div>
            <span class="hidden sm:inline-flex items-center text-xs font-medium text-gray-500 bg-gray-50 px-2 py-1 rounded-md border border-gray-200">
                <?= esc($number($pagination['total'])) ?> ligne(s)
            </span>
        </div>

        <form class="flex flex-wrap items-center gap-2" method="get" action="<?= esc($reportActionUrl) ?>">
            <input type="hidden" name="page" value="1">
            <input type="hidden" name="per_page" value="<?= esc($pagination['per_page']) ?>">

            <input class="h-9 px-3 rounded-xl border-gray-300 border text-sm shadow-sm focus:border-blue-500 outline-none" type="date" name="date_debut" value="<?= esc($filters['date_debut']) ?>" aria-label="Date début" title="Date début">
            <input class="h-9 px-3 rounded-xl border-gray-300 border text-sm shadow-sm focus:border-blue-500 outline-none" type="date" name="date_fin"   value="<?= esc($filters['date_fin']) ?>"   aria-label="Date fin"   title="Date fin">

            <select class="h-9 px-3 rounded-xl border-gray-300 border text-sm shadow-sm focus:border-blue-500 outline-none bg-white max-w-[240px]" name="id_trajet" aria-label="Trajet" title="Trajet">
                <option value="">Tous les trajets</option>
                <?php foreach ($options['trajets'] as $route): ?>
                    <option value="<?= esc($route['id_trajet']) ?>" <?= $selected($filters['id_trajet'], $route['id_trajet']) ?>><?= esc($routeLabel($route)) ?></option>
                <?php endforeach; ?>
            </select>

            <button class="h-9 w-9 rounded-xl bg-blue-600 text-white flex items-center justify-center hover:bg-blue-700 transition-all shadow-sm" type="submit" title="Appliquer" aria-label="Appliquer">
                <span class="material-symbols-outlined text-[20px]">filter_alt</span>
            </button>
            <a class="h-9 w-9 rounded-xl bg-white border border-gray-300 text-gray-700 flex items-center justify-center hover:bg-gray-50 transition-all shadow-sm" href="<?= esc($reportActionUrl) ?>" title="Réinitialiser" aria-label="Réinitialiser">
                <span class="material-symbols-outlined text-[20px]">restart_alt</span>
            </a>
            <button class="h-9 px-4 rounded-xl bg-white border border-gray-300 text-gray-700 flex items-center gap-2 hover:bg-gray-50 transition-all shadow-sm" type="button" onclick="window.print()">
                <span class="material-symbols-outlined text-[18px]">print</span>
                <span class="hidden sm:inline text-sm font-medium">Imprimer</span>
            </button>
        </form>
    </div>

    <div class="mb-6 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-2 px-2">
        <div>
            <h1 class="text-xl font-bold text-gray-900 tracking-tight">Rapport des réservations</h1>
            <p class="text-sm text-gray-500 mt-0.5"><?= esc($periodLabel) ?></p>
        </div>
        <div class="text-xs text-gray-400">Généré le <?= esc(date('d/m/Y H:i')) ?></div>
    </div>

    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <div class="report-card bg-white border border-gray-200 rounded-2xl shadow-sm p-5 transition-all">
            <span class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Encaissements</span>
            <strong class="block text-xl font-bold text-gray-900 mt-2"><?= esc($money($summary['encaissements'])) ?> <span class="text-xs text-gray-500 font-normal">USD</span></strong>
            <span class="block text-xs text-gray-500 mt-1"><?= esc($number($summary['paiements'])) ?> paiement(s)</span>
        </div>
        <div class="report-card bg-white border border-gray-200 rounded-2xl shadow-sm p-5 transition-all">
            <span class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Réservations</span>
            <strong class="block text-xl font-bold text-gray-900 mt-2"><?= esc($number($summary['reservations'])) ?></strong>
            <span class="block text-xs text-gray-500 mt-1"><?= esc($number($summary['sieges'])) ?> siège(s)</span>
        </div>
        <div class="report-card bg-white border border-gray-200 rounded-2xl shadow-sm p-5 transition-all">
            <span class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Clients</span>
            <strong class="block text-xl font-bold text-gray-900 mt-2"><?= esc($number($summary['clients'])) ?></strong>
            <span class="block text-xs text-gray-500 mt-1"><?= esc($number($summary['nouveaux_clients'])) ?> nouveau(x)</span>
        </div>
        <div class="report-card bg-white border border-gray-200 rounded-2xl shadow-sm p-5 transition-all">
            <span class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Courses</span>
            <strong class="block text-xl font-bold text-gray-900 mt-2"><?= esc($number($summary['courses'])) ?></strong>
            <span class="block text-xs text-gray-500 mt-1"><?= esc($number($summary['conducteurs'])) ?> conducteur(s)</span>
        </div>
        <div class="report-card bg-white border border-gray-200 rounded-2xl shadow-sm p-5 transition-all">
            <span class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Bus</span>
            <strong class="block text-xl font-bold text-gray-900 mt-2"><?= esc($number($summary['bus'])) ?></strong>
            <span class="block text-xs text-gray-500 mt-1">utilisé(s)</span>
        </div>
    </section>

    <section class="report-card bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
            <div>
                <h3 class="text-base font-semibold text-gray-900">Détail des réservations</h3>
                <p class="text-xs text-gray-500 mt-0.5">
                    <?= esc($number($pagination['from'])) ?>-<?= esc($number($pagination['to'])) ?> sur <?= esc($number($pagination['total'])) ?> · Page <?= esc($pagination['page']) ?>/<?= esc($pagination['total_pages']) ?>
                </p>
            </div>

            <form class="no-print flex items-center gap-2" method="get" action="<?= esc($reportActionUrl) ?>">
                <input type="hidden" name="date_debut" value="<?= esc($filters['date_debut']) ?>">
                <input type="hidden" name="date_fin"   value="<?= esc($filters['date_fin']) ?>">
                <?php if ($filters['id_trajet']): ?>
                    <input type="hidden" name="id_trajet" value="<?= esc($filters['id_trajet']) ?>">
                <?php endif; ?>
                <input type="hidden" name="page" value="1">
                <select class="h-8 px-2 rounded-lg border-gray-300 border text-xs shadow-sm focus:border-blue-500 outline-none bg-white" name="per_page" onchange="this.form.submit()" aria-label="Lignes par page">
                    <?php foreach ([10, 25, 50, 100] as $size): ?>
                        <option value="<?= esc($size) ?>" <?= $selected($pagination['per_page'], $size) ?>><?= esc($size) ?>/page</option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left report-table">
                <thead class="text-gray-600 uppercase text-[11px] font-semibold bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">Référence</th>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4">Course</th>
                        <th class="px-6 py-4">Client</th>
                        <!-- <th class="px-6 py-4">Agent</th> -->
                        <th class="px-6 py-4 text-center">Statut</th>
                        <th class="px-6 py-4 text-right">Sièges</th>
                        <th class="px-6 py-4 text-right">Payé</th>
                        <th class="px-6 py-4">Paiement</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if ($details['reservations'] === []): ?>
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-gray-400">
                                <div class="flex flex-col items-center gap-2">
                                    <span class="material-symbols-outlined text-[36px]">inbox</span>
                                    <p class="text-sm font-medium">Aucune réservation pour les filtres sélectionnés.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($details['reservations'] as $row): ?>
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 font-semibold text-gray-900"><?= esc($row['reference_reservation']) ?></td>
                            <td class="px-6 py-4 text-gray-600 text-xs"><?= esc($row['date_reservation']) ?></td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">
                                    #<?= esc($row['id_programme']) ?> · <?= esc(substr((string) $row['heure_depart'], 0, 5)) ?>
                                </div>
                                <div class="text-xs text-gray-500 mt-0.5">
                                    <?= esc($row['trajet']) ?> <span class="text-gray-300">·</span> <?= esc($row['numero_plaque']) ?>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900"><?= esc($row['client'] ?: '-') ?></div>
                                <div class="text-xs text-gray-500 mt-0.5"><?= esc($row['telephone'] ?: '-') ?></div>
                            </td>
                            
                            <td class="px-6 py-4 text-center">
                                <?php 
                                    $statut = strtoupper($row['statut_reservation'] ?? 'INCONNU');
                                    $badgeClass = 'bg-gray-50 text-gray-700 border-gray-200';
                                    if ($statut === 'EN ATTENTE') $badgeClass = 'bg-yellow-50 text-yellow-700 border-yellow-200';
                                    elseif (in_array($statut, ['CONFIRME', 'CONFIRMÉ'])) $badgeClass = 'bg-green-50 text-green-700 border-green-200';
                                    elseif (in_array($statut, ['ANNULE', 'ANNULÉ'])) $badgeClass = 'bg-red-50 text-red-700 border-red-200';
                                ?>
                                <span class="inline-block px-2 py-0.5 text-[10px] font-semibold rounded-full <?= $badgeClass ?>">
                                    <?= esc($row['statut_reservation']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right font-medium text-gray-700"><?= esc($number($row['nombre_places'])) ?></td>
                            <td class="px-6 py-4 text-right font-semibold text-gray-900"><?= esc($money($row['montant_paye'])) ?></td>
                            <td class="px-6 py-4">
                                <span class="text-xs font-medium text-gray-600 bg-gray-50 px-2 py-1 rounded border border-gray-200">
                                    <?= esc($row['modes_paiement'] ?: '-') ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <nav class="no-print px-6 py-4 border-t border-gray-100 bg-gray-50/30 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between" aria-label="Pagination">
            <div class="text-sm text-gray-500">
                Affichage de <span class="font-medium text-gray-900"><?= esc($number($pagination['from'])) ?></span> à <span class="font-medium text-gray-900"><?= esc($number($pagination['to'])) ?></span> sur <span class="font-medium text-gray-900"><?= esc($number($pagination['total'])) ?></span>
            </div>
            <div class="flex items-center gap-1.5">
                <?php if ($pagination['page'] > 1): ?>
                    <a class="h-9 px-3 rounded-xl border border-gray-300 bg-white text-gray-700 text-sm font-medium flex items-center gap-1 hover:bg-gray-50 transition-all shadow-sm" href="<?= esc($pageUrl($pagination['page'] - 1)) ?>">
                        <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                    </a>
                <?php else: ?>
                    <span class="h-9 px-3 rounded-xl border border-gray-200 text-gray-300 text-sm font-medium flex items-center gap-1 opacity-60 pointer-events-none">
                        <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                    </span>
                <?php endif; ?>

                <div class="hidden sm:flex items-center gap-1.5">
                    <?php for ($page = $pageStart; $page <= $pageEnd; $page++): ?>
                        <?php if ($page === (int) $pagination['page']): ?>
                            <span class="h-9 min-w-9 px-3 rounded-xl bg-blue-600 text-white text-sm font-semibold flex items-center justify-center shadow-sm"><?= esc($page) ?></span>
                        <?php else: ?>
                            <a class="h-9 min-w-9 px-3 rounded-xl border border-gray-300 bg-white text-gray-700 text-sm font-medium flex items-center justify-center hover:bg-gray-50 transition-all shadow-sm" href="<?= esc($pageUrl($page)) ?>"><?= esc($page) ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>

                <?php if ($pagination['page'] < $pagination['total_pages']): ?>
                    <a class="h-9 px-3 rounded-xl border border-gray-300 bg-white text-gray-700 text-sm font-medium flex items-center gap-1 hover:bg-gray-50 transition-all shadow-sm" href="<?= esc($pageUrl($pagination['page'] + 1)) ?>">
                        <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                    </a>
                <?php else: ?>
                    <span class="h-9 px-3 rounded-xl border border-gray-200 text-gray-300 text-sm font-medium flex items-center gap-1 opacity-60 pointer-events-none">
                        <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                    </span>
                <?php endif; ?>
            </div>
        </nav>
    </section>
</main>
<?= $this->endSection() ?>