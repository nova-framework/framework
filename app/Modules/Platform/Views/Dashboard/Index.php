<section class="content-header">
    <h1><?= __d('platform', 'Dashboard'); ?></h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('dashboard'); ?>"><i class="fa fa-dashboard"></i> <?= __d('platform', 'Dashboard'); ?></a></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= Session::getMessages(); ?>

<?php if (! Widget::isEmptyPosition('frontend.dashboard.top')) { ?>
<div class="row">
<?= Widget::position('frontend.dashboard.top'); ?>
</div>
<?php } ?>

<?php if (! Widget::isEmptyPosition('frontend.dashboard.content')) { ?>
<div class="row">
<?= Widget::position('frontend.dashboard.content'); ?>
</div>
<?php } ?>

<?php if (! Widget::isEmptyPosition('frontend.dashboard.bottom')) { ?>
<div class="row">
<?= Widget::position('frontend.dashboard.bottom'); ?>
</div>
<?php } ?>

</section>
