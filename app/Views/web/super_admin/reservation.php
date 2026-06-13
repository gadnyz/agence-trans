<?= $this->extend('web/layouts/super_admin') ?>

<?= $this->section('content') ?>

<div class="fixed top-[64px] sm:top-[72px] left-0 w-full h-[60px] z-40 flex items-center justify-between px-6 bg-white border-b border-gray-200 shadow-sm transition-all">
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

<main class="pt-[140px] px-4 sm:px-6 lg:px-8 pb-12 max-w-[1600px] mx-auto">
    <section class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4 bg-white p-4 rounded-2xl border border-gray-200 shadow-sm">
        <input type="text" placeholder="Recherche (Ref, Nom...)" class="w-full rounded-xl border-gray-300 border px-4 py-2 text-sm shadow-sm focus:border-blue-500 outline-none">
        <input type="date" class="w-full rounded-xl border-gray-300 border px-4 py-2 text-sm shadow-sm focus:border-blue-500 outline-none">
        <select class="w-full rounded-xl border-gray-300 border px-4 py-2 text-sm shadow-sm focus:border-blue-500 outline-none bg-white">
            <option>Tous les statuts</option>
            <option>Validé</option>
        </select>
        <button class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-200 transition-all">
            Filtrer
        </button>
    </section>

    <section class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
            <h3 class="text-base font-semibold text-gray-900">Dernières réservations</h3>
            <span class="text-xs font-medium text-gray-500 bg-white px-2 py-1 rounded-md border border-gray-200">Total : 0</span>
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
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">Aucune réservation pour le moment</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</main>

<?= $this->endSection() ?>