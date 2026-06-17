<?= $this->extend($layout ?? 'web/layouts/super_admin') ?>

<?= $this->section('content') ?>

<main class="px-4 pb-12 max-w-[1600px] mx-auto">
    <div class="w-full h-[60px] mb-6 rounded-2xl flex items-center justify-between px-6 bg-white border-b border-gray-200 shadow-sm transition-all">
        <div class="flex items-center gap-3 h-full">
            <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-blue-50 text-blue-600">
                <span class="material-symbols-outlined text-[20px]">confirmation_number</span>
            </div>
            <h2 class="text-lg font-semibold text-gray-900 tracking-tight">Réservations</h2>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?= base_url('reservations/create') ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition-all shadow-sm">
                <span class="material-symbols-outlined text-[18px]">add</span>
                <span>Nouvelle</span>
            </a>
        </div>
    </div>

    <section class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4 bg-white p-4 rounded-2xl border border-gray-200 shadow-sm">
        <input type="text" placeholder="Recherche (Ref, Nom...)" class="w-full rounded-xl border-gray-300 border px-4 py-2 text-sm shadow-sm focus:border-blue-500 outline-none">
        <input type="date" class="w-full rounded-xl border-gray-300 border px-4 py-2 text-sm shadow-sm focus:border-blue-500 outline-none">
        <select class="w-full rounded-xl border-gray-300 border px-4 py-2 text-sm shadow-sm focus:border-blue-500 outline-none bg-white">
            <option>Tous les statuts</option>
            <option>EN_ATTENTE</option>
            <option>CONFIRME</option>
        </select>
        <button class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-200 transition-all">
            Filtrer
        </button>
    </section>

    <section class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
            <h3 class="text-base font-semibold text-gray-900">Dernières réservations</h3>
            <span class="text-xs font-medium text-gray-500 bg-white px-2 py-1 rounded-md border border-gray-200">Total : <?= count($reservations ?? []) ?></span>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-gray-600 uppercase text-[11px] font-semibold bg-gray-50">
                    <tr>
                        <th class="px-6 py-4">Référence</th>
                        <th class="px-6 py-4">Client</th>
                        <th class="px-6 py-4">Programme</th>
                        <th class="px-6 py-4 text-right">Montant</th>
                        <th class="px-6 py-4 text-center">Statut</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    
                    <?php if (empty($reservations)) : ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">Aucune réservation pour le moment</td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($reservations as $res) : ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    <?= esc($res['reference_reservation']) ?>
                                </td>
                                
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900"><?= esc($res['client_nom']) ?></div>
                                    <div class="text-xs text-gray-500"><?= esc($res['client_telephone']) ?></div>
                                </td>
                                
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">
                                        <?= esc($res['lieu_depart']) ?> <span class="text-gray-400">→</span> <?= esc($res['lieu_arrivee']) ?>
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        <?= date('d/m/Y', strtotime($res['date_programme'])) ?>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 text-right font-medium">
                                    <?= number_format($res['montant_final'], 2, '.', ' ') ?> <?= esc($res['symbole'] ?? '$') ?>
                                </td>
                                
                                <td class="px-6 py-4 text-center">
                                    <?php
                                        // $statut = strtoupper($res['statut_reservation']);
                                        $statut = strtoupper($res['statut_reservation'] ?? 'INCONNU');
                                        $badgeClass = 'bg-gray-100 text-gray-800 border-gray-200'; // Défaut

                                        if ($statut === 'EN ATTENTE') {
                                            $badgeClass = 'bg-yellow-50 text-yellow-700 border-yellow-200';
                                        } elseif ($statut === 'CONFIRME') {
                                            $badgeClass = 'bg-green-50 text-green-700 border-green-200';
                                        } elseif ($statut === 'ANNULE') {
                                            $badgeClass = 'bg-red-50 text-red-700 border-red-200';
                                        }
                                    ?>
                                    <span class="px-2.5 py-1 text-[11px] font-semibold rounded-full border <?= $badgeClass ?>">
                                        <?= esc($res['statut_reservation']) ?>
                                    </span>
                                </td>
                                
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button title="Imprimer le ticket" class="p-1.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                            <span class="material-symbols-outlined text-[18px]">print</span>
                                        </button>
                                        <button title="Voir les détails" class="p-1.5 text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
                                            <span class="material-symbols-outlined text-[18px]">visibility</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>

                </tbody>
            </table>
        </div>
    </section>
</main>

<?= $this->endSection() ?>