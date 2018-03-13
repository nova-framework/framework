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

<form id="create-group-form" class="form-horizontal" action="<?= site_url('admin/contacts/' .$contact->id .'/field-groups'); ?>" method='POST' role="form">

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('contacts', 'Create a new Field Group'); ?></h3>
    </div>
    <div class="box-body">
        <div class="form-group clearfix" style="margin: 0;">
            <label class="col-sm-1 control-label" for="title"><?= __d('contacts', 'Title'); ?></label>
            <div class="col-sm-6">
                <input name="title" id="title" type="text" class="form-control" value="<?= Input::old('title'); ?>" placeholder="<?= __d('contacts', 'Group Title'); ?>">
            </div>
            <label class="col-sm-1 control-label" for="title"><?= __d('contacts', 'Order'); ?></label>
            <div class="col-sm-1">
                <input name="order" id="order" type="number" class="form-control" min="-100" max="100" value="<?= Input::old('order', 0); ?>">
            </div>
            <div class="col-sm-3" style="padding: 0;">
                <input type="submit" name="submit"  class="btn btn-success col-sm-8 pull-right" value="<?= __d('contacts', 'Create a new Field Group'); ?>" />
            </div>
        </div>
    </div>
</div>

<input type="hidden" name="_token"     value="<?= csrf_token(); ?>" />
<input type="hidden" name="contact_id" value="<?= $contact->id; ?>" />

</form>

