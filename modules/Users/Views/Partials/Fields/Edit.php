<?php foreach ($items as $item) { ?>
<?php $type = $item->type; ?>
<?php $name = str_replace('-', '_', $item->name); ?>
<?php $id  = str_replace('_', '-', $item->name); ?>
<?php $options = $item->options ?: array(); ?>
<?php $placeholder = array_get($options, 'placeholder') ?: $item->title; ?>
<?php if (isset($user)) { ?>
<?php $field = $user->fields->where('name', $item->name)->first(); ?>
<?php $value = ! is_null($field) ? $field->value : null; ?>
<?php } else { ?>
<?php $value = null; ?>
<?php } ?>

<div class="form-group">
    <label class="col-sm-4 control-label" for="<?= $name; ?>">
        <?= $item->title; ?>
        <?php if (str_contains($item->rules, 'required')) { ?>
        <span class="text-danger" title="<?= __d('users', 'Required field'); ?>">*</span>
        <?php } ?>
    </label>
    <div class="col-sm-8">

    <?php if ($type == 'text') { ?>
        <input type="text" class="form-control" name="<?= $name; ?>" id="<?= $id; ?>" value="<?= Input::old($name, $value); ?>" placeholder="<?= $placeholder; ?>" />
    <?php } else if ($type == 'password') { ?>
        <input type="password" class="form-control" name="<?= $name; ?>" id="<?= $id; ?>" value="<?= Input::old($name); ?>" placeholder="<?= $placeholder; ?>" />
    <?php } else if ($type == 'textarea') { ?>
        <textarea name="<?= $name; ?>" id="<?= $id; ?>" rows="<?= array_get($options, 'rows', 10); ?>" class="form-control" style="resize: none;" placeholder="<?= $placeholder; ?>"><?= Input::old($name, $value); ?></textarea>
    <?php } else if ($type == 'select') { ?>
        <select name="<?= $name; ?>" id="<?= $id; ?>" placeholder="" data-placeholder="<?= array_get($options, 'placeholder') ?: __d('users', '- Choose an option -'); ?>" class="form-control select2">
<option></option>
<?php $selected = Input::old($name, $value); ?>
<?php $choices = explode("\n", trim(array_get($options, 'choices'))); ?>
<?php foreach($choices as $choice) { ?>
<?php list ($value, $label) = explode(':', trim($choice)); ?>
<option value="<?= $value = trim($value); ?>" <?= ($value == $selected) ? 'selected="selected"' : ''; ?>><?= trim($label); ?></option>
<?php } ?>
        </select>
    <?php } else if ($type == 'checkbox') { ?>
        <?php $choices = explode("\n", trim(array_get($options, 'choices'))); ?>
        <?php $multiple = (count($choices) > 1); ?>
        <?php $checked = (array) Input::old($name, $multiple ? (array) $value : $value); ?>
        <?php foreach($choices as $choice) { ?>
        <?php list ($value, $label) = explode(':', trim($choice)); ?>
        <?php $checkId = $id .'-' .str_replace('_', '-', $value = trim($value)); ?>
        <div class="checkbox icheck-primary">
            <input type="checkbox" name="<?= $name; ?><?= $multiple ? '[]' : ''; ?>" id="<?= $checkId; ?>" value="<?= $value; ?>" <?= in_array($value, $checked) ? 'checked' : ''; ?>> <label for="<?= $checkId; ?>"><?= trim($label); ?></label>
        </div>
        <div class="clearfix"></div>
        <?php } ?>
    <?php } else if ($type == 'radio') { ?>
        <?php $checked = Input::old($name, $value); ?>
        <?php $choices = explode("\n", trim(array_get($options, 'choices'))); ?>
        <?php foreach($choices as $choice) { ?>
        <?php list ($value, $label) = explode(':', trim($choice)); ?>
        <?php $checkId = $id .'-' .str_replace('_', '-', $value = trim($value)); ?>
        <div class="radio icheck-primary">
            <input type="radio" name="<?= $name; ?>" id="<?= $checkId; ?>" value="<?= $value; ?>" <?= ($value == $checked) ? 'checked' : ''; ?>> <label for="<?= $checkId; ?>"><?= trim($label); ?></label>
        </div>
        <div class="clearfix"></div>
        <?php } ?>
    <?php } ?>

    </div>
</div>

<?php } ?>

<div class="clearfix"></div>

