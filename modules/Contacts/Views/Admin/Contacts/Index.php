<section class="content-header">
    <h1><?= $title; ?></h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('admin/dashboard'); ?>"><i class="fa fa-dashboard"></i> <?= __d('contacts', 'Dashboard'); ?></a></li>
        <li><?= $title; ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= View::fetch('Partials/Messages'); ?>

<div class="box box-default">
    <div class="box-header">
        <h3 class="box-title"><?= __d('contacts', 'All Contacts'); ?></h3>
        <div class="box-tools">
        <?= $contacts->links(); ?>
        </div>
    </div>
    <div class="box-body no-padding">
        <?php $deletables = 0; ?>
        <?php if (! $contacts->isEmpty()) { ?>
        <table id="left" class="table table-striped table-hover responsive">
            <tr class="bg-navy disabled">
                <th style="text-align: center; vertical-align: middle;"><?= __d('contacts', 'ID'); ?></th>
                <th style="text-align: left; vertical-align: middle;"><?= __d('contacts', 'Name'); ?></th>
                <th style="text-align: left; vertical-align: middle;"><?= __d('contacts', 'E-mail'); ?></th>
                <th style="text-align: left; vertical-align: middle;"><?= __d('contacts', 'Paths'); ?></th>
                <th style="text-align: center; vertical-align: middle;"><?= __d('contacts', 'Messages'); ?></th>
                <th style="text-align: right; vertical-align: middle;"><?= __d('contacts', 'Operations'); ?></th>
            </tr>
            <?php foreach ($contacts as $contact) { ?>
            <tr>
                <td style="text-align: center; vertical-align: middle;" width="5%"><?= $contact->id; ?></td>
                <td style="text-align: left; vertical-align: middle;" title="<?= $contact->description ?: __d('contacts', 'No description'); ?>" width="20%"><?= $contact->name; ?></td>
                <td style="text-align: left; vertical-align: middle;" width="25%"><?= implode(', ', array_filter(array_map('trim', explode("\n", $contact->email)))); ?></td>
                <td style="text-align: left; vertical-align: middle;" width="25%"><?= $contact->path ?: '*'; ?></td>
                <td style="text-align: center; vertical-align: middle;" width="10%"><?= $contact->count; ?></td>
                <td style="text-align: right; vertical-align: middle;" width="20%">
                    <a class="btn btn-sm btn-primary" href="<?= site_url('admin/contacts/' .$contact->id .'/messages'); ?>" title="<?= __d('contacts', 'View the Messages received by this Contact'); ?>" role="button"><i class="fa fa-envelope"></i></a>
                    <div class="btn-group" role="group" aria-label="...">
                        <a class="btn btn-sm btn-danger" href="#" data-toggle="modal" data-target="#modal-delete-dialog" data-id="<?= $contact->id; ?>" title="<?= __d('contacts', 'Delete this Contact'); ?>" role="button"><i class="fa fa-remove"></i></a>
                        <a class="btn btn-sm btn-success" href="<?= site_url('admin/contacts/' .$contact->id .'/edit'); ?>" title="<?= __d('contacts', 'Edit this Contact'); ?>" role="button"><i class="fa fa-pencil"></i></a>
                        <a class="btn btn-sm btn-warning" href="<?= site_url('admin/contacts/' .$contact->id); ?>" title="<?= __d('contacts', 'View this Contact'); ?>" role="button"><i class="fa fa-search"></i></a>
                   </div>
                </td>
            </tr>
            <?php } ?>
        </table>
        <?php } else { ?>
        <div class="alert alert-warning" style="margin: 0 5px 5px;">
            <h4><i class="icon fa fa-warning"></i> <?= strftime("%d %b %Y, %R", time()) ." - "; ?> <?= __d('contacts', 'No registered Contacts'); ?></h4>
            <?= __d('contacts', 'There are no registered Contacts.'); ?>
        </div>
        <?php } ?>
    </div>
</div>

</section>

<div id="modal-delete-dialog" class="modal modal-default fade" tabindex="-1" role="dialog" aria-labelledby="...">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-label="Close" data-dismiss="modal" class="close" type="button">
                <span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><?= __d('contacts', 'Delete this Contact?'); ?></h4>
            </div>
            <div class="modal-body">
                <p><?= __d('contacts', 'Are you sure you want to remove this Contact, the operation being irreversible?'); ?></p>
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

        $('#modal-delete-form').attr('action', '<?= site_url("admin/contacts/"); ?>/' + id + '/destroy');
    });
});

</script>
