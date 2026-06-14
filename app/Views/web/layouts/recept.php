<?= $this->extend('web/layouts/main') ?>

<?= $this->section('styles') ?>
<style>
    :root {
        --color-primary: #0d9488; /* Teal */
        --color-primary-container: #ccfbf1;
        --color-on-primary-container: #115e59;
        --color-primary-fixed: #ccfbf1;
        --color-on-primary-fixed: #115e59;
        --color-on-primary-fixed-variant: #0d9488;
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
