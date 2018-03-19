<?php if (! $user->fields->isEmpty()) { ?>

<div class="box box-widget">
    <div class="box-header">
        <h3 class="box-title"><?= __d('users', 'User Profile'); ?></h3>
    </div>
    <div class="box-body no-padding">
        <table id="left" class="table table-hover responsive">
            <tr class="bg-navy disabled">
                <th style="text-align: left; vertical-align: middle;"><?= __d('users', 'Field'); ?></th>
                <th style="text-align: left; vertical-align: middle;"><?= __d('users', 'Value'); ?></th>
            </tr>
<?php
foreach ($user->fields as $field) {
    $name = $field->fieldItem->title;

    if (! empty($value = $field->getValueString()) && ($field->type == 'textarea')) {
        $value = nl2br($value);
    }
?>
            <tr>
                <th style="text-align: left; vertical-align: middle;"><?= $name; ?></th>
                <td style="text-align: left; vertical-align: middle;" width="75%"><?= $value; ?></td>
            </tr>
<?php } ?>
        </table>
    </div>
</div>

<?php } ?>
