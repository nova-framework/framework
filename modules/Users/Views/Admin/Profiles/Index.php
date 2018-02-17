<section class="content-header">
    <h1><?= __d('users', 'Users Profile'); ?></h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('admin/dashboard'); ?>"><i class="fa fa-dashboard"></i> <?= __d('users', 'Dashboard'); ?></a></li>
        <li><?= __d('users', 'Users Profile'); ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= Session::getMessages(); ?>

<div class="box box-default">
    <div class="box-header">
        <h3 class="box-title"><?= __d('users', 'Registered Fields'); ?></h3>
    </div>
    <div class="box-body no-padding">
        <?php $deletables = $editables = 0; ?>
        <?php if (! $profile->fields->isEmpty()) { ?>
        <table id="left" class="table table-striped table-hover responsive">
            <tr class="bg-navy disabled">
                <th style="text-align: center; vertical-align: middle;"><?= __d('users', 'Name'); ?></th>
                <th style="text-align: left; vertical-align: middle;"><?= __d('users', 'Key'); ?></th>
                <th style="text-align: left; vertical-align: middle;"><?= __d('users', 'Type'); ?></th>
                <th style="text-align: left; vertical-align: middle;"><?= __d('users', 'Validation'); ?></th>
                <th style="text-align: center; vertical-align: middle;"><?= __d('users', 'Order'); ?></th>
                <th style="text-align: center; vertical-align: middle;"><?= __d('users', 'Columns'); ?></th>
                <th style="text-align: center; vertical-align: middle;"><?= __d('users', 'Hidden'); ?></th>
                <th style="text-align: right; vertical-align: middle;"><?= __d('users', 'Operations'); ?></th>
            </tr>
            <?php foreach ($profile->fields as $field) { ?>
            <?php $deletables++; ?>
            <?php $editables++; ?>
            <tr>
                <td style="text-align: center; vertical-align: middle;" width="15%"><?= $field->name; ?></td>
                <td style="text-align: left; vertical-align: middle;" width="15%"><?= $field->key; ?></td>
                <td style="text-align: left; vertical-align: middle;" width="25%"><?= $field->type; ?></td>
                <td style="text-align: left; vertical-align: middle;" width="20%"><?= $field->validate ?: '-'; ?></td>
                <td style="text-align: center; vertical-align: middle;" width="5%"><?= $field->order; ?></td>
                <td style="text-align: center; vertical-align: middle;" width="5%"><?= $field->columns; ?></td>
                <td style="text-align: center; vertical-align: middle;" width="5%"><?= ($field->hidden === 1) ? __d('users', 'Yes') : __d('users', 'No'); ?></td>
                <td style="text-align: right; vertical-align: middle;" width="15%">
                    <div class="btn-group" role="group" aria-label="...">
                        <a class="btn btn-sm btn-danger" href="#" data-toggle="modal" data-target="#modal-delete-dialog" data-id="<?= $field->id; ?>" title="<?= __d('users', 'Delete this Field'); ?>" role="button"><i class="fa fa-remove"></i></a>
                        <a class="btn btn-sm btn-success" href="#" data-toggle="modal" data-target="#modal-edit-dialog" data-id="<?= $field->id; ?>" data-name="<?= $field->name; ?>" data-key="<?= $field->key; ?>" data-type="<?= $field->type; ?>" data-validate="<?= $field->validate; ?>" data-hidden="<?= $field->hidden; ?>" data-columns="<?= $field->columns; ?>" data-order="<?= $field->order; ?>" title="<?= __d('users', 'Edit this Field'); ?>" role="button"><i class="fa fa-pencil"></i></a>
                    </div>
                </td>
            </tr>
            <?php } ?>
        </table>
        <?php } else { ?>
        <div class="alert alert-warning" style="margin: 0 5px 5px;">
            <h4><i class="icon fa fa-warning"></i> <?= strftime("%d %b %Y, %R", time()) ." - "; ?> <?= __d('users', 'No registered Fields'); ?></h4>
            <?= __d('users', 'There are no registered Fields.'); ?>
        </div>
        <?php } ?>
    </div>
</div>

<form class="form-horizontal" action="<?= site_url('admin/profile'); ?>" method='POST' role="form">

