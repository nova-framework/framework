<section class="content-header">
    <h1><?= $title; ?></h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('admin/dashboard'); ?>"><i class="fa fa-dashboard"></i> <?= __d('contacts', 'Dashboard'); ?></a></li>
        <li><a href="<?= site_url('admin/contacts'); ?>"><?= __d('contacts', 'Contacts'); ?></a></li>
        <li><?= __d('contacts', 'Manage the Field Groups'); ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= View::fetch('Partials/Messages'); ?>

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('contacts', 'Manage the Field Groups'); ?></h3>
    </div>
    <div class="box-body">
        <a class="btn btn-success col-sm-2 pull-right" href="#" data-toggle="modal" data-target="#modal-edit-group-dialog" title="<?= __d('contacts', 'Create a new Fields Group'); ?>" role="button"><?= __d('contacts', 'Create a new Field Group'); ?></a>
    </div>
</div>

<?php if (! $contact->fieldGroups->isEmpty()) { ?>

<?php foreach ($contact->fieldGroups->sortBy('order') as $group) { ?>

<div class="box box-default">
    <div class="box-header">
        <h3 class="box-title"><?= __d('contacts', 'Field Group : <b>{0}</b>', $group->title); ?></h3>
        <div class="box-tools">
            <div class="btn-group" role="group" aria-label="...">
                <a class="btn btn-sm btn-danger" href="#" data-toggle="modal" data-target="#modal-delete-group-dialog" data-id="<?= $group->id; ?>" title="<?= __d('contacts', 'Delete this Fields Group'); ?>" role="button"><i class="fa fa-remove"></i> </a>
                <a class="btn btn-sm btn-success" href="#" data-toggle="modal" data-target="#modal-edit-group-dialog" data-id="<?= $group->id; ?>" data-title="<?= $group->title; ?>" data-content="<?= $group->content; ?>" data-order="<?= $group->order; ?>" title="<?= __d('contacts', 'Edit this Fields Group'); ?>" role="button"><i class="fa fa-pencil"></i></a>
            </div>
        </div>
    </div>
    <div class="box-body no-padding">
        <?php if (! $group->fieldItems->isEmpty()) { ?>
        <table id="left" class="table table-striped table-hover responsive">
            <tr class="bg-navy disabled">
                <th style="text-align: center; vertical-align: middle;"><?= __d('roles', 'ID'); ?></th>
                <th style="text-align: center; vertical-align: middle;"><?= __d('roles', 'Label'); ?></th>
                <th style="text-align: center; vertical-align: middle;"><?= __d('roles', 'Name'); ?></th>
                <th style="text-align: center; vertical-align: middle;"><?= __d('roles', 'Type'); ?></th>
                <th style="text-align: center; vertical-align: middle;"><?= __d('roles', 'Order'); ?></th>
                <th style="text-align: left; vertical-align: middle;"><?= __d('roles', 'Rules'); ?></th>
                <th style="text-align: center; vertical-align: middle;"><?= __d('roles', 'Visible'); ?></th>
                <th style="text-align: right; vertical-align: middle;"><?= __d('roles', 'Operations'); ?></th>
            </tr>
            <?php foreach ($group->fieldItems as $item) { ?>
            <tr>
                <td style="text-align: center; vertical-align: middle;" width="5%"><?= $item->id; ?></td>
                <td style="text-align: center; vertical-align: middle;" width="15%"><?= $item->title; ?></td>
                <td style="text-align: center; vertical-align: middle;" width="10%"><?= $item->name; ?></td>
                <td style="text-align: center; vertical-align: middle;" width="10%"><?= $item->type; ?></td>
                <td style="text-align: center; vertical-align: middle;" width="5%"><?= $item->order; ?></td>
                <td style="text-align: left; vertical-align: middle;" width="25%"><?= $item->rules ?: '-'; ?></td>
                <td style="text-align: center; vertical-align: middle;" width="5%"><?= ($item->visible == 1) ? __d('contacts', 'Yes') : __d('contacts', 'No'); ?></td>
                <td style="text-align: right; vertical-align: middle;" width="15%">
                    <div class="btn-group" role="group" aria-label="...">
                        <a class="btn btn-sm btn-danger" href="#" data-toggle="modal" data-target="#modal-delete-item-dialog" data-group-id="<?= $group->id; ?>" data-id="<?= $item->id; ?>" title="<?= __d('roles', 'Delete this Role'); ?>" role="button"><i class="fa fa-remove"></i></a>
                        <a class="btn btn-sm btn-success" href="<?= site_url('admin/contacts/field-groups/{0}/items/{1}/edit', $group->id, $item->id); ?>" title="<?= __d('roles', 'Edit this Role'); ?>" role="button"><i class="fa fa-pencil"></i></a>
                        <a class="btn btn-sm btn-warning" href="<?= site_url('admin/contacts/field-groups/{0}/items/{1}', $group->id, $item->id); ?>" title="<?= __d('roles', 'Show the Details'); ?>" role="button"><i class="fa fa-search"></i></a>
                    </div>
                </td>
            </tr>
            <?php } ?>
        </table>
        <?php } else { ?>
        <div class="alert alert-warning" style="margin: 0 5px 5px;">
            <h4><i class="icon fa fa-warning"></i> <?= strftime("%d %b %Y, %R", time()) ." - "; ?> <?= __d('contacts', 'No registered Fields'); ?></h4>
            <?= __d('contacts', 'There are no registered Fields for this Group.'); ?>
        </div>
        <?php } ?>
    </div>
    <div class="box-footer">
        <div class="pull-left" style="padding: 7px 10px 0 10px;">
            <?= __d('contacts', 'Group Order: <b>{0}</b>', $group->order); ?>
        </div>
        <a class="btn btn-success col-sm-2 pull-right" href="<?= site_url('admin/contacts/field-groups/{0}/items/create', $group->id); ?>">
            <?= __d('contacts', 'Create a new Field Item'); ?>
        </a>
    </div>
</div>

<?php } ?>

<?php } else { ?>

<div class="box box-widget">
    <div class="box-header">
        <h3 class="box-title"><?= __d('contacts', 'No Field Groups'); ?></h3>
    </div>
    <div class="box-body no-padding">
        <div class="alert alert-warning" style="margin: 0 5px 5px;">
            <h4><i class="icon fa fa-warning"></i> <?= strftime("%d %b %Y, %R", time()) ." - "; ?> <?= __d('contacts', 'No registered Field Groups'); ?></h4>
            <?= __d('contacts', 'There are no registered Field Groups for this Contact.'); ?>
        </div>
    </div>
</div>

<?php } ?>


