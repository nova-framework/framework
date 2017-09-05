<div class="page-header">
    <h1><?= $title; ?></h1>
</div>

<?= $content; ?>

<?php Section::start('page-top'); ?>

<div style="padding: 20px;">
This is my shiny new Section from the View.
</div>

<?php Section::stop(); ?>
