<section class="content-header">
    <h1><?= __d('platform', 'Account'); ?></h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('dashboard'); ?>"><i class="fa fa-dashboard"></i> <?= __d('platform', 'Dashboard'); ?></a></li>
        <li><?= __d('platform', 'Account'); ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= Session::getMessages(); ?>

<div class="box box-default">
    <div class="box-header">
        <h3 class="box-title"><?= __d('platform', 'User Account'); ?></h3>
    </div>
    <div class="box-body no-padding">
        <table id="left" class="table table-hover responsive">
            <tr class="bg-navy disabled">
                <th style="text-align: left; vertical-align: middle;"><?= __d('platform', 'Field'); ?></th>
                <th style="text-align: left; vertical-align: middle;"><?= __d('platform', 'Value'); ?></th>
            </tr>
            <tr>
                <th style="text-align: left; vertical-align: middle;"><?= __d('platform', 'Username'); ?></th>
                <td style="text-align: left; vertical-align: middle;" width="75%"><?= $user->username; ?></td>
            </tr>
            <tr>
                <th style="text-align: left; vertical-align: middle;"><?= __d('platform', 'E-mail'); ?></th>
                <td style="text-align: left; vertical-align: middle;" width="75%"><?= $user->email; ?></td>
            </tr>
            <tr>
                <th style="text-align: left; vertical-align: middle;"><?= __d('platform', 'Roles'); ?></th>
                <td style="text-align: left; vertical-align: middle;" width="75%"><?= implode(', ', $user->roles->lists('name')); ?></td>
            </tr>
            <tr>
                <th style="text-align: left; vertical-align: middle;"><?= __d('platform', 'Created At'); ?></th>
                <td style="text-align: left; vertical-align: middle;" width="75%"><?= $user->created_at->formatLocalized(__d('platform', '%d %b %Y, %R')); ?></td>
            </tr>
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

<form action="<?= site_url('account'); ?>" class="form-horizontal" method='POST' enctype="multipart/form-data" role="form">

<div  class="box box-widget">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('platform', 'Update the Account information'); ?></h3>
    </div>

    <div class="box-body">
        <div class="col-md-8 col-md-offset-2 col-sm-8 col-sm-offset-2">
            <h4><?= __d('platform', 'Account'); ?></h4>
            <hr>
            <div class="form-group">
                <label class="col-sm-4 control-label" for="name"><?= __d('platform', 'Current Password'); ?> <font color="#CC0000">*</font></label>
                <div class="col-sm-8">
                    <input name="current_password" id="current_password" type="password" class="form-control" value="" placeholder="<?= __d('platform', 'Insert the current Password'); ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label" for="name"><?= __d('platform', 'New Password'); ?></label>
                <div class="col-sm-8">
                    <input name="password" id="password" type="password" class="form-control" value="" placeholder="<?= __d('platform', 'Insert the new Password'); ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label" for="name"><?= __d('platform', 'Confirm Password'); ?></label>
                <div class="col-sm-8">
                    <input name="password_confirmation" id="password_confirmation" type="password" class="form-control" value="" placeholder="<?= __d('platform', 'Verify the new Password'); ?>">
                </div>
            </div>
            <div class="clearfix"></div>
            <br>
            <h4><?= __d('platform', 'Profile'); ?></h4>
            <hr>
            <?= $fields; ?>
            <div class="clearfix"></div>
            <br>
            <font color="#CC0000">*</font><?= __d('platform', 'Required field'); ?>
        </div>
    </div>
    <div class="box-footer">
        <input type="submit" name="submit" class="btn btn-success col-sm-2 pull-right" value="<?= __d('platform', 'Save'); ?>">
    </div>
</div>

<?= csrf_field(); ?>

</form>

</section>
