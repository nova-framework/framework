<?php

//
$opt_category = Input::old('category');
$opt_hours    = Input::old('hours');
$opt_cfu      = Input::old('cfu');

//
$opt_hours = ! empty($opt_hours) ? $opt_hours : 1500;
$opt_cfu   = ! empty($opt_cfu)   ? $opt_cfu   : 60;

?>

<section class="content-header">
    <h1><?= __('Create User'); ?></h1>
    <ol class="breadcrumb">
        <li><a href='<?= site_url('admin/dashboard'); ?>'><i class="fa fa-dashboard"></i> <?= __('Dashboard'); ?></a></li>
        <li><a href='<?= site_url('admin/users'); ?>'><?= __('Users'); ?></a></li>
        <li><?= __('Create User'); ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= Session::message('status'); ?>

<form action="<?= site_url('admin/users'); ?>" class="form-horizontal" method="POST">

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __('Create a new User'); ?></h3>
    </div>
    <div class="box-body">
        <div class="form-group">
            <label class="col-sm-3 control-label" for="name"><?= __('Name'); ?> <font color='#CC0000'>*</font></label>
            <div class="col-sm-9">
                <input name="name" id="name" type="text" class="form-control" value="<?= Input::old('name'); ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label" for="category"><?= __('Category'); ?> <font color='#CC0000'>*</font></label>
            <div class="col-sm-9">
                <select name="category" id="category" class="form-control select2">
                    <option value="" <?php if (empty($opt_category)) echo 'selected'; ?>>- <?= __('Choose a Category'); ?> -</option>
                    <?php foreach ($categories as $category) { ?>
                    <option value="<?= $category->id ?>" <?php if ($opt_category == $category->id) echo 'selected'; ?>><?= $category->name; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label" for="hours"><?= __('CFU'); ?> <font color='#CC0000'>*</font></label>
            <div class="col-sm-9">
                <div class="col-sm-2" style="padding: 0;">
                    <input name="cfu" id="cfu" type="text" class="form-control" style="text-align: right;" value="<?= $opt_cfu; ?>">
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label" for="hours"><?= __('Hours'); ?> <font color='#CC0000'>*</font></label>
            <div class="col-sm-9">
                <div class="col-sm-2" style="padding: 0;">
                    <input name="hours" id="hours" type="text" class="form-control" style="text-align: right;" value="<?= $opt_hours; ?>">
                </div>
            </div>
        </div>
        <font color='#CC0000'>*</font><?= __('Required field'); ?>
    </div>
    <div class="box-footer with-border">
        <input type="submit" name="button" class='btn btn-success col-sm-3 pull-right' value="<?= __('Create User'); ?>">
    </div>
</div>

<input type="hidden" name="csrfToken" value="<?= $csrfToken; ?>" />

</form>

<a class='btn btn-primary' href='<?= site_url('admin/users'); ?>'><?= __('<< Previous Page'); ?></a>

</section>