<div class="box box-widget">
    <div class="box-header">
        <h3 class="box-title"><?= __d('users', 'Add a new Field'); ?></h3>
    </div>
    <div class="box-body no-padding">
        <table id="left" class="table table-striped table-hover responsive">
            <tr class="bg-navy disabled">
                <th style="text-align: center; vertical-align: middle;"><?= __d('users', 'Name'); ?></th>
                <th style="text-align: center; vertical-align: middle;"><?= __d('users', 'Key'); ?></th>
                <th style="text-align: center; vertical-align: middle;"><?= __d('users', 'Type'); ?></th>
                <th style="text-align: center; vertical-align: middle;"><?= __d('users', 'Validation'); ?></th>
                <th style="text-align: center; vertical-align: middle;"><?= __d('users', 'Order'); ?></th>
                <th style="text-align: center; vertical-align: middle;"><?= __d('users', 'Columns'); ?></th>
                <th style="text-align: center; vertical-align: middle;"><?= __d('users', 'Hidden'); ?></th>
            </tr>
            <tr>
                <td style="text-align: center; vertical-align: middle;" width="15%">
                    <input name="name" id="name" type="text" class="form-control" value="<?= ''; ?>" placeholder="<?= __d('users', 'Name'); ?>" autocomplete="off">
                </td>
                <td style="text-align: center; vertical-align: middle;" width="15%">
                    <input name="key" id="key" type="text" class="form-control" value="<?= ''; ?>" placeholder="<?= __d('users', 'Key'); ?>" autocomplete="off">
                </td>
                <td style="text-align: left; vertical-align: middle;" width="25%">
                    <?php $optType = Input::old('type'); ?>
                    <select name="type" id="type" class="form-control select2" placeholder="" data-placeholder="<?= __d('users', 'Select a Type'); ?>" style="width: 100%;" autocomplete="off">
                        <option></option>
                        <?php foreach ($types as $class => $instance) { ?>
                        <option value="<?= $class ?>" <?= ($optType == $class) ? 'selected' : ''; ?>><?= $class; ?></option>
                        <?php } ?>
                    </select>
                </td>
                <td style="text-align: center; vertical-align: middle;" width="25%">
                    <input name="validate" id="validate" type="text" class="form-control" value="<?= ''; ?>" placeholder="<?= __d('users', 'Validation'); ?>" autocomplete="off">
                </td>
                <td style="text-align: center; vertical-align: middle;" width="8%">
                    <input name="order" id="order" type="number" class="form-control" min="0" max="1000" value="<?= Input::old('order', 1); ?>" autocomplete="off">
                </td>
                <td style="text-align: center; vertical-align: middle;" width="7%">
                    <input name="columns" id="columns" type="number" class="form-control" min="1" max="8" value="<?= Input::old('columns', 8); ?>" autocomplete="off">
                </td>
                <td style="text-align: center; vertical-align: middle;" width="5%">
                    <?php $checked = (1 === (int) Input::old('hidden')); ?>
                    <input type="checkbox" name="hidden" value="1" <?= $checked ? 'checked="checked"' : ''; ?> autocomplete="off"/>
                </td>
            </tr>
        </table>
    </div>
    <div class="box-footer">
        <input type="submit" name="submit" class="btn btn-success col-sm-2 pull-right" value="<?= __d('platform', 'Create a New Field'); ?>">
    </div>
</div>

<?= csrf_field(); ?>

</form>

</section>

<?php if ($deletables > 0) { ?>

<div class="modal modal-default" id="modal-delete-dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-label="<?= __d('users', 'Close'); ?>" data-dismiss="modal" class="close" type="button">
                <span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><?= __d('users', 'Delete this Field?'); ?></h4>
            </div>
            <div class="modal-body">
                <p><?= __d('users', 'Are you sure you want to remove this Field, the operation being irreversible?'); ?></p>
                <p><?= __d('users', 'Please click the button <b>Delete</b> to proceed, or <b>Cancel</b> to abandon the operation.'); ?></p>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-primary pull-left col-md-3" type="button"><?= __d('users', 'Cancel'); ?></button>
                <form id="modal-delete-form" action="" method="POST">
                    <input type="hidden" name="id" id="delete-record-id" value="0" />
                    <input type="hidden" name="_token" value="<?= csrf_token(); ?>" />
                    <input type="submit" name="button" class="btn btn btn-danger pull-right col-md-3" value="<?= __d('users', 'Delete'); ?>">
                </form>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
</div>

<script>

$(function () {
    $('#modal-delete-dialog').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal

        var id = button.data('id');

        //
        $('#delete-record-id').val(id);

        $('#modal-delete-form').attr('action', '<?= site_url("admin/profile"); ?>/' + id + '/destroy');
    });
});

</script>

<?php } ?>

