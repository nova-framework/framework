<section class="content-header">
    <h1><?= __('Dashboard'); ?></h1>
    <ol class="breadcrumb">
        <li><a href='<?= site_url('users/dashboard'); ?>'><i class="fa fa-dashboard"></i> <?= __('Dashboard'); ?></a></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= Session::message('message'); ?>

<div class="box box-widget">
    <div class="box-body">
        <h4><strong><?= __('Yup. This is the Dashboard.'); ?></strong></h4>
        <p><?= __('Someday, we\'ll have widgets and stuff on here...'); ?></p>
    </div>
</div>

</section>
