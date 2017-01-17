<section class="content-header">
    <h1><?= __d('video_chat', 'Video Chat'); ?></h1>
    <ol class="breadcrumb">
        <li><a href='<?= site_url('admin/dashboard'); ?>'><i class="fa fa-dashboard"></i> <?= __d('video_chat', 'Dashboard'); ?></a></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= Session::getMessages(); ?>

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('video_chat', 'Open a Video Chat'); ?></h3>
    </div>
    <div class="box-body">
        <div class="col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
            <div class="clearfix"></div>
            <br>

            <form class="form-horizontal" action="<?= site_url('admin/chat/video'); ?>" method="POST" role="form">

            <div class="form-group">
                <label class="col-sm-3 control-label" for="user"><?= __d('video_chat', 'Chat with'); ?> <font color='#CC0000'>*</font></label>
                <div class="col-sm-9">
                    <?php $opt_user = Input::old('user'); ?>
                    <select name="userId" id="userId" class="form-control select2">
                        <option value="" <?php if (empty($opt_user)) echo 'selected'; ?>>- <?= __d('video_chat', 'Choose a User'); ?> -</option>
                        <?php foreach ($users as $user) { ?>
                        <option value="<?= $user->id ?>" <?php if ($opt_user == $user->id) echo 'selected'; ?>><?= $user->username; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="clearfix"></div>
            <br>
            <font color='#CC0000'>*</font><?= __d('video_chat', 'Required field'); ?>
            <hr>
            <div class="form-group">
                <div class="col-sm-12">
                    <button type="submit" class="btn btn-success col-sm-3 pull-right"><i class='fa fa-comments'></i> <?= __d('video_chat', 'Open the Chat'); ?></button>
                </div>
            </div>

            <input type="hidden" name="_token" value="<?= csrf_token(); ?>">

            </form>
        </div>
    </div>
</div>

</section>
