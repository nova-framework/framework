<section class="content-header">
    <h1><?= $title; ?></h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('admin/dashboard'); ?>"><i class="fa fa-dashboard"></i> <?= __d('content', 'Dashboard'); ?></a></li>
        <li><a href="<?= site_url('admin/content/' .$postType->label()); ?>"><?= $postType->label('items'); ?></a></li>
        <li><?= __d('content', '{0} revisions', $name); ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= View::fetch('Partials/Messages'); ?>

<div class="box box-default">
    <div class="box-header">
        <h3 class="box-title"><?= __d('content', 'Revisions'); ?></h3>
        <div class="box-tools">
        <?= $revisions->links(); ?>
        </div>
    </div>
    <div class="box-body no-padding">
       <?php $deletables = $restorables = 0; ?>
        <?php if (! $revisions->isEmpty()) { ?>
        <table id="left" class="table table-striped table-hover responsive">
            <tr class="bg-navy disabled">
                <th style="text-align: center; vertical-align: middle;"><?= __d('content', 'Revision'); ?></th>
                <th style="text-align: left; vertical-align: middle;"><?= __d('content', 'Title'); ?></th>
                <th style="text-align: center; vertical-align: middle;"><?= __d('content', 'Created By'); ?></th>
                <th style="text-align: center; vertical-align: middle;"><?= __d('content', 'Created At'); ?></th>
                <th style="text-align: right; vertical-align: middle;"><?= __d('content', 'Operations'); ?></th>
            </tr>
            <?php foreach ($revisions as $revision) { ?>
            <?php $deletables++; ?>
            <?php $restorables++; ?>
            <?php preg_match('#^(?:\d+)-revision-v(\d+)$#', $revision->name, $matches); ?>
            <tr>
                <td style="text-align: center; vertical-align: middle;" width="10%"><?= $version = $matches[1]; ?></td>
                <td style="text-align: left; vertical-align: middle;" width="40%"><?= $revision->title ?: __d('content', 'Untitled'); ?></td>
                <td style="text-align: center; vertical-align: middle;" width="20%"><?= $revision->author->username; ?></td>
                <td style="text-align: center; vertical-align: middle;" width="15%"><?= $revision->created_at->formatLocalized(__d('content', '%d %b %Y, %R')); ?></td>
                <td style="text-align: right; vertical-align: middle;" width="15%">
                    <div class="btn-group" role="group" aria-label="...">
                        <a class="btn btn-sm btn-danger" href="#" data-toggle="modal" data-target="#modal-delete-revision-dialog" data-id="<?= $revision->id; ?>" title="<?= __d('content', 'Delete this revision'); ?>" role="button"><i class="fa fa-remove"></i></a>
                        <a class="btn btn-sm btn-success" href="#" data-toggle="modal" data-target="#modal-restore-revision-dialog" data-id="<?= $revision->id; ?>" data-version="<?= $version; ?>" title="<?= __d('content', 'Restore this revision'); ?>" role="button"><i class="fa fa-repeat"></i></a>
                        <a class="btn btn-sm btn-warning" href="<?= site_url('content/' .$revision->slug); ?>" title="<?= __d('content', 'View this revision'); ?>" target="_blank" role="button"><i class="fa fa-search"></i></a>
                    </div>
                </td>
            </tr>
            <?php } ?>
        </table>
        <?php } else { ?>
        <div class="alert alert-warning" style="margin: 0 5px 5px;">
            <h4><i class="icon fa fa-warning"></i> <?= strftime("%d %b %Y, %R", time()) ." - "; ?> <?= __d('content', 'No revisions'); ?></h4>
            <?= __d('content', 'The {0} <b>{1}</b> has no revisions.', $name, $post->title); ?>
        </div>
        <?php } ?>
    </div>
</div>

<a class="btn btn-primary col-sm-2" href="<?= site_url('admin/content/' .$post->id .'/edit'); ?>"><?= __d('content', '<< Previous Page'); ?></a>

<div class="clearfix"></div>

</section>

<?php if ($deletables > 0) { ?>

<div class="modal modal-default" id="modal-delete-revision-dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-label="<?= __d('content', 'Close'); ?>" data-dismiss="modal" class="close" type="button">
                <span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><?= __d('content', 'Delete this {0} revision?', $name); ?></h4>
            </div>
            <div class="modal-body">
                <p><?= __d('content', 'Are you sure you want to remove this {0} revision, the operation being irreversible?', $name); ?></p>
                <p><?= __d('content', 'Please click the button <b>Delete</b> to proceed, or <b>Cancel</b> to abandon the operation.'); ?></p>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-primary pull-left col-md-3" type="button"><?= __d('content', 'Cancel'); ?></button>
                <form id="modal-delete-revision-form" action="" method="POST">
                    <input type="hidden" name="_token" value="<?= csrf_token(); ?>" />
                    <input type="submit" name="button" class="btn btn btn-danger pull-right col-md-3" value="<?= __d('content', 'Delete'); ?>">
                </form>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
</div>

<script>

$(function () {
    $('#modal-delete-revision-dialog').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal

        $('#modal-delete-revision-form').attr('action', '<?= site_url("admin/content"); ?>/' + button.data('id') + '/destroy');
    });
});

</script>

<?php } ?>

<?php if ($restorables > 0) { ?>

<div class="modal modal-default" id="modal-restore-revision-dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-label="<?= __d('content', 'Close'); ?>" data-dismiss="modal" class="close" type="button">
                <span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><?= __d('content', 'Restore this {0} revision?', $name); ?></h4>
            </div>
            <div class="modal-body">
                <p class="question"><?= __d('content', 'Are you sure you want to restore this {0} revision?', $name); ?></p>
                <p><?= __d('content', 'Please click the button <b>Restore</b> to proceed, or <b>Cancel</b> to abandon the operation.'); ?></p>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-primary pull-left col-md-3" type="button"><?= __d('content', 'Cancel'); ?></button>
                <form id="modal-restore-revision-form" action="" method="POST">
                    <input type="hidden" name="_token" value="<?= csrf_token(); ?>" />
                    <input type="submit" name="button" class="btn btn btn-danger pull-right col-md-3" value="<?= __d('content', 'Restore'); ?>">
                </form>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
</div>

<script>

$(function () {
    $('#modal-restore-revision-dialog').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal

        var question = sprintf("<?= __d('content', 'Are you sure you want to restore the {0} to the revision <b>#%s</b> ?', $name); ?>", button.data('version'));

        $('#modal-restore-revision-dialog').find('.question').html(question);

        $('#modal-restore-revision-form').attr('action', '<?= site_url("admin/content"); ?>/' + button.data('id') + '/restore');
    });
});

</script>

<?php } ?>
