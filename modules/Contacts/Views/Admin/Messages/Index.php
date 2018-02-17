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

<?php $deletables = 0; ?>
<?php if (! $messages->isEmpty()) { ?>

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('contacts', 'Submitted Messages'); ?></h3>
        <div class="box-tools">
        <?= $messages->links(); ?>
        </div>
    </div>
    <div class="box-body">
        <div class="text-center" style="padding: 5px;"><big><?= __d('contacts', '<b>{0}</b> message(s) was received by <b>{1}</b>.', $messages->getTotal(), $contact->name); ?></big></div>
    </div>
</div>

<?php foreach ($messages as $message) { ?>
<?php $deletables++; ?>

<div class="box box-widget">
    <div class="box-header">
        <h3 class="box-title"><?= $message->created_at->formatLocalized(__d('contacts', '%d %B %Y, %R')); ?></h3>
        <div class="box-tools">
            <div class="btn-group" role="group" aria-label="...">
                <a class="btn btn-sm btn-danger" href="#" data-toggle="modal" data-target="#modal-delete-dialog" data-id="<?= $message->id; ?>" title="<?= __d('contacts', 'Delete this Message'); ?>" role="button"><i class="fa fa-trash"></i></a>
                <a class="btn btn-sm btn-success" href="<?= site_url('admin/contacts/' .$contact->id .'/messages/' .$message->id .'?offset=' .Input::get('offset', 1)); ?>" title="<?= __d('contacts', 'View this Message'); ?>" role="button"><i class="fa fa-search"></i></a>
            </div>
        </div>
    </div>
    <div class="box-body no-padding">
        <table id="left" class="table table-hover responsive">
            <tr class="bg-navy disabled">
                <th style="text-align: left; vertical-align: middle;"><?= __d('contacts', 'Field'); ?></th>
                <th style="text-align: left; vertical-align: middle;"><?= __d('contacts', 'Value'); ?></th>
            </tr>
            <tr>
                <th style="text-align: left; vertical-align: middle;"><?= __d('contacts', 'Path'); ?></th>
                <td style="text-align: left; vertical-align: middle;" width="75%"><?= $message->contact_path; ?></td>
            <tr>
            <tr>
                <th style="text-align: left; vertical-align: middle;"><?= __d('contacts', 'Author IP'); ?></th>
                <td style="text-align: left; vertical-align: middle;" width="75%"><?= $message->contact_author_ip; ?></td>
            <tr>
            <?php
            foreach ($message->meta as $meta) {
                if (! Str::is('contact_*', $name = $meta->key) || ($name == 'contact_author_ip') || ($name == 'contact_path')) {
                    continue;
                }

                $value = $meta->value;

                if ('select' == Arr::get($elements, $name .'.type')) {
                    $value = Arr::get($elements, $value, $value);
                }

                $label = Arr::get($elements, $name .'.label', __d('contacts', 'Unknown'));
            ?>
            <tr>
                <th style="text-align: left; vertical-align: middle;"><?= $label; ?></th>
                <td style="text-align: left; vertical-align: middle;" width="75%"><?= nl2br(e($value)); ?></td>
            <tr>
            <?php } ?>
        </table>
    </div>
</div>

<?php } ?>

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

