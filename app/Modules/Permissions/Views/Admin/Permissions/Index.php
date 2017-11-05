<section class="content-header">
    <h1><?= $title; ?></h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('admin/dashboard'); ?>"><i class="fa fa-dashboard"></i> <?= __d('permissions', 'Dashboard'); ?></a></li>
        <li><?= __d('permissions', 'Permissions'); ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= Session::getMessages(); ?>

<?php $perms = $permissions->where('group', 'app'); ?>

<form action="<?= site_url('admin/permissions/'); ?>" class="form-horizontal" method='POST' enctype="multipart/form-data" role="form">

<div class="box box-default">
    <div class="box-header <?= $perms->isEmpty() ? 'with-border' : ''; ?>">
        <h3 class="box-title"><?= __d('permissions', 'Permissions registered by the main <b>Application</b>'); ?></h3>
    </div>
    <div class="box-body <?= ! $perms->isEmpty() ? 'no-padding' : ''; ?>">
        <?php if (! $perms->isEmpty()) { ?>
        <?php $count = $roles->count(); ?>
        <table id="left" class="table table-striped table-hover responsive">
            <tr class="bg-navy disabled">
                <th style="text-align: left; vertical-align: middle;" width="<?= (100 - (10 * $count)); ?>%"><?= __d('permissions', 'Permission'); ?></th>
                <?php foreach ($roles as $role) { ?>
                <th style="text-align: center; vertical-align: middle;" width="10%"><?= $role->name; ?></th>
                <?php } ?>
            </tr>
            <?php foreach ($perms as $permission) { ?>
            <tr>
                <td style="text-align: left; vertical-align: middle;"><?= $permission->name; ?></td>
                <?php $ids = Input::get('permission_id.' .$permission->id, $permission->roles->lists('id')); ?>
                <?php foreach ($roles as $role) { ?>
                <td style="text-align: center; vertical-align: middle;">
                    <input
                        type="checkbox"
                        name="permission_id[<?= $permission->id; ?>][]"
                        value="<?= $role->id; ?>"
                        <?= in_array($role->id, $ids) ? 'checked="checked"' : ''; ?>
                    />
                </td>
                <?php } ?>
            </tr>
            <?php } ?>
        </table>
        <?php } else { ?>
        <p class="text-center" style="margin-top: 10px;"><?= __d('permissions', 'The main <b>Application</b> has no registered permissions.'); ?></p>
        <?php } ?>
    </div>
    <?php if (! $perms->isEmpty()) { ?>
    <div class="box-footer">
        <input class="btn btn-success col-sm-2 pull-right" type="submit" id="submit" name="submit" value="<?= __d('permissions', 'Apply the changes') ?>" />&nbsp;
    </div>
    <?php } ?>
</div>

<?= csrf_field(); ?>

</form>

<?php foreach ($modules as $module) { ?>
<?php $perms = $permissions->where('group', $module['slug']); ?>
<?php if ($perms->isEmpty()) continue; ?>
<?php $count = $roles->count(); ?>

<form action="<?= site_url('admin/permissions/'); ?>" class="form-horizontal" method='POST' enctype="multipart/form-data" role="form">

<div class="box box-widget">
    <div class="box-header">
        <h3 class="box-title"><?= __d('permissions', 'Permissions registered by the <b>{0}</b> module', $module['basename']); ?></h3>
    </div>
    <div class="box-body no-padding">
        <table id="left" class="table table-striped table-hover responsive">
            <tr class="bg-navy disabled">
                <th style="text-align: left; vertical-align: middle;" width="<?= (100 - (10 * $count)); ?>%"><?= __d('permissions', 'Permission'); ?></th>
                <?php foreach ($roles as $role) { ?>
                <th style="text-align: center; vertical-align: middle;" width="10%"><?= $role->name; ?></th>
                <?php } ?>
            </tr>
            <?php foreach ($perms as $permission) { ?>
            <tr>
                <td style="text-align: left; vertical-align: middle;"><?= $permission->name; ?></td>
                <?php $ids = Input::get('permission_id.' .$permission->id, $permission->roles->lists('id')); ?>
                <?php foreach ($roles as $role) { ?>
                <td style="text-align: center; vertical-align: middle;">
                    <input
                        type="checkbox"
                        name="permission_id[<?= $permission->id; ?>][]"
                        value="<?= $role->id; ?>"
                        <?= in_array($role->id, $ids) ? 'checked="checked"' : ''; ?>
                    />
                </td>
                <?php } ?>
            </tr>
            <?php } ?>
        </table>
    </div>
    <div class="box-footer">
        <input class="btn btn-success col-sm-2 pull-right" type="submit" id="submit" name="submit" value="<?= __d('permissions', 'Apply the changes') ?>" />&nbsp;
    </div>
</div>

<?= csrf_field(); ?>

</form>

<?php } ?>

</section>