<?php if (! $contact->fieldGroups->isEmpty()) { ?>

<?php foreach ($contact->fieldGroups as $group) { ?>

<div class="box box-default">
    <div class="box-header">
        <h3 class="box-title"><?= __d('contacts', 'Field Group: <b>{0}</b>', $group->title); ?></h3>
        <div class="box-tools">
            <div class="btn-group" role="group" aria-label="...">
                <a class="btn btn-sm btn-danger" href="#" data-toggle="modal" data-target="#modal-delete-group-dialog" data-id="<?= $group->id; ?>" title="<?= __d('contacts', 'Delete this Fields Group'); ?>" role="button"><i class="fa fa-remove"></i></a>
                <a class="btn btn-sm btn-success" href="#" title="<?= __d('contacts', 'Edit this Fields Group'); ?>" role="button"><i class="fa fa-pencil"></i></a>
                <a class="btn btn-sm btn-warning" href="#" title="<?= __d('contacts', 'View this Fields Group'); ?>" role="button"><i class="fa fa-search"></i></a>
            </div>
        </div>
    </div>
    <div class="box-body no-padding">
        <?php $deletables = 0; ?>
        <?php if (! $group->fieldItems->isEmpty()) { ?>
        <table id="left" class="table table-striped table-hover responsive">
            <tr class="bg-navy disabled">
                <th style="text-align: center; vertical-align: middle;"><?= __d('roles', 'ID'); ?></th>
                <th style="text-align: center; vertical-align: middle;"><?= __d('roles', 'Label'); ?></th>
                <th style="text-align: center; vertical-align: middle;"><?= __d('roles', 'Name'); ?></th>
                <th style="text-align: center; vertical-align: middle;"><?= __d('roles', 'Type'); ?></th>
                <th style="text-align: center; vertical-align: middle;"><?= __d('roles', 'Order'); ?></th>
                <th style="text-align: left; vertical-align: middle;"><?= __d('roles', 'Rules'); ?></th>
                <th style="text-align: right; vertical-align: middle;"><?= __d('roles', 'Operations'); ?></th>
            </tr>
            <?php foreach ($group->fieldItems as $item) { ?>
            <tr>
                <td style="text-align: center; vertical-align: middle;" width="5%"><?= $item->id; ?></td>
                <td style="text-align: center; vertical-align: middle;" width="15%"><?= $item->title; ?></td>
                <td style="text-align: center; vertical-align: middle;" width="10%"><?= $item->slug; ?></td>
                <td style="text-align: center; vertical-align: middle;" width="10%"><?= $item->type; ?></td>
                <td style="text-align: center; vertical-align: middle;" width="10%"><?= $item->order; ?></td>
                <td style="text-align: left; vertical-align: middle;" width="25%"><?= $item->rules ?: '-'; ?></td>
                <td style="text-align: right; vertical-align: middle;" width="15%">
                    <div class="btn-group" role="group" aria-label="...">
                        <?php $deletables++; ?>
                        <a class="btn btn-sm btn-danger" href="#" data-toggle="modal" data-target="#modal-delete-dialog" data-id="<?= $item->id; ?>" title="<?= __d('roles', 'Delete this Role'); ?>" role="button"><i class="fa fa-remove"></i></a>
                        <a class="btn btn-sm btn-success" href="<?= site_url('admin/contacts/' .$group->id .'/field-items/' .$item->id .'/edit'); ?>" title="<?= __d('roles', 'Edit this Role'); ?>" role="button"><i class="fa fa-pencil"></i></a>
                        <a class="btn btn-sm btn-warning" href="<?= site_url('admin/contacts/' .$group->id .'/field-items/' .$item->id); ?>" title="<?= __d('roles', 'Show the Details'); ?>" role="button"><i class="fa fa-search"></i></a>
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
        <a class="btn btn-success col-sm-2 pull-right" href="<?= site_url('admin/contacts/' .$group->id .'/field-items/create'); ?>">
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

<div class="modal modal-default" id="modal-edit-field-dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="modal-edit-field-form" class="form-horizontal" action="" method='POST' role="form">

            <div class="modal-header" style="padding: 10px;">
                <button aria-label="<?= __d('records', 'Close'); ?>" data-dismiss="modal" class="close" type="button">
                <span aria-hidden="true">×</span></button>
                <h4 class="modal-edit-title" style="margin: 0;"><?= __d('records', 'Create a new Custom Field'); ?></h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="field_title"><?= __d('contacts', 'Label'); ?> <font color="#CC0000">*</font></label>
                    <div class="col-sm-9">
                        <input name="field_title" id="modal-edit-field-title" type="text" class="form-control" value="" placeholder="<?= __d('contacts', 'Label'); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="field_slug"><?= __d('contacts', 'Name'); ?> <font color="#CC0000">*</font></label>
                    <div class="col-sm-9">
                        <input name="field_slug" id="modal-edit-field-slug" type="text" class="form-control" value="" placeholder="<?= __d('contacts', 'Name'); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="field_type"><?= __d('contacts', 'Type'); ?> <font color="#CC0000">*</font></label>
                    <div class="col-sm-3">
                        <select name="field_type" id="modal-edit-field-type" class="form-control select2" placeholder="" data-placeholder="<?= __d('contacts', 'Select a Type'); ?>" style="width: 100%;" autocomplete="off">
                            <option value="text"><?= __d('contacts', 'Text'); ?></option>
                            <option value="password"><?= __d('contacts', 'Password'); ?></option>
                            <option value="textarea"><?= __d('contacts', 'Textarea'); ?></option>
                            <option value="select"><?= __d('contacts', 'Select'); ?></option>
                            <option value="checkbox"><?= __d('contacts', 'Checkbox'); ?></option>
                            <option value="radio"><?= __d('contacts', 'Radio'); ?></option>
                            <option value="file"><?= __d('contacts', 'File'); ?></option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="field_default"><?= __d('contacts', 'Default Value'); ?></label>
                    <div class="col-sm-9">
                        <input name="field_default" id="modal-edit-field-default" type="text" class="form-control" value="" placeholder="<?= __d('contacts', 'Default Value'); ?>">
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="order"><?= __d('contacts', 'Order'); ?> <font color="#CC0000">*</font></label>
                    <div class="col-sm-2">
                        <input name="order" id="modal-edit-order" type="number" class="form-control" min="0" max="1000" value="1" autocomplete="off">
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="field_options"><?= __d('contacts', 'Options'); ?></label>
                    <div class="col-sm-9">
                        <textarea name="field_options" id="modal-edit-field-options" class="form-control" style="resize: none;" rows="5" placeholder="<?= __d('contacts', 'Options'); ?>"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="field_rules"><?= __d('contacts', 'Rules'); ?></label>
                    <div class="col-sm-9">
                        <input name="field_rules" id="modal-edit-field-rules" type="text" class="form-control" value="" placeholder="<?= __d('contacts', 'Validation Rules'); ?>">
                    </div>
                </div>
                <div class="clearfix"></div>
                <br>
                <font color="#CC0000">*</font><?= __d('contacts', 'Required field'); ?>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="id" id="edit-record-id" value="0" />
                <input type="hidden" name="_token" value="<?= csrf_token(); ?>" />
                <button data-dismiss="modal" class="btn btn-primary pull-left col-md-3" type="button"><?= __d('contacts', 'Cancel'); ?></button>
                <input type="submit" name="button" class="btn btn btn-success pull-right col-md-3" value="<?= __d('contacts', 'Save'); ?>">
            </div>

            <?= csrf_field(); ?>

            </form>
        </div>
        <!-- /.modal-content -->
    </div>
</div>

<script>

$(function () {
    $('#modal-edit-field-dialog').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal

        var groupId = button.data('groupid');

        var id = button.data('id');

        var action = '<?= site_url("admin/contacts/" .$contact->id .'/field-groups'); ?>/' + groupId + '/items';

        var title   = button.data('title');
        var slug    = button.data('slug');
        var type    = button.data('type');
        var rules   = button.data('rules');
        var order   = button.data('order');
        var options = button.data('options');

        $('#modal-edit-field-title').val(title);
        $('#modal-edit-field-slug').val(slug);

        $('#modal-edit-field-type').val(type).trigger('change');

        $('#modal-edit-field-rules').val(rules);
        $('#modal-edit-field-order').val(order);
        $('#modal-edit-field-options').val(options);

        if (isNaN(id = parseInt(id, 10))) {
            //
        } else if (id > 0) {
            // Adjust the dialog title.
            var title = sprintf("<?= __d('contacts', 'Edit the Custom Field <b>#%d</b>'); ?>", id);

            $('.modal-edit-field-title').html(title);

            // Adjust the form action.
            action += '/' + id;
        }

        $('#modal-edit-field-form').attr('action', action);
    });

    $("#modal-edit-dialog").on('hidden.bs.modal', function () {
        $('.modal-edit-iframe').attr('action', '');
    });
});

</script>

