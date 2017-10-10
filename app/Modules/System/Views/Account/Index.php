<section class="content-header">
    <h1><?= __d('system', 'Account'); ?></h1>
    <ol class="breadcrumb">
        <li><a href='<?= site_url('dashboard'); ?>'><i class="fa fa-dashboard"></i> <?= __d('system', 'Dashboard'); ?></a></li>
        <li><?= __d('system', 'Account'); ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= Session::getMessages(); ?>

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('users', 'Account Details'); ?></h3>
    </div>
    <div class="box-body no-padding">
        <table id='left' class='table table-hover responsive'>
            <tr>
                <th style='text-align: left; vertical-align: middle;'><?= __d('users', 'Username'); ?></th>
                <td style='text-align: left; vertical-align: middle;' width='75%'><?= $user->username; ?></td>
            </tr>
            <tr>
                <th style='text-align: left; vertical-align: middle;'><?= __d('users', 'Roles'); ?></th>
                <td style='text-align: left; vertical-align: middle;' width='75%'><?= implode(', ', $user->roles->lists('name')); ?></td>
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
        </table>
    </div>
</div>

<form action="<?= site_url('account/picture'); ?>" class="form-horizontal" method='POST' enctype="multipart/form-data" role="form">

<div  class="box box-widget">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('system', 'Profile Picture'); ?></h3>
    </div>
    <div class="box-body">
        <div class="col-md-4">
            <img src="<?= $user->picture() ?>" class="img-thumbnail img-responsive" alt="Profile Image" style="margin-bottom: 0; max-height: 200px; width:auto;">
        </div>
        <div class="col-md-8">
            <div class="form-group">
                <label class="col-sm-4 control-label" for="image"><?= __d('system', 'Profile Picture (to change)'); ?></label>
                <div class="input-group">
                    <input type="text" id="file_path" class="form-control" placeholder="Browse...">
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
        <input type="submit" name="submit" class="btn btn-success col-sm-2 pull-right" value="<?= __d('system', 'Update'); ?>">
    </div>
</div>

<input type="hidden" name="csrfToken" value="<?= $csrfToken; ?>" />

</form>

<form action="<?= site_url('account'); ?>" class="form-horizontal" method='POST' role="form">

<div  class="box box-widget">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('system', 'Change Password'); ?></h3>
    </div>

    <div class="box-body">
        <div class="col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
            <div class="form-group">
                <label class="col-sm-4 control-label" for="name"><?= __d('system', 'Current Password'); ?> <font color='#CC0000'>*</font></label>
                <div class="col-sm-8">
                    <input name="current_password" id="current_password" type="password" class="form-control" value="" placeholder="<?= __d('system', 'Insert the current Password'); ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label" for="name"><?= __d('system', 'New Password'); ?> <font color='#CC0000'>*</font></label>
                <div class="col-sm-8">
                    <input name="password" id="password" type="password" class="form-control" value="" placeholder="<?= __d('system', 'Insert the new Password'); ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label" for="name"><?= __d('system', 'Confirm Password'); ?> <font color='#CC0000'>*</font></label>
                <div class="col-sm-8">
                    <input name="password_confirmation" id="password_confirmation" type="password" class="form-control" value="" placeholder="<?= __d('system', 'Verify the new Password'); ?>">
                </div>
            </div>
            <div class="clearfix"></div>
            <br>
            <font color='#CC0000'>*</font><?= __d('system', 'Required field'); ?>
        </div>
    </div>
    <div class="box-footer">
        <input type="submit" name="submit" class="btn btn-success col-sm-2 pull-right" value="<?= __d('system', 'Save'); ?>">
    </div>
</div>

<input type="hidden" name="csrfToken" value="<?= $csrfToken; ?>" />

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
