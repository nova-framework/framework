<section class="content-header">
    <h1><?= $title; ?></h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('admin/dashboard'); ?>"><i class="fa fa-dashboard"></i> <?= __d('contacts', 'Dashboard'); ?></a></li>
        <li><?= $title; ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= Session::getMessages(); ?>

<div class="row">

<div class="col-md-3">

<form id="page-form" action="<?= site_url('admin/contacts'); ?>" method='POST' role="form">

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('contacts', 'Create a new Contact'); ?></h3>
    </div>
    <div class="box-body">
        <div class="form-group">
            <label class="control-label" for="name"><?= __d('contacts', 'Name'); ?></label>
            <input name="name" id="name" type="text" class="form-control" value="<?= Input::old('name'); ?>" placeholder="<?= __d('contacts', 'Name'); ?>">
        </div>
        <div class="form-group">
            <label class="control-label" for="email"><?= __d('contacts', 'E-mail'); ?></label>
            <input name="email" id="email" type="text" class="form-control" value="<?= Input::old('email'); ?>" placeholder="<?= __d('contacts', 'E-mail'); ?>">
        </div>
        <div class="form-group">
            <label class="control-label" for="path"><?= __d('contacts', 'Paths'); ?></label>
            <textarea name="path" id="path" class="form-control" rows="4" style="resize: none;" placeholder="<?= __d('contacts', 'Paths'); ?>"><?= Input::old('path'); ?></textarea>
        </div>
        <div class="form-group" style=" margin-bottom: 0;">
            <label class="control-label" for="description"><?= __d('contacts', 'Description'); ?></label>
            <textarea name="description" id="description" class="form-control" rows="4" style="resize: none;" placeholder="<?= __d('contacts', 'Description'); ?>"><?= Input::old('description'); ?></textarea>
        </div>
    </div>
    <div class="box-footer">
        <input type="submit" name="submit"  class="btn btn-success col-sm-6 pull-right" value="<?= __d('contacts', 'Add new Contact'); ?>" />
    </div>
</div>

<input type="hidden" name="_token"   value="<?= csrf_token(); ?>" />

</form>

</div>

<div class="col-md-9">

