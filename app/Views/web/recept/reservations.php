<?= $this->extend('web/layouts/super_admin') ?>

<?= $this->section('content') ?>

<?php
$user        = session()->get('user') ?? [];
$displayName = trim(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? '')) ?: ($user['username'] ?? 'Guichetier');
$today       = date('d/m/Y');
?>

<div class="space-y-lg">

    {{-- ── En-tête ── --}}
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-sm">
        <div>
            <h2 class="font-headline-lg text-headline-lg text-on-surface">
                Guichet — Réservations
            </h2>
            <p class="text-body-lg text-outline"><?= esc($today) ?> &mdash; <?= esc($displayName) ?></p>
        </div>
        <button
            id="btn-nouvelle-reservation"
            class="inline-flex items-center gap-sm px-lg py-sm bg-primary text-on-primary rounded-lg font-label-lg hover:brightness-110 transition-all shadow-sm"
        >
            <span class="material-symbols-outlined text-[20px]">add</span>
            Nouvelle réservation
        </button>
    </div>

    {{-- ── Recherche rapide ── --}}
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-lg shadow-sm">
        <h3 class="font-title-sm text-title-sm text-on-surface mb-md">Rechercher un programme</h3>
        <div class="flex flex-col sm:flex-row gap-md">
            <div class="relative flex-1">
                <span class="material-symbols-outlined absolute left-md top-1/2 -translate-y-1/2 text-outline text-[20px]">search</span>
                <input
                    id="search-programme"
                    type="text"
                    placeholder="Trajet, date, ville de départ..."
                    class="w-full pl-xl pr-md py-sm bg-surface-container-low border border-outline-variant rounded-lg text-body-md focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all"
                />
            </div>
            <input
                type="date"
                id="filter-date"
                value="<?= date('Y-m-d') ?>"
                class="px-md py-sm bg-surface-container-low border border-outline-variant rounded-lg text-body-md focus:ring-2 focus:ring-primary outline-none"
            />
            <button class="inline-flex items-center gap-sm px-lg py-sm bg-surface-container border border-outline-variant rounded-lg font-label-lg hover:bg-surface-container-high transition-all">
                <span class="material-symbols-outlined text-[20px]">filter_list</span>
                Filtrer
            </button>
        </div>
    </div>

    {{-- ── Liste des programmes disponibles ── --}}
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm overflow-hidden">
        <div class="flex justify-between items-center p-lg border-b border-outline-variant">
            <h3 class="font-title-md text-title-md text-on-surface">Programmes du jour</h3>
            <span class="text-label-sm text-outline bg-surface-container px-sm py-xs rounded-full">
                0 voyages disponibles
            </span>
        </div>

        {{-- État vide ── à remplacer par une boucle réelle --}}
        <div class="flex flex-col items-center justify-center py-xl text-outline gap-md">
            <span class="material-symbols-outlined text-[56px] text-outline-variant">directions_bus</span>
            <div class="text-center">
                <p class="text-body-md font-medium text-on-surface">Aucun programme pour cette date</p>
                <p class="text-body-sm text-outline mt-xs">Sélectionnez une autre date ou contactez l'administrateur.</p>
            </div>
        </div>

        {{-- 
        Exemple de ligne programme (décommenter quand les données arrivent) :
        <div class="divide-y divide-outline-variant/50">
            <div class="flex items-center justify-between p-lg hover:bg-surface-container-low transition-colors">
                <div class="flex items-center gap-md">
                    <div class="w-10 h-10 bg-primary-container text-primary rounded-lg flex items-center justify-center">
                        <span class="material-symbols-outlined">directions_bus</span>
                    </div>
                    <div>
                        <p class="font-label-lg text-on-surface">Kinshasa → Lubumbashi</p>
                        <p class="text-body-sm text-outline">Départ 06h00 — Bus KT-001 — 45 sièges libres</p>
                    </div>
                </div>
                <button class="px-md py-sm bg-primary text-on-primary rounded-lg font-label-sm hover:brightness-110 transition-all">
                    Réserver
                </button>
            </div>
        </div>
        --}}
    </div>

    {{-- ── Réservations du jour (créées par ce guichetier) ── --}}
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm overflow-hidden">
        <div class="flex justify-between items-center p-lg border-b border-outline-variant">
            <h3 class="font-title-md text-title-md text-on-surface">Mes réservations du jour</h3>
        </div>
        <div class="flex flex-col items-center justify-center py-xl text-outline gap-sm">
            <span class="material-symbols-outlined text-[48px] text-outline-variant">inbox</span>
            <p class="text-body-md">Aucune réservation enregistrée aujourd'hui.</p>
        </div>
    </div>

</div>

<?= $this->endSection() ?>
