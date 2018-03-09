<section class="content-header">
    <h1><?= $title; ?></h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('admin/dashboard'); ?>"><i class="fa fa-dashboard"></i> <?= __d('contacts', 'Dashboard'); ?></a></li>
        <li><?= __d('contacts', 'Messages'); ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= View::fetch('Partials/Messages'); ?>

<?php $deletables = 0; ?>

<?php if (! $messages->isEmpty()) { ?>

<div class="box box-default">
    <div class="box-header">
        <h3 class="box-title"><?= __d('contacts', 'Messages');; ?></h3>
        <div class="box-tools">
        <?= $messages->links(); ?>
        </div>
    </div>
    <div class="box-body no-padding">
        <table id="left" class="table table-hover responsive">
            <tr class="bg-navy disabled">
                <th style="text-align: center; vertical-align: middle;"><?= __d('contacts', 'ID'); ?></th>
                <th style="text-align: center; vertical-align: middle;"><?= __d('contacts', 'Author'); ?></th>
                <th style="text-align: left; vertical-align: middle;"><?= __d('contacts', 'Subject'); ?></th>
                <th style="text-align: center; vertical-align: middle;"><?= __d('contacts', 'Attachments'); ?></th>
                <th style="text-align: center; vertical-align: middle;"><?= __d('contacts', 'Submitted On'); ?></th>
                <th style="text-align: right; vertical-align: middle;"><?= __d('contacts', 'Operations'); ?></th>
            </tr>
            <?php foreach ($messages as $message) { ?>
            <?php $deletables++; ?>
            <tr>
                <td style="text-align: center; vertical-align: middle;" width="5%"><?= $message->id; ?></td>
                <td style="text-align: center; vertical-align: middle;" width="30%"><?= e($message->author); ?> &lt;<?= e($message->author_email); ?>&gt;</td>
                <td style="text-align: left; vertical-align: middle;" width="30%" title="<?= e($message->subject); ?>"><?= Str::limit(e($message->subject), 60); ?></td>
                <td style="text-align: center; vertical-align: middle;" width="10%"><?= $message->attachments->count(); ?></td>
                <td style="text-align: center; vertical-align: middle;" width="15%"><?= $message->created_at->formatLocalized(__d('contacts', '%d %b %Y, %R')); ?></td>
                <td style="text-align: right; vertical-align: middle;" width="15%">
                    <div class="btn-group" role="group" aria-label="...">
                        <a class="btn btn-sm btn-danger" href="#" data-toggle="modal" data-target="#modal-delete-dialog" data-id="<?= $message->id; ?>" title="<?= __d('contacts', 'Delete this Message'); ?>" role="button"><i class="fa fa-trash"></i></a>
                        <a class="btn btn-sm btn-success" href="<?= site_url('admin/contacts/' .$contact->id .'/messages/' .$message->id .'?offset=' .Input::get('offset', 1)); ?>" title="<?= __d('contacts', 'View this Message'); ?>" role="button"><i class="fa fa-search"></i></a>
                    </div>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>
</div>

<?php } else { ?>

<div class="box box-default">
    <div class="box-header">
        <h3 class="box-title"><?= __d('contacts', 'Submitted Messages'); ?></h3>
    </div>
    <div class="box-body no-padding">
        <div class="alert alert-warning" style="margin: 0 5px 5px;">
            <h4><i class="icon fa fa-warning"></i> <?= strftime("%d %b %Y, %R", time()) ." - "; ?> <?= __d('contacts', 'No Messages'); ?></h4>
            <?= __d('contacts', 'No message has been added yet!'); ?>
        </div>
    </div>
</div>

<?php } ?>

<a class="btn btn-primary col-sm-2" href="<?= site_url('admin/contacts'); ?>"><?= __d('contacts', '<< Previous Page'); ?></a>

<div class="clearfix"></div>
<br>

</section>

<?php if ($deletables > 0) { ?>

<div id="modal-delete-dialog" class="modal modal-default fade" tabindex="-1" role="dialog" aria-labelledby="...">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-label="Close" data-dismiss="modal" class="close" type="button">
                <span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><?= __d('contacts', 'Delete this Message?'); ?></h4>
            </div>
            <div class="modal-body">
                <p><?= __d('contacts', 'Are you sure you want to remove this Message, the operation being irreversible?'); ?></p>
                <p><?= __d('contacts', 'Please click the button <b>Delete</b> to proceed, or <b>Cancel</b> to abandon the operation.'); ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" aria-hidden="true" class="btn btn-primary col-md-3"><?= __d('contacts', 'Cancel'); ?></button>
                <form id="modal-delete-form" action="" method="POST">
                    <input type="hidden" name="_token" value="<?= csrf_token(); ?>" />
                    <button type="submit" name="button" class="btn btn-danger col-md-3 pull-right"><?= __d('contacts', 'Delete'); ?></button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>

$(function() {
    // The Modal Delete dialog.
    $('#modal-delete-dialog').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal

        var id = button.data('id');

        $('#modal-delete-form').attr('action', '<?= site_url("admin/contacts/" .$contact->id); ?>/messages/' + id);
    });
});

</script>

<?php } ?>
