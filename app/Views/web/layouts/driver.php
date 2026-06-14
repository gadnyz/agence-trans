<?= $this->extend('web/layouts/main') ?>

<?= $this->section('styles') ?>
<style>
    /* Masquer la sidebar pour les chauffeurs */
    #sidebar {
        display: none !important;
    }
    :root {
        --color-primary: #d97706; /* Amber/Orange */
        --color-primary-container: #fef3c7;
        --color-on-primary-container: #78350f;
        --color-primary-fixed: #fef3c7;
        --color-on-primary-fixed: #78350f;
        --color-on-primary-fixed-variant: #d97706;
    }
</style>
<?= $this->renderSection('styles') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?= $this->renderSection('content') ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?= $this->renderSection('scripts') ?>
<?= $this->endSection() ?>
