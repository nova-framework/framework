<section class="content-header">
    <h1><?= __d('users', 'Show User'); ?></h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('admin/dashboard'); ?>"><i class="fa fa-dashboard"></i> <?= __d('users', 'Dashboard'); ?></a></li>
        <li><a href="<?= site_url('admin/users'); ?>"><?= __d('users', 'Users'); ?></a></li>
        <li><?= __d('users', 'Show User'); ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= Session::getMessages(); ?>

<div class="box box-widget">
    <div class="box-header">
        <h3 class="box-title"><?= __d('users', 'User Account'); ?></h3>
    </div>
    <div class="box-body no-padding">
        <table id="left" class="table table-hover responsive">
            <tr class="bg-navy disabled">
                <th style="text-align: left; vertical-align: middle;"><?= __d('users', 'Field'); ?></th>
                <th style="text-align: left; vertical-align: middle;"><?= __d('users', 'Value'); ?></th>
            </tr>
            <tr>
                <th style="text-align: left; vertical-align: middle;"><?= __d('users', 'ID'); ?></th>
                <td style="text-align: left; vertical-align: middle;" width="75%"><?= $user->id; ?></td>
            <tr>
            <tr>
                <th style="text-align: left; vertical-align: middle;"><?= __d('users', 'Username'); ?></th>
                <td style="text-align: left; vertical-align: middle;" width="75%"><?= $user->username; ?></td>
            </tr>
            <tr>
                <th style="text-align: left; vertical-align: middle;"><?= __d('users', 'Roles'); ?></th>
                <td style="text-align: left; vertical-align: middle;" width="75%"><?= implode(', ', $user->roles->lists('name')); ?></td>
            </tr>
            <tr>
                <th style="text-align: left; vertical-align: middle;"><?= __d('users', 'E-mail'); ?></th>
                <td style="text-align: left; vertical-align: middle;" width="75%"><?= $user->email; ?></td>
            </tr>
            <tr>
                <th style="text-align: left; vertical-align: middle;"><?= __d('users', 'Created At'); ?></th>
                <td style="text-align: left; vertical-align: middle;" width="75%"><?= $user->created_at->formatLocalized(__d('users', '%d %b %Y, %R')); ?></td>
            </tr>
            <tr>
                <th style="text-align: left; vertical-align: middle;"><?= __d('users', 'Updated At'); ?></th>
                <td style="text-align: left; vertical-align: middle;" width="75%"><?= $user->updated_at->formatLocalized(__d('users', '%d %b %Y, %R')); ?></td>
            <tr>
        </table>
    </div>
</div>

<div class="box box-widget">
    <div class="box-header">
        <h3 class="box-title"><?= __d('users', 'User Profile'); ?></h3>
    </div>
    <div class="box-body no-padding">
        <table id="left" class="table table-hover responsive">
            <tr class="bg-navy disabled">
                <th style="text-align: left; vertical-align: middle;"><?= __d('users', 'Field'); ?></th>
                <th style="text-align: left; vertical-align: middle;"><?= __d('users', 'Value'); ?></th>
            </tr>
            <?php foreach ($user->profile->fields as $field) { ?>
            <?php if ($field->hidden === 1) continue; ?>
            <?php if (! is_null($key = $user->meta->findItem($field->key))) { ?>
            <?php $item = $user->meta->get($key); ?>
            <tr>
                <th style="text-align: left; vertical-align: middle;"><?= $field->name; ?></th>
                <td style="text-align: left; vertical-align: middle;" width="75%"><?= $item->render(); ?></td>
            </tr>
            <?php } ?>
            <?php } ?>
        </table>
    </div>
</div>

<a class="btn btn-primary col-sm-2" href="<?= site_url('admin/users'); ?>"><?= __d('users', '<< Previous Page'); ?></a>

<div class="clearfix"></div>
<br>

</section>
