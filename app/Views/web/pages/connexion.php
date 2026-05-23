<?= $this->extend('web/layouts/app') ?>

<?= $this->section('content') ?>
<div class="flex items-center justify-center min-h-[80vh]">
    <div class="w-full max-w-[400px] flex flex-col items-center">
        <!-- Brand Identity Container -->
        <div class="mb-xl text-center">
            <div class="flex items-center justify-center mb-sm">
                <span class="material-symbols-outlined text-primary text-[40px]"
                    style="font-variation-settings: 'FILL' 1;">directions_bus</span>
            </div>
            <h1 class="font-h1 text-h1 text-on-surface tracking-tight uppercase">KASHALA Trans</h1>
            <p class="font-body-md text-body-md text-on-surface-variant mt-xs">Agence de transport</p>
        </div>

        <!-- Central Login Card (Trello/Odoo Style) -->
        <div
            class="bg-surface-container-lowest border border-outline-variant rounded-lg p-xl w-full login-card shadow-sm">
            <?= form_open('login', ['class' => 'space-y-lg']) ?>
            <!-- Username Field -->
            <div class="space-y-xs">
                <label class="block font-label-caps text-label-caps text-on-surface-variant" for="username">NOM
                    D'UTILISATEUR</label>
                <div class="relative">
                    <input
                        class="w-full px-md py-sm bg-surface-container-lowest border border-outline-variant rounded-lg text-body-md font-body-md focus:outline-none focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB] transition-all placeholder:text-outline-variant"
                        id="username" name="username" placeholder="votre identifiant" required="" type="text" />
                </div>
            </div>
            <!-- Password Field -->
            <div class="space-y-xs mt-4">
                <div class="flex justify-between items-center">
                    <label class="block font-label-caps text-label-caps text-on-surface-variant" for="password">MOT DE
                        PASSE</label>
                </div>
                <div class="relative">
                    <input
                        class="w-full px-md py-sm bg-surface-container-lowest border border-outline-variant rounded-lg text-body-md font-body-md focus:outline-none focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB] transition-all placeholder:text-outline-variant"
                        id="password" name="password" placeholder="••••••••" required="" type="password" />
                </div>
            </div>
            <!-- Action Button -->
            <button
                class="w-full bg-[#2563EB] hover:bg-[#1d4ed8] text-white font-h2 text-body-md py-sm px-md rounded-lg shadow-sm transition-colors cursor-pointer flex justify-center items-center gap-sm mt-6"
                type="submit">
                Se connecter
            </button>
            <?= form_close() ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>