<!doctype html>
<html lang="en">
        <head>
        <meta charset="UTF-8">
        <title><?= __d('contacts', 'Message'); ?></title>
        <style type="text/css">

.page-break {
    page-break-after: always;
}

table.table {
   width: 100%;

   color:#333333;
   border: 1px solid #666666;
   border-collapse: collapse;
}

table.table th {
   width: 100%;

   padding: 6px;
   background-color: #DEDEDE;
   border: 1px solid #666666;
   text-align: right;
}

table.table td {
   width: 100%;

   padding: 6px;
   /*background-color: #FFFFFF;*/
   border: 1px solid #666666;
}

tr.odd { background-color: #FDFDFD; }

tr, tr.even { }

td.field {
    font-weight: bold;
    text-align: right;
}

hr
{
   border: none;
   background-color: #666666;
   color: #666666;
   height: 1px;
}

        </style>
</head>
<body>
    <h3><?= __d('contacts', 'Message Information'); ?></h3>

    <table class="table">
        <tr>
            <th style="width: 35%;"><?= __d('contacts', 'Field'); ?></th>
            <th style="width: 65%; text-align: left;"><?= __d('contacts', 'Value'); ?></th>
        </tr>
        <tr>
            <td style="width: 35%;" class="field"><?= __d('contacts', 'ID'); ?></td>
            <td style="width: 65%;"><?= $message->id; ?></td>
        </tr>
        <tr>
            <td style="width: 35%;" class="field"><?= __d('contacts', 'Contact'); ?></td>
            <td style="width: 65%;"><?= $message->contact->name; ?></td>
        </tr>
        <tr>
            <td style="width: 35%;" class="field"><?= __d('contacts', 'Sent At'); ?></td>
            <td style="width: 65%;"><?= $message->created_at->formatLocalized(__d('contacts', '%d %b %Y, %R')); ?></td>
        </tr>
        <tr>
            <td style="width: 35%;" class="field"><?= __d('contacts', 'Remote IP'); ?></td>
            <td style="width: 65%;"><?= $message->remote_ip; ?></td>
        </tr>
        <tr>
            <td style="width: 35%;" class="field"><?= __d('contacts', 'Attachments'); ?></td>
            <td style="width: 65%;"><?= $message->attachments->count(); ?></td>
        </tr>
    </table>

<?php
foreach ($contact->fieldGroups as $group) {
    $items = $group->fieldItems->filter(function ($item)
    {
        return ($item->type != 'file');
    });

    if ($items->isEmpty()) {
        continue;
    }
?>
    <div class="page-break"></div>

    <h3><?= $group->title; ?></h3>

    <table class="table">
        <tr>
            <th style="width: 35%;"><?= __d('contacts', 'Field'); ?></th>
            <th style="width: 65%; text-align: left;"><?= __d('contacts', 'Value'); ?></th>
        </tr>
<?php
    foreach ($items as $item) {
        $field = $message->fields->where('field_item_id', $item->id)->first();

        if (is_null($field)) {
            $value = '-';
        } else {
            $value = $field->getValueString();
        }

        if ($item->type == 'textarea') {
            $value = nl2br($value);
        }
?>
        <tr>
            <td style="width: 35%;" class="field"><?= $item->title; ?></td>
            <td style="width: 65%;"><?= $value; ?></td>
        </tr>
    <?php } ?>
    </table>
<?php } ?>

</body>
</html>
