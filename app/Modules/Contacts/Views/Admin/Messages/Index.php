<section class="content-header">
    <h1><?= $title; ?></h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('admin/dashboard'); ?>"><i class="fa fa-dashboard"></i> <?= __d('contacts', 'Dashboard'); ?></a></li>
        <li><?= __d('contacts', 'Messages'); ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= Session::getMessages(); ?>

<div class="box box-default">
    <div class="box-header">
        <h3 class="box-title"><?= __d('contacts', 'Submitted Messages'); ?></h3>
        <div class="box-tools">
        <?= $messages->links(); ?>
        </div>
    </div>
    <div class="box-body no-padding">
        <?php $deletables = 0; ?>
        <?php if (! $messages->isEmpty()) { ?>
        <table id="left" class="table table-striped table-hover responsive">
            <tr class="bg-navy disabled">
                <th style="text-align: left; vertical-align: middle;"><?= __d('contacts', 'Author'); ?></th>
                <th style="text-align: left; vertical-align: middle;"><?= __d('contacts', 'Message'); ?></th>
                <th style="text-align: center; vertical-align: middle;"><?= __d('contacts', 'Submitted On'); ?></th>
                <th style="text-align: right; vertical-align: middle;"><?= __d('contacts', 'Operations'); ?></th>
            </tr>
            <?php foreach ($messages as $message) { ?>
            <?php $deletables++; ?>
            <tr>
                <td style="text-align: left; vertical-align: top;" width="20%">
                    <div style="padding-bottom: 5px;">
                        <a style="font-weight: bold;" href="<?= site_url('admin/contacts/' .$message->id .'/edit'); ?>"><?= $message->contact_author; ?></a>
                    </div>
                    <div style="padding-bottom: 5px;">
                        <a href="mailto:<?= $message->author_email; ?>"><?= $message->contact_author_email; ?></a>
                    </div>
                    <div style="padding-bottom: 5px; font-weight: bold;"><?= $message->contact_author_ip; ?></div>
                </td>
                <td style="text-align: left; vertical-align: top;" width="55%"><?= nl2br($message->contact_message); ?></td>
                <td style="text-align: center; vertical-align: top;" width="15%"><?= $message->created_at->formatLocalized(__d('contacts', '%d %b %Y, %R')); ?></td>
                <td style="text-align: right; vertical-align: top; padding-bottom: 5px 5px 30px 5px;" width="10%">
                    <a class="btn btn-xs btn-danger btn-block" href="#" data-toggle="modal" data-target="#modal-delete-dialog" data-id="<?= $message->id; ?>" title="<?= __d('contacts', 'Delete this Message'); ?>" role="button"><i class="fa fa-remove"></i> <?= __d('contacts', 'Delete'); ?></a>
                </td>
            </tr>
            <?php } ?>
        </table>
        <?php } else { ?>
        <div class="alert alert-warning" style="margin: 0 5px 5px;">
            <h4><i class="icon fa fa-warning"></i> <?= strftime("%d %b %Y, %R", time()) ." - "; ?> <?= __d('contacts', 'No Messages'); ?></h4>
            <?= __d('contacts', 'No message has been added yet!'); ?>
        </div>
        <?php } ?>
    </div>
</div>

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

