<div class="row">
    <h1><?= __d('backend', 'Show Role'); ?></h1>
    <ol class="breadcrumb">
        <li><a href='<?= site_url('admin/dashboard'); ?>'> <?= __d('backend', 'Dashboard'); ?></a></li>
        <li><a href='<?= site_url('admin/roles'); ?>'><?= __d('backend', 'Roles'); ?></a></li>
        <li><?= __d('backend', 'Show Role'); ?></li>
    </ol>
</div>

<?= View::fetch('Partials/Messages'); ?>

<!-- Main content -->
<div class="row">
    <h4><?= __d('backend', 'User Role : <b>{0}</b>', $role->name); ?></h4>

    <table class='table table-bordered table-hover responsive'>
        <tr>
            <th style='text-align: left; vertical-align: middle;'><?= __d('backend', 'ID'); ?></th>
            <td style='text-align: left; vertical-align: middle;' width='70%'><?= $role->id; ?></td>
        <tr>
        <tr>
            <th style='text-align: left; vertical-align: middle;'><?= __d('backend', 'Name'); ?></th>
            <td style='text-align: left; vertical-align: middle;' width='75%'><?= $role->name; ?></td>
        </tr>
        <tr>
            <th style='text-align: left; vertical-align: middle;'><?= __d('backend', 'Slug'); ?></th>
            <td style='text-align: left; vertical-align: middle;' width='75%'><?= $role->slug; ?></td>
        </tr>
        <tr>
            <th style='text-align: left; vertical-align: middle;'><?= __d('backend', 'Description'); ?></th>
            <td style='text-align: left; vertical-align: middle;' width='75%'><?= $role->description; ?></td>
        </tr>
        <tr>
            <th style='text-align: left; vertical-align: middle;'><?= __d('backend', 'Created At'); ?></th>
            <td style='text-align: left; vertical-align: middle;' width='75%'><?= $role->created_at->formatLocalized('%d %b %Y, %H:%M'); ?></td>
        </tr>
        <tr>
            <th style='text-align: left; vertical-align: middle;'><?= __d('backend', 'Updated At'); ?></th>
            <td style='text-align: left; vertical-align: middle;' width='75%'><?= $role->updated_at->formatLocalized('%d %b %Y, %H:%M'); ?></td>
        <tr>
    </table>
</div>

<div class="row">
    <a class='btn btn-primary' href='<?= site_url('admin/roles'); ?>'><?= __d('backend', '<< Previous Page'); ?></a>
</div>
