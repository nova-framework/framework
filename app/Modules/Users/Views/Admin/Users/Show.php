<section class="content-header">
    <h1><?= __d('users', 'Show User'); ?></h1>
    <ol class="breadcrumb">
        <li><a href='<?= site_url('admin/dashboard'); ?>'><i class="fa fa-dashboard"></i> <?= __d('users', 'Dashboard'); ?></a></li>
        <li><a href='<?= site_url('admin/users'); ?>'><?= __d('users', 'Users'); ?></a></li>
        <li><?= __d('users', 'Show User'); ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= Session::getMessages(); ?>

<div class="box box-widget">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('users', 'User Account : <b>{0}</b>', $user->username); ?></h3>
    </div>
    <div class="box-body no-padding">
        <table id='left' class='table table-hover responsive'>
            <tr>
                <th style='text-align: left; vertical-align: middle;'><?= __d('users', 'ID'); ?></th>
                <td style='text-align: left; vertical-align: middle;' width='70%'><?= $user->id; ?></td>
            <tr>
            <tr>
                <th style='text-align: left; vertical-align: middle;'><?= __d('users', 'Username'); ?></th>
                <td style='text-align: left; vertical-align: middle;' width='75%'><?= $user->username; ?></td>
            </tr>
            <tr>
                <th style='text-align: left; vertical-align: middle;'><?= __d('users', 'Role'); ?></th>
                <td style='text-align: left; vertical-align: middle;' width='75%'><?= $user->role->name; ?></td>
            </tr>
            <tr>
                <th style='text-align: left; vertical-align: middle;'><?= __d('users', 'Name and Surname'); ?></th>
                <td style='text-align: left; vertical-align: middle;' width='75%'><?= $user->realname; ?></td>
            </tr>
            <tr>
                <th style='text-align: left; vertical-align: middle;'><?= __d('users', 'E-mail'); ?></th>
                <td style='text-align: left; vertical-align: middle;' width='75%'><?= $user->email; ?></td>
            </tr>
            <tr>
                <th style='text-align: left; vertical-align: middle;'><?= __d('users', 'Created At'); ?></th>
                <td style='text-align: left; vertical-align: middle;' width='75%'><?= $user->created_at->formatLocalized(__d('users', '%d %b %Y, %R')); ?></td>
            </tr>
            <tr>
                <th style='text-align: left; vertical-align: middle;'><?= __d('users', 'Updated At'); ?></th>
                <td style='text-align: left; vertical-align: middle;' width='75%'><?= $user->updated_at->formatLocalized(__d('users', '%d %b %Y, %R')); ?></td>
            <tr>
        </table>
    </div>
</div>

<a class='btn btn-primary' href='<?= site_url('admin/users'); ?>'><?= __d('users', '<< Previous Page'); ?></a>

</section>
