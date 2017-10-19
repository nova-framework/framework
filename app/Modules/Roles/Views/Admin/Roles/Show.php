<section class="content-header">
    <h1><?= __d('users', 'Show Role'); ?></h1>
    <ol class="breadcrumb">
        <li><a href='<?= site_url('admin/dashboard'); ?>'><i class="fa fa-dashboard"></i> <?= __d('users', 'Dashboard'); ?></a></li>
        <li><a href='<?= site_url('admin/roles'); ?>'><?= __d('users', 'Roles'); ?></a></li>
        <li><?= __d('users', 'Show Role'); ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= Session::getMessages(); ?>

<div class="box box-widget">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('users', 'User Role : <b>{0}</b>', $role->name); ?></h3>
    </div>
    <div class="box-body no-padding">
        <table id='left' class='table table-hover responsive'>
            <tr>
                <th style='text-align: left; vertical-align: middle;'><?= __d('users', 'ID'); ?></th>
                <td style='text-align: left; vertical-align: middle;' width='70%'><?= $role->id; ?></td>
            <tr>
            <tr>
                <th style='text-align: left; vertical-align: middle;'><?= __d('users', 'Name'); ?></th>
                <td style='text-align: left; vertical-align: middle;' width='75%'><?= $role->name; ?></td>
            </tr>
            <tr>
                <th style='text-align: left; vertical-align: middle;'><?= __d('users', 'Slug'); ?></th>
                <td style='text-align: left; vertical-align: middle;' width='75%'><?= $role->slug; ?></td>
            </tr>
            <tr>
                <th style='text-align: left; vertical-align: middle;'><?= __d('users', 'Description'); ?></th>
                <td style='text-align: left; vertical-align: middle;' width='75%'><?= $role->description; ?></td>
            </tr>
            <tr>
                <th style='text-align: left; vertical-align: middle;'><?= __d('users', 'Created At'); ?></th>
                <td style='text-align: left; vertical-align: middle;' width='75%'><?= $role->created_at->formatLocalized(__d('users', '%d %b %Y, %R')); ?></td>
            </tr>
            <tr>
                <th style='text-align: left; vertical-align: middle;'><?= __d('users', 'Updated At'); ?></th>
                <td style='text-align: left; vertical-align: middle;' width='75%'><?= $role->updated_at->formatLocalized(__d('users', '%d %b %Y, %R')); ?></td>
            <tr>
        </table>
    </div>
</div>

<a class='btn btn-primary' href='<?= site_url('admin/roles'); ?>'><?= __d('users', '<< Previous Page'); ?></a>

</section>
