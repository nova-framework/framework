<section class="content-header">
    <h1><?= __d('content', 'Create a new {0}', $name); ?></h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('admin/dashboard'); ?>"><i class="fa fa-dashboard"></i> <?= __d('content', 'Dashboard'); ?></a></li>
        <li><a href="<?= site_url('admin/content/' .$type); ?>"><?= $mode; ?></a></li>
        <li><?= __d('content', 'Create {0}', $name); ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= Session::getMessages(); ?>

<div class="row">

<div class="col-md-9">

<form class="form-horizontal" action="<?= site_url('admin/users'); ?>" method='POST' enctype="multipart/form-data" role="form">

<div class="box box-default">
    <div class="box-body">
        <div class="col-md-12">
            <div class="form-group">
                <input name="title" id="title" type="text" style="font-size: 24px; padding: 10px; width: 100%;" value="<?= Input::old('title'); ?>" placeholder="<?= __d('content', 'Enter title here'); ?>">
            </div>
            <div class="form-group" style=" margin-bottom: 0;">
                <textarea name="content" id="content" style="width: 100%; padding: 10px; resize: none;" rows="20"><?= Input::old('content'); ?></textarea>
            </div>
        </div>
    </div>
</div>

</div>

<div class="col-md-3">

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('content', 'Publish'); ?></h3>
    </div>
    <div class="box-body">
    </div>
</div>

<?php if ($type == 'page') { ?>

<div class="box box-success">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('content', 'Page Attributes'); ?></h3>
    </div>
    <div class="box-body">
    </div>
</div>

<?php } ?>

<?php if ($type == 'post') { ?>

<div class="box box-success">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('content', 'Categories'); ?></h3>
    </div>
    <div class="box-body">
    </div>
</div>

<div class="box box-warning">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('content', 'Tags'); ?></h3>
    </div>
    <div class="box-body">
    </div>
</div>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('content', 'Featured Image'); ?></h3>
    </div>
    <div class="box-body">
    </div>
</div>

<?php } ?>

</div>

</div>

<a class="btn btn-primary col-sm-2" href="<?= site_url('admin/content/' .Str::plural($type)); ?>"><?= __d('content', '<< Previous Page'); ?></a>

<div class="clearfix"></div>
<br>

</section>
