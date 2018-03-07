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
            <td style="width: 65%;"><?= $message->author_ip; ?></td>
        </tr>
    </table>

    <h3><?= __d('contacts', 'Message Content'); ?></h3>

    <table class="table">
        <tr>
            <th style="width: 35%;"><?= __d('contacts', 'Field'); ?></th>
            <th style="width: 65%; text-align: left;"><?= __d('contacts', 'Value'); ?></th>
        </tr>
        <tr>
            <td style="width: 35%;" class="field"><?= __d('contacts', 'Author'); ?></td>
            <td style="width: 65%;"><?= e($message->author); ?></td>
        </tr>
        <tr>
            <td style="width: 35%;" class="field"><?= __d('contacts', 'E-mail'); ?></td>
            <td style="width: 65%;"><?= e($message->author_email); ?></td>
        </tr>
        <tr>
            <td style="width: 35%;" class="field"><?= __d('contacts', 'Website'); ?></td>
            <td style="width: 65%;"><?= e($message->author_url ?: '-'); ?></td>
        </tr>
        <tr>
            <td style="width: 35%;" class="field"><?= __d('contacts', 'Message'); ?></td>
            <td style="width: 65%;"><?= nl2br(e($message->content)); ?></td>
        </tr>
    </table>

</body>
</html>