<a class="btn btn-primary col-sm-2" href="<?= site_url('admin/contacts'); ?>"><?= __d('contacts', '<< Previous Page'); ?></a>

<div class="clearfix"></div>
<br>

</section>

<div class="modal modal-default" id="modal-delete-group-dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-label="<?= __d('roles', 'Close'); ?>" data-dismiss="modal" class="close" type="button">
                <span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><?= __d('roles', 'Delete this Field Group?'); ?></h4>
            </div>
            <div class="modal-body">
                <p><?= __d('roles', 'Are you sure you want to remove this Field Group, the operation being irreversible?'); ?></p>
                <p><?= __d('roles', 'Please click the button <b>Delete</b> to proceed, or <b>Cancel</b> to abandon the operation.'); ?></p>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-primary pull-left col-md-3" type="button"><?= __d('roles', 'Cancel'); ?></button>
                <form id="modal-delete-group-form" action="" method="POST">
                    <input type="hidden" name="id" id="delete-field-group-id" value="0" />
                    <input type="hidden" name="contact_id" value="<?= $contact->id; ?>" />
                    <input type="hidden" name="_token" value="<?= csrf_token(); ?>" />
                    <input type="submit" name="button" class="btn btn btn-danger pull-right col-md-3" value="<?= __d('roles', 'Delete'); ?>">
                </form>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
</div>

<script>

$(function () {
    $('#modal-delete-group-dialog').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal

        var id  = button.data('id');

        //
        $('#delete-field-group-id').val(id);

        $('#modal-delete-group-form').attr('action', '<?= site_url("admin/contacts/{0}/field-groups", $contact->id); ?>/' + id + '/destroy');
    });
});

</script>

