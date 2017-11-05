<section class="content-header">
    <h1><?= __d('roles', 'Show Role'); ?></h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('admin/dashboard'); ?>"><i class="fa fa-dashboard"></i> <?= __d('roles', 'Dashboard'); ?></a></li>
        <li><a href="<?= site_url('admin/roles'); ?>"><?= __d('roles', 'Roles'); ?></a></li>
        <li><?= __d('roles', 'Show Role'); ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= Session::getMessages(); ?>

<div class="box box-widget">
    <div class="box-header">
        <h3 class="box-title"><?= __d('roles', 'User Role : <b>{0}</b>', $role->name); ?></h3>
    </div>
    <div class="box-body no-padding">
        <table id="left" class="table table-hover responsive">
            <tr class="bg-navy disabled">
                <th style="text-align: left; vertical-align: middle;"><?= __d('roles', 'Field'); ?></th>
                <th style="text-align: left; vertical-align: middle;"><?= __d('roles', 'Value'); ?></th>
            </tr>
            <tr>
                <th style="text-align: left; vertical-align: middle;"><?= __d('roles', 'ID'); ?></th>
                <td style="text-align: left; vertical-align: middle;" width="75%"><?= $role->id; ?></td>
            <tr>
            <tr>
                <th style="text-align: left; vertical-align: middle;"><?= __d('roles', 'Name'); ?></th>
                <td style="text-align: left; vertical-align: middle;" width="75%"><?= $role->name; ?></td>
            </tr>
            <tr>
                <th style="text-align: left; vertical-align: middle;"><?= __d('roles', 'Slug'); ?></th>
                <td style="text-align: left; vertical-align: middle;" width="75%"><?= $role->slug; ?></td>
            </tr>
            <tr>
                <th style="text-align: left; vertical-align: middle;"><?= __d('roles', 'Description'); ?></th>
                <td style="text-align: left; vertical-align: middle;" width="75%"><?= $role->description; ?></td>
            </tr>
            <tr>
                <th style="text-align: left; vertical-align: middle;"><?= __d('roles', 'Created At'); ?></th>
                <td style="text-align: left; vertical-align: middle;" width="75%"><?= $role->created_at->formatLocalized(__d('roles', '%d %b %Y, %R')); ?></td>
            </tr>
            <tr>
                <th style="text-align: left; vertical-align: middle;"><?= __d('roles', 'Updated At'); ?></th>
                <td style="text-align: left; vertical-align: middle;" width="75%"><?= $role->updated_at->formatLocalized(__d('roles', '%d %b %Y, %R')); ?></td>
            <tr>
        </table>
    </div>
</div>

<a class="btn btn-primary col-sm-2" href="<?= site_url('admin/roles'); ?>"><?= __d('roles', '<< Previous Page'); ?></a>

<div class="clearfix"></div>
<br>

</section>
