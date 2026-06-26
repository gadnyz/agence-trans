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

$user = session()->get('user') ?? [];
$displayName = trim((string) (($user['prenom'] ?? '') ?: ($user['username'] ?? 'Admin')));

// Determine the action URL based on the current layout
$isDashboardSuperAdmin = str_contains($layout ?? '', 'super_admin');
$dashboardActionUrl = base_url($isDashboardSuperAdmin ? 'super-admin/dashboard' : 'admin/dashboard');
?>

<main class="px-4 pb-12 max-w-[1600px] mx-auto">

    <!-- ── En-tête + Filtres ── -->
    <div class="no-print w-full min-h-[60px] py-3 mb-6 rounded-2xl flex flex-col lg:flex-row lg:items-center lg:justify-between px-6 bg-white border border-gray-200 shadow-sm gap-4">
        <div class="flex items-center gap-3">
            <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-blue-50 text-blue-600">
                <span class="material-symbols-outlined text-[20px]">dashboard</span>
            </div>
            <div>
                <h2 class="text-lg font-semibold text-gray-900 tracking-tight">Tableau de bord</h2>
                <p class="text-xs text-gray-500"><?= esc($periodLabel) ?></p>
            </div>
        </div>

        <form class="flex flex-wrap items-center gap-2" method="get" action="<?= esc($dashboardActionUrl) ?>">
            <input class="h-9 px-3 rounded-xl border-gray-300 border text-sm shadow-sm focus:border-blue-500 outline-none" type="date" name="date_debut" value="<?= esc($filters['date_debut']) ?>" title="Date début">
            <input class="h-9 px-3 rounded-xl border-gray-300 border text-sm shadow-sm focus:border-blue-500 outline-none" type="date" name="date_fin" value="<?= esc($filters['date_fin']) ?>" title="Date fin">

            <select class="h-9 px-3 rounded-xl border-gray-300 border text-sm shadow-sm focus:border-blue-500 outline-none bg-white max-w-[240px]" name="id_trajet" title="Trajet">
                <option value="">Tous les trajets</option>
                <?php foreach ($options['trajets'] as $route): ?>
                    <option value="<?= esc($route['id_trajet']) ?>" <?= $selected($filters['id_trajet'], $route['id_trajet']) ?>><?= esc($routeLabel($route)) ?></option>
                <?php endforeach; ?>
            </select>

            <?php if ($isDashboardSuperAdmin && !empty($options['agents'])): ?>
                <select class="h-9 px-3 rounded-xl border-gray-300 border text-sm shadow-sm focus:border-blue-500 outline-none bg-white max-w-[200px]" name="id_agent" title="Agent">
                    <option value="">Tous les agents</option>
                    <?php foreach ($options['agents'] as $agent): ?>
                        <option value="<?= esc($agent['id_utilisateur']) ?>" <?= $selected($filters['id_agent'], $agent['id_utilisateur']) ?>><?= esc($agent['username']) ?></option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>

            <button class="h-9 w-9 rounded-xl bg-blue-600 text-white flex items-center justify-center hover:bg-blue-700 transition-all shadow-sm" type="submit" title="Appliquer">
                <span class="material-symbols-outlined text-[20px]">filter_alt</span>
            </button>
            <a class="h-9 w-9 rounded-xl bg-white border border-gray-300 text-gray-700 flex items-center justify-center hover:bg-gray-50 transition-all shadow-sm" href="<?= esc($dashboardActionUrl) ?>" title="Réinitialiser">
                <span class="material-symbols-outlined text-[20px]">restart_alt</span>
            </a>
            <button class="h-9 px-4 rounded-xl bg-white border border-gray-300 text-gray-700 flex items-center gap-2 hover:bg-gray-50 transition-all shadow-sm" type="button" onclick="window.print()">
                <span class="material-symbols-outlined text-[18px]">print</span>
                <span class="hidden sm:inline text-sm font-medium">Imprimer</span>
            </button>
        </form>
    </div>

    <!-- ── KPI Summary Cards ── -->
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <div class="report-card bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
            <div class="flex items-center gap-2 mb-3">
                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-blue-50 text-blue-600">
                    <span class="material-symbols-outlined text-[18px]">payments</span>
                </div>
            </div>
            <span class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Encaissements</span>
            <strong class="block text-xl font-bold text-gray-900 mt-1"><?= esc($money($summary['encaissements'])) ?> <span class="text-xs text-gray-500 font-normal">USD</span></strong>
            <span class="block text-xs text-gray-500 mt-1"><?= esc($number($summary['paiements'])) ?> paiement(s)</span>
        </div>
        <div class="report-card bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
            <div class="flex items-center gap-2 mb-3">
                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600">
                    <span class="material-symbols-outlined text-[18px]">book_online</span>
                </div>
            </div>
            <span class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Réservations</span>
            <strong class="block text-xl font-bold text-gray-900 mt-1"><?= esc($number($summary['reservations'])) ?></strong>
            <span class="block text-xs text-gray-500 mt-1"><?= esc($number($summary['sieges'])) ?> siège(s) réservé(s)</span>
        </div>
        <div class="report-card bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
            <div class="flex items-center gap-2 mb-3">
                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-violet-50 text-violet-600">
                    <span class="material-symbols-outlined text-[18px]">group</span>
                </div>
            </div>
            <span class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Clients</span>
            <strong class="block text-xl font-bold text-gray-900 mt-1"><?= esc($number($summary['clients'])) ?></strong>
            <span class="block text-xs text-gray-500 mt-1"><?= esc($number($summary['nouveaux_clients'])) ?> nouveau(x)</span>
        </div>
        <div class="report-card bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
            <div class="flex items-center gap-2 mb-3">
                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-amber-50 text-amber-600">
                    <span class="material-symbols-outlined text-[18px]">route</span>
                </div>
            </div>
            <span class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Courses</span>
            <strong class="block text-xl font-bold text-gray-900 mt-1"><?= esc($number($summary['courses'])) ?></strong>
            <span class="block text-xs text-gray-500 mt-1"><?= esc($number($summary['conducteurs'])) ?> conducteur(s)</span>
        </div>
        <div class="report-card bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
            <div class="flex items-center gap-2 mb-3">
                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-rose-50 text-rose-600">
                    <span class="material-symbols-outlined text-[18px]">directions_bus</span>
                </div>
            </div>
            <span class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Bus actifs</span>
            <strong class="block text-xl font-bold text-gray-900 mt-1"><?= esc($number($summary['bus'])) ?></strong>
            <span class="block text-xs text-gray-500 mt-1">utilisé(s) sur la période</span>
        </div>
    </section>

    <!-- ── Recent Reservations Table ── -->
    <section class="report-card bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
            <div>
                <h3 class="text-base font-semibold text-gray-900">Dernières réservations</h3>
                <p class="text-xs text-gray-500 mt-0.5">Les 10 réservations les plus récentes</p>
            </div>
            <a href="<?= base_url($isDashboardSuperAdmin ? 'super-admin/rapports' : 'admin/rapports') ?>" class="text-sm text-blue-600 font-medium hover:underline flex items-center gap-1">
                Voir tout
                <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-gray-600 uppercase text-[11px] font-semibold bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">Référence</th>
                        <th class="px-6 py-4">Trajet</th>
                        <th class="px-6 py-4">Client</th>
                        <th class="px-6 py-4">Heure</th>
                        <th class="px-6 py-4 text-center">Statut</th>
                        <th class="px-6 py-4 text-right">Montant</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (empty($details['reservations'])): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                <div class="flex flex-col items-center gap-2">
                                    <span class="material-symbols-outlined text-[36px]">inbox</span>
                                    <p class="text-sm font-medium">Aucune réservation pour les filtres sélectionnés.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($details['reservations'] as $row): ?>
                        <?php
                        $statut = strtoupper($row['statut'] ?? 'INCONNU');
                        $badgeClass = 'bg-gray-50 text-gray-700 border-gray-200';
                        if ($statut === 'EN ATTENTE') $badgeClass = 'bg-yellow-50 text-yellow-700 border-yellow-200';
                        elseif (in_array($statut, ['CONFIRME', 'CONFIRMÉ', 'VALIDE'])) $badgeClass = 'bg-green-50 text-green-700 border-green-200';
                        elseif (in_array($statut, ['ANNULE', 'ANNULÉ'])) $badgeClass = 'bg-red-50 text-red-700 border-red-200';
                        ?>
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 font-semibold text-gray-900"><?= esc($row['reference_reservation'] ?? '#' . ($row['id_reservation'] ?? '-')) ?></td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[16px] text-gray-400">route</span>
                                    <span class="text-gray-700"><?= esc($row['trajet'] ?? '-') ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900"><?= esc($row['client'] ?? '-') ?></div>
                                <div class="text-xs text-gray-500 mt-0.5"><?= esc($row['client_telephone'] ?? '') ?></div>
                            </td>
                            <td class="px-6 py-4 text-gray-600"><?= esc(substr((string) ($row['heure_depart'] ?? ''), 0, 5)) ?></td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-block px-2.5 py-0.5 text-[11px] font-semibold rounded-full border <?= $badgeClass ?>">
                                    <?= esc($row['statut'] ?? '-') ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right font-semibold text-gray-900">
                                <?= esc(number_format((float) ($row['montant_final'] ?? 0), 2, '.', ' ')) ?> <?= esc($row['symbole'] ?? '$') ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

</main>
<?= $this->endSection() ?>