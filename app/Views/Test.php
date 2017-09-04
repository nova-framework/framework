<div class="page-header">
    <h1><?= $title; ?></h1>
</div>

<?= $content; ?>

<?php Section::start('page-top'); ?>
This is my shiny Section from View.
<?php Section::stop(); ?>