<div class="modal modal-default" id="modal-delete-item-dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-label="<?= __d('roles', 'Close'); ?>" data-dismiss="modal" class="close" type="button">
                <span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><?= __d('roles', 'Delete this Field Item?'); ?></h4>
            </div>
            <div class="modal-body">
                <p><?= __d('roles', 'Are you sure you want to remove this Field Item, the operation being irreversible?'); ?></p>
                <p><?= __d('roles', 'Please click the button <b>Delete</b> to proceed, or <b>Cancel</b> to abandon the operation.'); ?></p>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-primary pull-left col-md-3" type="button"><?= __d('roles', 'Cancel'); ?></button>
                <form id="modal-delete-item-form" action="" method="POST">
                    <input type="hidden" name="id" id="delete-field-item-id" value="0" />
                    <input type="hidden" name="contact_id" value="<?= $contact->id; ?>" />
                    <input type="hidden" name="_token" value="<?= csrf_token(); ?>" />
                    <input type="submit" name="button" class="btn btn btn-danger pull-right col-md-3" value="<?= __d('roles', 'Delete'); ?>">
                </form>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
</div>

<script>

$(function () {
    $('#modal-delete-item-dialog').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal

        var gid = button.data('group-id');

        var id  = button.data('id');

        //
        $('#delete-field-item-id').val(id);

        $('#modal-delete-item-form').attr('action', '<?= site_url("admin/contacts/field-groups"); ?>/' + gid + '/items/' + id + '/destroy');
    });
});

</script>

<div class="modal modal-default" id="modal-edit-group-dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="modal-edit-form" class="form-horizontal" action="" method='POST' role="form">

            <div class="modal-header" style="padding: 10px;">
                <button aria-label="<?= __d('records', 'Close'); ?>" data-dismiss="modal" class="close" type="button">
                <span aria-hidden="true">×</span></button>
                <h4 class="modal-edit-title" style="margin: 0;"><?= __d('records', 'Create a new Field Group'); ?></h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="title"><?= __d('contacts', 'Label'); ?> <font color="#CC0000">*</font></label>
                    <div class="col-sm-9">
                        <input name="title" id="modal-edit-group-title" type="text" class="form-control" value="" placeholder="<?= __d('contacts', 'Label'); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="content"><?= __d('contacts', 'Content'); ?> <font color="#CC0000">*</font></label>
                    <div class="col-sm-9">
                        <textarea name="content" id="modal-edit-group-content" class="form-control" rows="10" style="resize: none;" placeholder="<?= __d('contacts', 'Content'); ?>"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="order"><?= __d('contacts', 'Order'); ?> <font color="#CC0000">*</font></label>
                    <div class="col-sm-2">
                        <input name="order" id="modal-edit-group-order" type="number" class="form-control" min="0" max="1000" value="0" style="padding: 6px 3px 6px 12px;" autocomplete="off">
                    </div>
                </div>
                <div class="clearfix"></div>
                <br>
                <font color="#CC0000">*</font><?= __d('contacts', 'Required field'); ?>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="id" id="modal-edit-group-id" value="0" />
                <input type="hidden" name="contact_id" value="<?= $contact->id; ?>" />
                <input type="hidden" name="_token" value="<?= csrf_token(); ?>" />
                <button data-dismiss="modal" class="btn btn-primary pull-left col-md-3" type="button"><?= __d('contacts', 'Cancel'); ?></button>
                <input type="submit" name="button" class="btn btn btn-success pull-right col-md-3" value="<?= __d('contacts', 'Save'); ?>">
            </div>

            <input type="hidden" name="_token" value="<?= csrf_token(); ?>" />
            <input type="hidden" name="contact_id" value="<?= $contact->id; ?>" />

            </form>
        </div>
        <!-- /.modal-content -->
    </div>
</div>

<script>

$(function () {
    $('#modal-edit-group-dialog').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal

        var id = parseInt(button.data('id'));

        var action = '<?= site_url("admin/contacts/{0}/field-groups", $contact->id); ?>';

        if (id > 0) {
            var title   = button.data('title');
            var content = button.data('content');
            var order   = button.data('order');

            $('#modal-edit-group-title').val(title);
            $('#modal-edit-group-content').val(content);
            $('#modal-edit-group-order').val(order);

            $('#modal-edit-group-id').val(id);

            // Adjust the dialog title.
            var title = sprintf("<?= __d('contacts', 'Edit the Field Group : #%d'); ?>", id);

            $('.modal-edit-title').html(title);

            // Adjust the form action.
            var action = '<?= site_url("admin/contacts/{0}/field-groups", $contact->id); ?>/' + id + '/update';
        }

        $('#modal-edit-form').attr('action', action);
    });

    $("#modal-edit-group-dialog").on('hidden.bs.modal', function () {
        $('.modal-edit-iframe').attr('action', '');
    });
});

</script>

