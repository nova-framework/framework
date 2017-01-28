<section class="content-header" style="margin: 0 15px; padding-bottom: 15px; border-bottom: 1px solid #FFF;">
    <h1><?= __d('web_chat', 'Video Chat'); ?></h1>
    <ol class="breadcrumb">
        <li><a href='<?= site_url('admin/dashboard'); ?>'><i class="fa fa-dashboard"></i> <?= __d('web_chat', 'Dashboard'); ?></a></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= Session::getMessages(); ?>

<div class="row">
    <div class="col-md-6 col-md-offset-3 col-sm-12">

    <form class="form-horizontal" action="<?= site_url('admin/chat/video'); ?>" method="POST" role="form">

        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title"><?= __d('web_chat', 'Open a Video Chat'); ?></h3>
            </div>
            <div class="box-body">
                <div class="clearfix"></div>
                <br>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="user"><?= __d('web_chat', 'Chat with'); ?> <font color='#CC0000'>*</font></label>
                    <div class="col-sm-9">
                        <?php $opt_user = Input::old('user'); ?>
                        <select name="userId" id="userId" class="form-control select2">
                            <option value="" <?php if (empty($opt_user)) echo 'selected'; ?>>- <?= __d('web_chat', 'Choose a User'); ?> -</option>
                            <?php foreach ($users as $user) { ?>
                            <option value="<?= $user->id ?>" <?php if ($opt_user == $user->id) echo 'selected'; ?>><?= $user->username; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="clearfix"></div>
                <br>
                <font color='#CC0000'>*</font><?= __d('web_chat', 'Required field'); ?>
            </div>
            <div class="box-footer with-border">
                <button type="submit" class="btn btn-success col-sm-3 pull-right"><i class='fa fa-comments'></i> <?= __d('web_chat', 'Open the Chat'); ?></button>
            </div>
        </div>

        <input type="hidden" name="_token" value="<?= csrf_token(); ?>">

        </form>
    </div>
</div>

</section>
