<?= $this->extend('web/layouts/main') ?>

<?= $this->section('styles') ?>
<style>
    :root {
        --color-primary: #2563eb; 
        --color-primary-container: #dbeafe;
        --color-on-primary-container: #1e3a8a;
        --color-primary-fixed: #dbeafe;
        --color-on-primary-fixed: #1e3a8a;
        --color-on-primary-fixed-variant: #2563eb;
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