<div class="box box-default">
    <div class="box-header">
        <h3 class="box-title"><?= __d('contacts', 'The registered {0}', $title); ?></h3>
        <div class="box-tools">
        <?= $contacts->links(); ?>
        </div>
    </div>
    <div class="box-body no-padding">
        <?php $deletables = 0; ?>
        <?php if (! $contacts->isEmpty()) { ?>
        <table id="left" class="table table-striped table-hover responsive">
            <tr class="bg-navy disabled">
                <th style="text-align: left; vertical-align: middle;"><?= __d('contacts', 'Name'); ?></th>
                <th style="text-align: left; vertical-align: middle;"><?= __d('contacts', 'E-mail'); ?></th>
                <th style="text-align: left; vertical-align: middle;"><?= __d('contacts', 'Paths'); ?></th>
                <th style="text-align: center; vertical-align: middle;"><?= __d('contacts', 'Messages'); ?></th>
                <th style="text-align: right; vertical-align: middle;"><?= __d('contacts', 'Operations'); ?></th>
            </tr>
            <?php foreach ($contacts as $contact) { ?>
            <tr>
                <td style="text-align: left; vertical-align: middle;" title="<?= $contact->description ?: __d('contacts', 'No description'); ?>" width="20%"><?= $contact->name; ?></td>
                <td style="text-align: left; vertical-align: middle;" width="25%"><?= implode(', ', array_filter(array_map('trim', explode("\n", $contact->email)), 'is_not_empty')); ?></td>
                <td style="text-align: left; vertical-align: middle;" width="25%"><?= $contact->path ?: '*'; ?></td>
                <td style="text-align: center; vertical-align: middle;" width="10%"><?= $contact->message_count; ?></td>
                <td style="text-align: right; vertical-align: middle;" width="20%">
                    <div class="btn-group" role="group" aria-label="...">
                        <a class="btn btn-sm btn-danger" href="#" data-toggle="modal" data-target="#modal-delete-dialog" data-id="<?= $contact->id; ?>" title="<?= __d('contacts', 'Delete this Contact'); ?>" role="button"><i class="fa fa-remove"></i></a>
                        <a class="btn btn-sm btn-success" href="#" data-toggle="modal" data-target="#modal-edit-dialog" data-id="<?= $contact->id; ?>" data-name="<?= $contact->name; ?>" data-email="<?= $contact->email; ?>" data-description="<?= $contact->description; ?>" data-path="<?= $contact->path; ?>" title="<?= __d('contacts', 'Edit this Contact'); ?>" role="button"><i class="fa fa-pencil"></i></a>
                        <a class="btn btn-sm btn-warning" href="<?= site_url('admin/contacts/' .$contact->id); ?>" title="<?= __d('contacts', 'View the Messages received by this Contact'); ?>" role="button"><i class="fa fa-search"></i></a>
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

</div>

</div>

</section>

<div id="modal-edit-dialog" class="modal modal-default fade" tabindex="-1" role="dialog" aria-labelledby="...">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="modal-edit-form" class="form-horizontal" action="" method='POST' role="form">

            <div class="modal-header">
                <button aria-label="Close" data-dismiss="modal" class="close" type="button">
                <span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><?= __d('contacts', 'Edit or create a new Contact'); ?></h4>
            </div>
            <div class="modal-body">
                <div class="col-md-12">

                <div class="form-group">
                    <label class="control-label" for="name"><?= __d('contacts', 'Name'); ?></label>
                    <input name="name" id="modal-edit-name" type="text" class="form-control" value="" placeholder="<?= __d('contacts', 'Name'); ?>">
                </div>
                <div class="form-group">
                    <label class="control-label" for="email"><?= __d('contacts', 'E-mail'); ?></label>
                    <input name="email" id="modal-edit-email" type="text" class="form-control" value="" placeholder="<?= __d('contacts', 'E-mail'); ?>">
                </div>
                <div class="form-group">
                    <label class="control-label" for="path"><?= __d('contacts', 'Paths'); ?></label>
                    <textarea name="path" id="modal-edit-path" class="form-control" rows="4" style="resize: none;" placeholder="<?= __d('contacts', 'Paths'); ?>"></textarea>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="control-label" for="description"><?= __d('contacts', 'Description'); ?></label>
                    <textarea name="description" id="modal-edit-description" class="form-control" rows="4" style="resize: none;" placeholder="<?= __d('contacts', 'Description'); ?>"></textarea>
                </div>

                </div>

                <div class="clearfix"></div>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" aria-hidden="true" class="btn btn-primary col-md-3"><?= __d('contacts', 'Cancel'); ?></button>
                <input type="submit" name="button" class="update-item-button btn btn-success col-md-3 pull-right" value="<?= __d('contacts', 'Save'); ?>" />
            </div>

            <input type="hidden" name="_token" value="<?= csrf_token(); ?>" />

            </form>
        </div>
    </div>
</div>

<script>

$(function () {
    $('#modal-edit-dialog').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal

        var id    = button.data('id');
        var name  = button.data('name');
        var email = button.data('email');
        var text  = button.data('description');
        var path  = button.data('path');

        $('#modal-edit-name').val(name);
        $('#modal-edit-email').val(email);
        $('#modal-edit-description').val(text);
        $('#modal-edit-path').val(path);

        // The title.
        var title = sprintf("<?= __d('contacts', 'Edit the Contact Item : <b>%s</b>'); ?>", name);

        $('.modal-edit-title').html(title);

        // The form action.
        $('#modal-edit-form').attr('action', '<?= site_url("admin/contacts"); ?>/' + id);
    });
});

</script>

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
