<section class="content-header">
    <h1><?= __d('users', 'Edit User'); ?></h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('admin/dashboard'); ?>"><i class="fa fa-dashboard"></i> <?= __d('users', 'Dashboard'); ?></a></li>
        <li><a href="<?= site_url('admin/users'); ?>"><?= __d('users', 'Users'); ?></a></li>
        <li><?= __d('users', 'Edit User'); ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= View::fetch('Partials/Messages'); ?>

<form action="<?= site_url('admin/users/' .$user->id); ?>" class="form-horizontal" method='POST' enctype="multipart/form-data" role="form">

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('users', 'Edit the User Account : <b>{0}</b>', $user->username); ?></h3>
    </div>
    <div class="box-body">
        <div class="col-md-8 col-md-offset-2 col-sm-8 col-sm-offset-2">
            <h4><?= __d('users', 'Account'); ?></h4>
            <hr>
            <div class="form-group">
                <label class="col-sm-4 control-label" for="username"><?= __d('users', 'Username'); ?> <font color="#CC0000">*</font></label>
                <div class="col-sm-8">
                    <input name="username" id="username" type="text" class="form-control" value="<?= Input::old('username', $user->username); ?>" placeholder="<?= __d('users', 'Username'); ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label" for="password"><?= __d('users', 'Password'); ?> <font color="#CC0000">*</font></label>
                <div class="col-sm-8">
                    <input name="password" id="password" type="password" class="form-control" value="" placeholder="<?= __d('users', 'Password'); ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label" for="password_confirmation"><?= __d('users', 'Confirm Password'); ?> <font color="#CC0000">*</font></label>
                <div class="col-sm-8">
                    <input name="password_confirmation" id="password_confirmation" type="password" class="form-control" value="" placeholder="<?= __d('users', 'Password confirmation'); ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label" for="role"><?= __d('users', 'Roles'); ?> <font color="#CC0000">*</font></label>
                <div class="col-sm-8">
                    <?php $optRoles = Input::old('roles', $user->roles->lists('id')); ?>
                    <select name="roles[]" id="roles" class="form-control select2" multiple="multiple" data-placeholder="<?= __d('users', 'Select a Role'); ?>" style="width: 100%;">
                        <?php foreach ($roles as $role) { ?>
                        <option value="<?= $role->id ?>" <?= in_array($role->id, $optRoles) ? 'selected' : ''; ?>><?= $role->name; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label" for="realname"><?= __d('users', 'Name and Surname'); ?> <font color="#CC0000">*</font></label>
                <div class="col-sm-8">
                    <input name="realname" id="first-name" type="text" class="form-control" value="<?= Input::old('realname', $user->realname); ?>" placeholder="<?= __d('users', 'Name and Surname'); ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label" for="email"><?= __d('users', 'E-mail'); ?> <font color="#CC0000">*</font></label>
                <div class="col-sm-8">
                    <input name="email" id="email" type="text" class="form-control" value="<?= Input::old('email', $user->email); ?>" placeholder="<?= __d('users', 'E-mail'); ?>">
                </div>
            </div>
            <div class="clearfix"></div>
            <h4><?= __d('users', 'Profile'); ?></h4>
            <hr>
            <?= View::fetch('Modules/Users::Partials/Fields/Edit', compact('user', 'items')); ?>

            <div class="clearfix"></div>
            <div class="form-group">
                <label class="col-sm-4 control-label" for="realname"><?= __d('users', 'Profile Picture'); ?> <font color="#CC0000">*</font></label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <input type="text" class="form-control" readonly>
                        <label class="input-group-btn">
                            <span class="btn btn-primary">
                                <?= __d('users', 'Browse ...'); ?> <input type="file" name="image" style="display: none;">
                            </span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            <br>
            <font color="#CC0000">*</font><?= __d('users', 'Required field'); ?>
            <hr>
            <div class="form-group">
                <div class="col-sm-12">
                    <input type="submit" name="submit" class="btn btn-success col-sm-3 pull-right" value="<?= __d('users', 'Save'); ?>">
                </div>
            </div>
        </div>
    </div>
</div>

<?= csrf_field(); ?>

<input type="hidden" name="userId" value="<?= $user->id; ?>" />

</form>

<a class="btn btn-primary col-sm-2" href="<?= site_url('admin/users'); ?>"><?= __d('users', '<< Previous Page'); ?></a>

<div class="clearfix"></div>
<br>

</section>

<script type="text/javascript">

$(function() {

    // We can attach the `fileselect` event to all file inputs on the page
    $(document).on('change', ':file', function() {
        var input = $(this),
            numFiles = input.get(0).files ? input.get(0).files.length : 1,
            label = input.val().replace(/\\/g, '/').replace(/.*\//, '');

        if (input.get(0).files) {
            var items = [];

            var files = input.get(0).files;

            for (var i = 0, file; file = files[i]; i++) {
                items.push(file.name);
            }

            label = items.join(', ');
        }

        input.trigger('fileselect', [numFiles, label]);
    });

    // We can watch for our custom `fileselect` event like this
    $(document).ready( function() {
        $(':file').on('fileselect', function(event, numFiles, label) {
            var input = $(this).parents('.input-group').find(':text'),
                log = (numFiles > 1) ? sprintf("<?= __d('users', '%d files selected'); ?>", numFiles) : label;

            if (input.length) {
                input.val(label);
            } else {
                if (log) alert(log);
            }
      });
  });

});

</script>
