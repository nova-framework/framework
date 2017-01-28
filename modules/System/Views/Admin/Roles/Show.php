<section class="content-header">
    <h1><?= __d('system', 'Show Role'); ?></h1>
    <ol class="breadcrumb">
        <li><a href='<?= site_url('admin/dashboard'); ?>'><i class="fa fa-dashboard"></i> <?= __d('system', 'Dashboard'); ?></a></li>
        <li><a href='<?= site_url('admin/roles'); ?>'><?= __d('system', 'Roles'); ?></a></li>
        <li><?= __d('system', 'Show Role'); ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= Session::getMessages(); ?>

<div class="box box-widget">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('system', 'User Role : <b>{0}</b>', $role->name); ?></h3>
    </div>
    <div class="box-body no-padding">
        <table id='left' class='table table-hover responsive'>
            <tr>
                <th style='text-align: left; vertical-align: middle;'><?= __d('system', 'ID'); ?></th>
                <td style='text-align: left; vertical-align: middle;' width='70%'><?= $role->id; ?></td>
            <tr>
            <tr>
                <th style='text-align: left; vertical-align: middle;'><?= __d('system', 'Name'); ?></th>
                <td style='text-align: left; vertical-align: middle;' width='75%'><?= $role->name; ?></td>
            </tr>
            <tr>
                <th style='text-align: left; vertical-align: middle;'><?= __d('system', 'Slug'); ?></th>
                <td style='text-align: left; vertical-align: middle;' width='75%'><?= $role->slug; ?></td>
            </tr>
            <tr>
                <th style='text-align: left; vertical-align: middle;'><?= __d('system', 'Description'); ?></th>
                <td style='text-align: left; vertical-align: middle;' width='75%'><?= $role->description; ?></td>
            </tr>
            <tr>
                <th style='text-align: left; vertical-align: middle;'><?= __d('system', 'Created At'); ?></th>
                <td style='text-align: left; vertical-align: middle;' width='75%'><?= $role->created_at->formatLocalized('%d %b %Y, %H:%M'); ?></td>
            </tr>
            <tr>
                <th style='text-align: left; vertical-align: middle;'><?= __d('system', 'Updated At'); ?></th>
                <td style='text-align: left; vertical-align: middle;' width='75%'><?= $role->updated_at->formatLocalized('%d %b %Y, %H:%M'); ?></td>
            <tr>
        </table>
    </div>
</div>

<a class='btn btn-primary' href='<?= site_url('admin/roles'); ?>'><?= __d('system', '<< Previous Page'); ?></a>

</section>
