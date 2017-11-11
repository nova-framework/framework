<section class="content-header">
    <h1><?= __d('users', 'Create {0}', $name); ?></h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('admin/dashboard'); ?>"><i class="fa fa-dashboard"></i> <?= __d('users', 'Dashboard'); ?></a></li>
        <li><a href="<?= site_url('admin/content/' .$type); ?>"><?= $mode; ?></a></li>
        <li><?= __d('users', 'Create {0}', $name); ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= Session::getMessages(); ?>

<form class="form-horizontal" action="<?= site_url('admin/users'); ?>" method='POST' enctype="multipart/form-data" role="form">

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('users', 'Create a new {0}', $name); ?></h3>
    </div>
    <div class="box-body">
    </div>
</div>

<a class="btn btn-primary col-sm-2" href="<?= site_url('admin/content/' .Str::plural($type)); ?>"><?= __d('users', '<< Previous Page'); ?></a>

<div class="clearfix"></div>
<br>

</section>
