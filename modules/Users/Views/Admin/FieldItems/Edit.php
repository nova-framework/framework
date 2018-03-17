<section class="content-header">
    <h1><?= $title; ?></h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('admin/dashboard'); ?>"><i class="fa fa-dashboard"></i> <?= __d('users', 'Dashboard'); ?></a></li>
        <li><a href="<?= site_url('admin/users'); ?>"><?= __d('users', 'Users'); ?></a></li>
        <li><a href="<?= site_url('admin/users/fields'); ?>"><?= __d('users', 'Custom Fields'); ?></a></li>
        <li><?= __d('users', 'Edit Field'); ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= View::fetch('Partials/Messages'); ?>

<form id="create-group-form" class="form-horizontal" action="<?= site_url('admin/users/fields/{0}', $item->id); ?>" method='POST' role="form">

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('users', 'Edit a Field'); ?></h3>
    </div>
    <div class="box-body">
        <div class="form-group clearfix" style="margin: 0;">
            <div class="col-md-8 col-md-offset-2 col-sm-8 col-sm-offset-2" style="padding-bottom: 0;">
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="field_title"><?= __d('users', 'Label'); ?> <font color="#CC0000">*</font></label>
                    <div class="col-sm-9">
                        <input name="field_title" id="field-title" type="text" class="form-control" value="<?= Input::old('field_title', $item->title); ?>" placeholder="<?= __d('users', 'Label'); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="field_name"><?= __d('users', 'Name'); ?> <font color="#CC0000">*</font></label>
                    <div class="col-sm-9">
                        <input name="field_name" id="field-name" type="text" class="form-control" value="<?= Input::old('field_name', $item->name); ?>" placeholder="<?= __d('users', 'Name'); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="field_type"><?= __d('users', 'Type'); ?> <font color="#CC0000">*</font></label>
                    <div class="col-sm-3">
                        <?php $type = Input::old('field_type', $item->type); ?>
                        <select name="field_type" id="field-type" class="form-control select2" placeholder="" data-placeholder="<?= __d('users', 'Select a Type'); ?>" style="width: 100%;" autocomplete="off">
                            <option value="text"     <?= ($type == 'text'     ? 'selected="selected"' : '') ?>><?= __d('users', 'Text'); ?></option>
                            <option value="textarea" <?= ($type == 'textarea' ? 'selected="selected"' : '') ?>><?= __d('users', 'Textarea'); ?></option>
                            <option value="select"   <?= ($type == 'select'   ? 'selected="selected"' : '') ?>><?= __d('users', 'Select'); ?></option>
                            <option value="checkbox" <?= ($type == 'checkbox' ? 'selected="selected"' : '') ?>><?= __d('users', 'Checkbox'); ?></option>
                            <option value="radio"    <?= ($type == 'radio'    ? 'selected="selected"' : '') ?>><?= __d('users', 'Radio'); ?></option>
                        </select>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="order"><?= __d('users', 'Order'); ?> <font color="#CC0000">*</font></label>
                    <div class="col-sm-3">
                        <input name="field_order" id="field-order" type="number" class="form-control" min="0" max="1000" value="<?= Input::old('field_order', $item->order); ?>" style="padding: 6px 3px 6px 12px;" autocomplete="off">
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="form-group" id="choices-form-group" style="display: none;">
                    <label class="col-sm-3 control-label" for="field_choices"><?= __d('users', 'Choices'); ?></label>
                    <div class="col-sm-9">
                        <textarea name="field_choices" id="field-choices" class="form-control" style="resize: none;" rows="5" placeholder="<?= __d('users', 'Choices'); ?>"><?= Input::old('field_choices', $choices); ?></textarea>
                    </div>
                </div>
                <div class="form-group" id="default-form-group" style="display: none;">
                    <label class="col-sm-3 control-label" for="field_default"><?= __d('users', 'Default'); ?></label>
                    <div class="col-sm-9">
                        <input name="field_default" id="field-default" type="text" class="form-control" value="<?= Input::old('field_default', $default); ?>" placeholder="<?= __d('users', 'Default'); ?>">
                    </div>
                </div>
                <div class="form-group" id="rows-form-group" style="display: none;">
                    <label class="col-sm-3 control-label" for="field_rows"><?= __d('users', 'Rows'); ?></label>
                    <div class="col-sm-3">
                        <input name="field_rows" id="field_rows" type="number" class="form-control" min="1" max="100" value="<?= Input::old('field_rows', $rows); ?>" style="padding: 6px 3px 6px 12px;" autocomplete="off">
                    </div>
                </div>
                <div class="form-group" id="placeholder-form-group" style="display: none;">
                    <label class="col-sm-3 control-label" for="field_placeholder"><?= __d('users', 'Placeholder'); ?></label>
                    <div class="col-sm-9">
                        <input name="field_placeholder" id="field-placeholder" type="text" class="form-control" value="<?= Input::old('field_placeholder', $placeholder); ?>" placeholder="<?= __d('users', 'Placeholder'); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="field_rules"><?= __d('users', 'Rules'); ?></label>
                    <div class="col-sm-9">
                        <input name="field_rules" id="field-rules" type="text" class="form-control" value="<?= Input::old('field_rules', $item->rules); ?>" placeholder="<?= __d('users', 'Rules'); ?>">
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            <font color="#CC0000">*</font><?= __d('users', 'Required field'); ?>
        </div>
    </div>
    <div class="box-footer">
        <input type="submit" name="submit" class="btn btn-success col-sm-2 pull-right" value="<?= __d('roles', 'Save'); ?>">
    </div>
</div>

<input type="hidden" name="_token"   value="<?= csrf_token(); ?>" />

</form>

<a class="btn btn-primary col-sm-2" href="<?= site_url('admin/users/fields'); ?>"><?= __d('users', '<< Previous Page'); ?></a>

<div class="clearfix"></div>
<br>

</section>

<script>

function updateFormFields()
{
    var type = $('#field-type').val();

    //
    var placeholder   = $('#placeholder-form-group');
    var input_choices = $('#choices-form-group');
    var default_value = $('#default-form-group');
    var textarea_rows = $('#rows-form-group');

    if (type == 'text') {
        placeholder.show();
        input_choices.hide();
        default_value.show();
        textarea_rows.hide();
    } else if (type == 'textarea') {
        placeholder.show();
        input_choices.hide();
        default_value.hide();
        textarea_rows.show();
    } else if (type == 'select') {
        placeholder.show();
        input_choices.show();
        default_value.show();
        textarea_rows.hide();
    } else if ((type == 'checkbox') || (type == 'radio')) {
        placeholder.hide();
        input_choices.show();
        default_value.hide();
        textarea_rows.hide();
    }
}

$(function () {
    $('#field-type').on('change', function () {
        updateFormFields();
    });

    updateFormFields();
});

</script>