<?php if ($editables > 0) { ?>

<div class="modal modal-default" id="modal-edit-dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="modal-edit-form" class="form-horizontal" action="<?= site_url('admin/profile'); ?>" method='POST' role="form">

            <div class="modal-header" style="padding: 10px;">
                <button aria-label="<?= __d('records', 'Close'); ?>" data-dismiss="modal" class="close" type="button">
                <span aria-hidden="true">×</span></button>
                <h4 class="modal-edit-title" style="margin: 0;"><?= __d('records', 'Edit a Field'); ?></h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="name"><?= __d('users', 'Name'); ?> <font color="#CC0000">*</font></label>
                    <div class="col-sm-9">
                        <input name="name" id="modal-edit-name" type="text" class="form-control" value="" placeholder="<?= __d('users', 'Name'); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="key"><?= __d('users', 'Key'); ?> <font color="#CC0000">*</font></label>
                    <div class="col-sm-9">
                        <input name="key" id="modal-edit-key" type="text" class="form-control" value="" placeholder="<?= __d('users', 'Key'); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="type"><?= __d('users', 'Type'); ?> <font color="#CC0000">*</font></label>
                    <div class="col-sm-9">
                        <select name="type" id="modal-edit-type" class="form-control select2" placeholder="" data-placeholder="<?= __d('users', 'Select a Type'); ?>" style="width: 100%;" autocomplete="off">
                            <?php foreach ($types as $class => $instance) { ?>
                            <option value="<?= $class ?>"><?= $class; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="validate"><?= __d('users', 'Validation'); ?> <font color="#CC0000">*</font></label>
                    <div class="col-sm-9">
                        <input name="validate" id="modal-edit-validate" type="text" class="form-control" value="" placeholder="<?= __d('users', 'Validation'); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="order"><?= __d('users', 'Order'); ?> <font color="#CC0000">*</font></label>
                    <div class="col-sm-2">
                        <input name="order" id="modal-edit-order" type="number" class="form-control" min="0" max="1000" value="1" autocomplete="off">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="order"><?= __d('users', 'Columns'); ?> <font color="#CC0000">*</font></label>
                    <div class="col-sm-2">
                        <input name="columns" id="modal-edit-columns" type="number" class="form-control" min="1" max="8" value="8" autocomplete="off">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="hidden"><?= __d('users', 'Hidden'); ?> <font color="#CC0000">*</font></label>
                    <div class="col-sm-9">
                        <input type="checkbox" name="hidden" id="modal-edit-hidden" value="1" autocomplete="off"/>
                    </div>
                </div>
                <div class="clearfix"></div>
                <br>
                <font color="#CC0000">*</font><?= __d('users', 'Required field'); ?>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="id" id="edit-record-id" value="0" />
                <input type="hidden" name="_token" value="<?= csrf_token(); ?>" />
                <button data-dismiss="modal" class="btn btn-primary pull-left col-md-3" type="button"><?= __d('users', 'Cancel'); ?></button>
                <input type="submit" name="button" class="btn btn btn-success pull-right col-md-3" value="<?= __d('users', 'Save'); ?>">
            </div>

            <?= csrf_field(); ?>

            </form>
        </div>
        <!-- /.modal-content -->
    </div>
</div>

<script>

$(function () {
    $('#modal-edit-dialog').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal

        var id       = button.data('id');
        var name     = button.data('name');
        var key      = button.data('key');
        var type     = button.data('type');
        var validate = button.data('validate');
        var hidden   = button.data('hidden');
        var columns  = button.data('columns');
        var order    = button.data('order');

        $('#modal-edit-name').val(name);
        $('#modal-edit-key').val(key);

        $('#modal-edit-type').val(type).trigger('change');

        $('#modal-edit-validate').val(validate);

        var checkbox = $('#modal-edit-hidden');

        if (hidden === 1) {
            checkbox.attr('checked', 'checked');
        } else {
            checkbox.removeAttr('checked');
        }

        checkbox.iCheck('update');

        $('#modal-edit-columns').val(columns);
        $('#modal-edit-order').val(order);

        // The title.
        var title = sprintf("<?= __d('users', 'Edit the Field <b>#%s</b>'); ?>", id);

        $('.modal-edit-title').html(title);

        // The form action.
        $('#modal-edit-form').attr('action', '<?= site_url("admin/profile"); ?>/' + id);
    });

    $("#modal-edit-dialog").on('hidden.bs.modal', function () {
        $('.modal-edit-iframe').attr('src', '');
    });
});

</script>

<?php } ?>


