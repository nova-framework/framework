<section class="content-header">
    <h1><?= __d('system', 'Dashboard'); ?></h1>
    <ol class="breadcrumb">
        <li><a href='<?= site_url('admin/dashboard'); ?>'><i class="fa fa-dashboard"></i> <?= __d('system', 'Dashboard'); ?></a></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= Session::getMessages(); ?>

<div class="box box-widget">
    <div class="box-body">
        <h4><strong><?= __d('system', 'Yup. This is the Dashboard.'); ?></strong></h4>
        <p><?= __d('system', 'Someday, we\'ll have widgets and stuff on here...'); ?></p>
    </div>
</div>

</section>
