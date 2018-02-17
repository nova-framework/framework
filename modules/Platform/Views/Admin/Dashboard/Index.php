<section class="content-header">
    <h1><?= __d('platform', 'Dashboard'); ?></h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('admin/dashboard'); ?>"><i class="fa fa-dashboard"></i> <?= __d('platform', 'Dashboard'); ?></a></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= Session::getMessages(); ?>

<?php if (! Widget::isEmptyPosition('backend.dashboard.top')) { ?>
<div class="row">
<?= Widget::position('backend.dashboard.top'); ?>
</div>
<?php } ?>

<?php if (! Widget::isEmptyPosition('backend.dashboard.content')) { ?>
<div class="row">
<?= Widget::position('backend.dashboard.content'); ?>
</div>
<?php } ?>

<?php if (! Widget::isEmptyPosition('backend.dashboard.bottom')) { ?>
<div class="row">
<?= Widget::position('backend.dashboard.bottom'); ?>
</div>
<?php } ?>

</section>
