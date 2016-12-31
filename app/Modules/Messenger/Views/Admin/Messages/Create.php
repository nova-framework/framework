<section class="content-header">
    <h1><?= __d('messenger', 'Create Message'); ?></h1>
    <ol class="breadcrumb">
        <li><a href='<?= site_url('admin/dashboard'); ?>'><i class="fa fa-dashboard"></i> <?= __d('messenger', 'Dashboard'); ?></a></li>
        <li><a href='<?= site_url('admin/messages'); ?>'><?= __d('messenger', 'Messages'); ?></a></li>
        <li><?= __d('messenger', 'Create Message'); ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= Session::getMessages(); ?>

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('messenger', 'Create a new Message'); ?></h3>
    </div>
    <div class="box-body">
        <div class="col-md-8 col-md-offset-2 col-sm-8 col-sm-offset-2">
            <form class="form-horizontal" action="<?= site_url('admin/messages'); ?>" method='POST' role="form">

            <div class="form-group">
                <label class="col-sm-2 control-label" for="subject"><?= __d('messenger', 'Subject'); ?> <font color='#CC0000'>*</font></label>
                <div class="col-sm-10">
                    <input name="subject" id="subject" type="text" class="form-control" value="<?= Input::old('subject'); ?>" placeholder="<?= __d('messenger', 'Subject'); ?>">
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label" for="message"><?= __d('messenger', 'Message'); ?> <font color='#CC0000'>*</font></label>
                <div class="col-sm-10">
                    <textarea name="message" id="message" class="form-control" rows="10" placeholder="<?= __d('messenger', 'Write a Message'); ?>"><?= Input::old('message'); ?></textarea>
                </div>
            </div>
            <?php if (! $users->isEmpty()) { ?>

            <div class="form-group">
                <label class="col-sm-2 control-label" for="recipients"><?= __d('messenger', 'Recipients'); ?> <font color='#CC0000'>*</font></label>
                <div class="col-sm-10">
                <select name="recipients[]" id="recipients" class="form-control select2" multiple="multiple" data-placeholder="<?= __d('messenger', 'Select a Recipient'); ?>">
                <?php $recipients = Input::old('recipients', array()); ?>
                <?php foreach ($users as $user) { ?>
                    <option value="<?= $user->id ?>" <?php if (in_array($user->id, $recipients)) echo 'selected'; ?>><?= $user->username; ?></option>
                <?php } ?>
                </select>
                </div>
            </div>

            <?php } ?>
            <div class="clearfix"></div>
            <br>
            <font color='#CC0000'>*</font><?= __d('messenger', 'Required field'); ?>
            <hr>
            <div class="form-group">
                <div class="col-sm-12">
                    <input type="submit" name="submit" class="btn btn-success col-sm-3 pull-right" value="<?= __d('messenger', 'Save'); ?>">
                </div>
            </div>

            <input type="hidden" name="_token" value="<?= csrf_token(); ?>" />

            </form>
        </div>
    </div>
</div>

<a class='btn btn-primary' href='<?= site_url('admin/messages'); ?>'><?= __d('messenger', '<< Previous Page'); ?></a>

</section>
