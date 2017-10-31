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
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('platform', 'Account Details'); ?></h3>
    </div>
    <div class="box-body no-padding">
        <table id="left" class="table table-hover responsive">
            <tr>
                <th style="text-align: left; vertical-align: middle;"><?= __d('platform', 'Username'); ?></th>
                <td style="text-align: left; vertical-align: middle;" width="75%"><?= $user->username; ?></td>
            </tr>
            <tr>
                <th style="text-align: left; vertical-align: middle;"><?= __d('platform', 'Roles'); ?></th>
                <td style="text-align: left; vertical-align: middle;" width="75%"><?= implode(', ', $user->roles->lists('name')); ?></td>
            </tr>
            <tr>
                <th style="text-align: left; vertical-align: middle;"><?= __d('platform', 'Name and Surname'); ?></th>
                <td style="text-align: left; vertical-align: middle;" width="75%"><?= $user->realname; ?></td>
            </tr>
            <tr>
                <th style="text-align: left; vertical-align: middle;"><?= __d('platform', 'E-mail'); ?></th>
                <td style="text-align: left; vertical-align: middle;" width="75%"><?= $user->email; ?></td>
            </tr>
            <tr>
                <th style="text-align: left; vertical-align: middle;"><?= __d('platform', 'Created At'); ?></th>
                <td style="text-align: left; vertical-align: middle;" width="75%"><?= $user->created_at->formatLocalized(__d('platform', '%d %b %Y, %R')); ?></td>
            </tr>
        </table>
    </div>
</div>

<form action="<?= site_url('account/picture'); ?>" class="form-horizontal" method='POST' enctype="multipart/form-data" role="form">

<div  class="box box-widget">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('platform', 'Profile Picture'); ?></h3>
    </div>
    <div class="box-body">
        <div class="col-md-4">
            <img src="<?= $user->picture() ?>" class="img-thumbnail img-responsive" alt="Profile Image" style="margin-bottom: 0; max-height: 200px; width:auto;">
        </div>
        <div class="col-md-8">
            <div class="form-group">
                <label class="col-sm-4 control-label" for="image"><?= __d('platform', 'Profile Picture (to change)'); ?></label>
                <div class="input-group">
                    <input type="text" id="file_path" class="form-control" placeholder="Browse..." readonly>
                    <span class="input-group-btn">
                        <button class="btn btn-primary" type="button" id="file_browser">
                        <i class="fa fa-search"></i> Browse</button>
                    </span>
                </div>
                <input type="file" class="hidden" id="image" name="image">
            </div>
        </div>
    </div>
    <div class="box-footer">
        <input type="submit" name="submit" class="btn btn-success col-sm-2 pull-right" value="<?= __d('platform', 'Update'); ?>">
    </div>
</div>

<?= csrf_field(); ?>

</form>

<form action="<?= site_url('account'); ?>" class="form-horizontal" method='POST' role="form">

<div  class="box box-widget">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('platform', 'Change Password'); ?></h3>
    </div>

    <div class="box-body">
        <div class="col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
            <div class="form-group">
                <label class="col-sm-4 control-label" for="name"><?= __d('platform', 'Current Password'); ?> <font color="#CC0000">*</font></label>
                <div class="col-sm-8">
                    <input name="current_password" id="current_password" type="password" class="form-control" value="" placeholder="<?= __d('platform', 'Insert the current Password'); ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label" for="name"><?= __d('platform', 'New Password'); ?> <font color="#CC0000">*</font></label>
                <div class="col-sm-8">
                    <input name="password" id="password" type="password" class="form-control" value="" placeholder="<?= __d('platform', 'Insert the new Password'); ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label" for="name"><?= __d('platform', 'Confirm Password'); ?> <font color="#CC0000">*</font></label>
                <div class="col-sm-8">
                    <input name="password_confirmation" id="password_confirmation" type="password" class="form-control" value="" placeholder="<?= __d('platform', 'Verify the new Password'); ?>">
                </div>
            </div>
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

<script>

$('#file_browser').click(function(e) {
    e.preventDefault();

    $('#image').click();
});

$('#image').change(function() {
    $('#file_path').val($(this).val());
});

$('#file_path').click(function() {
    $('#file_browser').click();
});

</script>
