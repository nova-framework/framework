<div class='row-responsive'>
    <h2>User Profile : <?= $user->realname; ?></h2>
    <hr>
</div>

<div class="row">
    <?php echo Errors::display($error); ?>
    <?php echo Session::message('message'); ?>
    <div class="clearfix"></div>

    <div class="col-md-4 col-md-offset-4">
        <div class="login-panel panel panel-primary" style="margin-top:40px">
            <div class="panel-heading text-center">
                <h3 class="panel-title">Change Password</h3>
            </div>
            <div class="panel-body">
                <form style="margin: 0;" method="post">
                    <div class="form-control-container" style="margin-bottom: 10px;">
                        <input type="password" class="input-medium input-block-level form-control" name="password" placeholder="Insert the current Password" title="Insert the current Password">
                    </div>
                    <div class="form-control-container" style="margin-bottom: 10px;">
                        <input type="password" class="input-medium input-block-level form-control" name="newPassword" placeholder="Insert the new Password" title="Insert the new Password">
                    </div>
                    <div class="form-control-container" style="margin-bottom: 10px;">
                        <input type="password" class="input-medium input-block-level form-control" name="confirmPass" placeholder="Verify the new Password" title="Verify the new Password">
                    </div>
                    <hr>
                    <div>
                        <button type="submit" class="btn btn-success col-lg-6 pull-right"><i class='fa fa-check'></i> Save</button>
                    </div>
                    <div class="clearfix"></div>
                    <input type="hidden" name="csrfToken" value="<?= $csrfToken; ?>" />
                </form>
            </div>
        </div>
    </div>
</div>
