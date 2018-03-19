<section class="content-header">
    <h1><?= __d('contacts', 'Show Role'); ?></h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('admin/dashboard'); ?>"><i class="fa fa-dashboard"></i> <?= __d('contacts', 'Dashboard'); ?></a></li>
        <li><a href="<?= site_url('admin/contacts'); ?>"><?= __d('contacts', 'Contacts'); ?></a></li>
        <li><a href="<?= site_url('admin/contacts/{0}/field-groups', $contact->id); ?>"><?= __d('contacts', 'Manage Fields : {0}', $contact->name); ?></a></li>
        <li><?= __d('contacts', 'Show Field Item'); ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= View::fetch('Partials/Messages'); ?>

<div class="box box-widget">
    <div class="box-header">
        <h3 class="box-title"><?= __d('contacts', 'Field Item : <b>{0}</b>', $item->title); ?></h3>
    </div>
    <div class="box-body no-padding">
        <table id="left" class="table table-hover responsive">
            <tr class="bg-navy disabled">
                <th style="text-align: left; vertical-align: middle;"><?= __d('contacts', 'Field'); ?></th>
                <th style="text-align: left; vertical-align: middle;"><?= __d('contacts', 'Value'); ?></th>
            </tr>
            <tr>
                <th style="text-align: left; vertical-align: middle;"><?= __d('contacts', 'ID'); ?></th>
                <td style="text-align: left; vertical-align: middle;" width="75%"><?= $item->id; ?></td>
            <tr>
            <tr>
                <th style="text-align: left; vertical-align: middle;"><?= __d('contacts', 'Label'); ?></th>
                <td style="text-align: left; vertical-align: middle;" width="75%"><?= $item->title; ?></td>
            </tr>
            <tr>
                <th style="text-align: left; vertical-align: middle;"><?= __d('contacts', 'Name'); ?></th>
                <td style="text-align: left; vertical-align: middle;" width="75%"><?= $item->name; ?></td>
            </tr>
            <tr>
                <th style="text-align: left; vertical-align: middle;"><?= __d('contacts', 'Type'); ?></th>
                <td style="text-align: left; vertical-align: middle;" width="75%"><?= Str::title($type = $item->type); ?></td>
            </tr>
            <tr>
                <th style="text-align: left; vertical-align: middle;"><?= __d('contacts', 'Rules'); ?></th>
                <td style="text-align: left; vertical-align: middle;" width="75%"><?= $item->rules; ?></td>
            </tr>
            <?php if (($type == 'select') || ($type == 'checkbox') || ($type == 'radio')) { ?>
            <tr>
                <th style="text-align: left; vertical-align: middle;"><?= __d('contacts', 'Choices'); ?></th>
                <td style="text-align: left; vertical-align: middle;" width="75%"><?= array_get($options, 'choices') ?: '-'; ?></td>
            </tr>
            <?php } else if ($type == 'textarea') { ?>
            <tr>
                <th style="text-align: left; vertical-align: middle;"><?= __d('contacts', 'Rows'); ?></th>
                <td style="text-align: left; vertical-align: middle;" width="75%"><?= array_get($options, 'rows') ?: '-'; ?></td>
            </tr>
            <?php } ?>
            <?php if (($type == 'text') || ($type == 'select')) { ?>
            <tr>
                <th style="text-align: left; vertical-align: middle;"><?= __d('contacts', 'Default Value'); ?></th>
                <td style="text-align: left; vertical-align: middle;" width="75%"><?= array_get($options, 'default') ?: '-'; ?></td>
            </tr>
            <?php } ?>
            <?php if (($type == 'text') || ($type == 'password') || ($type == 'select')) { ?>
            <tr>
                <th style="text-align: left; vertical-align: middle;"><?= __d('contacts', 'Placeholder'); ?></th>
                <td style="text-align: left; vertical-align: middle;" width="75%"><?= array_get($options, 'placeholder') ?: '-'; ?></td>
            </tr>
            <?php } ?>
            <tr>
                <th style="text-align: left; vertical-align: middle;"><?= __d('contacts', 'Created At'); ?></th>
                <td style="text-align: left; vertical-align: middle;" width="75%"><?= $item->created_at->formatLocalized($format = __d('contacts', '%d %b %Y, %R')); ?></td>
            </tr>
            <tr>
                <th style="text-align: left; vertical-align: middle;"><?= __d('contacts', 'Updated At'); ?></th>
                <td style="text-align: left; vertical-align: middle;" width="75%"><?= $item->updated_at->formatLocalized($format); ?></td>
            <tr>
        </table>
    </div>
</div>

<a class="btn btn-primary col-sm-2" href="<?= site_url('admin/contacts/{0}/field-groups', $contact->id); ?>"><?= __d('contacts', '<< Previous Page'); ?></a>

<div class="clearfix"></div>
<br>

</section>
