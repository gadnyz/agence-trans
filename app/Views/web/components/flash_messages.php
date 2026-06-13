<div class="px-margin pt-sm">
    <?php if (session()->getFlashdata('error')): ?>
        <div class="p-md mb-md bg-error-container text-on-error-container border border-error/20 rounded-xl font-body-md" role="alert">
            <?= esc(session()->getFlashdata('error')) ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="p-md mb-md bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl font-body-md" role="status">
            <?= esc(session()->getFlashdata('success')) ?>
        </div>
    <?php endif; ?>
</div>