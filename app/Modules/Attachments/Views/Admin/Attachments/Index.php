<section class="content-header">
    <h1><?= __d('attachments', 'Attachments'); ?></h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('admin/dashboard'); ?>"><i class="fa fa-dashboard"></i> <?= __d('attachments', 'Dashboard'); ?></a></li>
        <li><?= __d('attachments', 'Attachments'); ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= Session::getMessages(); ?>

<form id="attachable-form"  action="<?= site_url('admin/attachments'); ?>" method="POST" role="form">

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('attachments', 'The Attachable\'s Form'); ?></h3>
    </div>
    <div class="box-body">
        <p class="text-center text-danger" style="padding-top: 10px;"><strong><?= __d('attachments', 'There will be dragons!'); ?></strong></p>
    </div>
    <div class="box-footer">
            <input type="submit" class="btn btn-success col-sm-2 pull-right submit-button" value="<?= __d('attachments', 'Send'); ?>">
    </div>
</div>

<?= csrf_field(); ?>

</form>

<?= View::fetch('Partials/Attachments', $attachments, 'Attachments'); ?>

</section>
