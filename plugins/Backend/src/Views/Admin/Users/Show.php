<div class="row">
    <h1><?= __d('backend', 'Show User'); ?></h1>
    <ol class="breadcrumb">
        <li><a href='<?= site_url('admin/dashboard'); ?>'><?= __d('backend', 'Dashboard'); ?></a></li>
        <li><a href='<?= site_url('admin/users'); ?>'><?= __d('backend', 'Users'); ?></a></li>
        <li><?= __d('backend', 'Show User'); ?></li>
    </ol>
</div>

<?= View::fetch('Partials/Messages'); ?>

<!-- Main content -->
<div class="row">
    <h4><?= __d('backend', 'User Account : <b>{0}</b>', $user->username); ?></h4>

    <table class='table table-bordered table-hover table-hover responsive'>
        <tr>
            <th style='text-align: left; vertical-align: middle;'><?= __d('backend', 'ID'); ?></th>
            <td style='text-align: left; vertical-align: middle;' width='70%'><?= $user->id; ?></td>
        <tr>
        <tr>
            <th style='text-align: left; vertical-align: middle;'><?= __d('backend', 'Username'); ?></th>
            <td style='text-align: left; vertical-align: middle;' width='75%'><?= $user->username; ?></td>
        </tr>
        <tr>
            <th style='text-align: left; vertical-align: middle;'><?= __d('backend', 'Role'); ?></th>
            <td style='text-align: left; vertical-align: middle;' width='75%'><?= $user->role->name; ?></td>
        </tr>
        <tr>
            <th style='text-align: left; vertical-align: middle;'><?= __d('backend', 'Name and Surname'); ?></th>
            <td style='text-align: left; vertical-align: middle;' width='75%'><?= $user->first_name; ?> <?= $user->last_name; ?></td>
        </tr>
        <tr>
            <th style='text-align: left; vertical-align: middle;'><?= __d('backend', 'E-mail'); ?></th>
            <td style='text-align: left; vertical-align: middle;' width='75%'><?= $user->email; ?></td>
        </tr>
        <tr>
            <th style='text-align: left; vertical-align: middle;'><?= __d('backend', 'Created At'); ?></th>
            <td style='text-align: left; vertical-align: middle;' width='75%'><?= $user->created_at->formatLocalized('%d %b %Y, %H:%M'); ?></td>
        </tr>
        <tr>
            <th style='text-align: left; vertical-align: middle;'><?= __d('backend', 'Updated At'); ?></th>
            <td style='text-align: left; vertical-align: middle;' width='75%'><?= $user->updated_at->formatLocalized('%d %b %Y, %H:%M'); ?></td>
        <tr>
    </table>
</div>

<div class="row">
    <a class='btn btn-primary' href='<?= site_url('admin/users'); ?>'><?= __d('backend', '<< Previous Page'); ?></a>
</div>
