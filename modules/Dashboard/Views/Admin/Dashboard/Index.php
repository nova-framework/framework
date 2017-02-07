<section class="content-header">
    <h1><?= __d('dashboard', 'Dashboard'); ?></h1>
    <ol class="breadcrumb">
        <li><a href='<?= site_url('admin/dashboard'); ?>'><i class="fa fa-dashboard"></i> <?= __d('dashboard', 'Dashboard'); ?></a></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= Session::getMessages(); ?>

<div class="row">
    <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <?= $smallBoxUsers; ?>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <?= $smallBoxUniqueVisitors; ?>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <?= $smallBoxOrders; ?>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <?= $smallBoxBounceRate; ?>
    </div>
    <!-- ./col -->
</div>

<?= $debug; ?>

</section>
