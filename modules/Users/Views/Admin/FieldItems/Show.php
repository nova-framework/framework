<section class="content-header">
    <h1><?= __d('users', 'Show Role'); ?></h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('admin/dashboard'); ?>"><i class="fa fa-dashboard"></i> <?= __d('users', 'Dashboard'); ?></a></li>
        <li><a href="<?= site_url('admin/users'); ?>"><?= __d('users', 'Users'); ?></a></li>
        <li><a href="<?= site_url('admin/users/fields'); ?>"><?= __d('users', 'Custom Fields'); ?></a></li>
        <li><?= __d('users', 'Show Field'); ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= View::fetch('Partials/Messages'); ?>

<div class="box box-widget">
    <div class="box-header">
        <h3 class="box-title"><?= __d('users', 'Field : <b>{0}</b>', $item->title); ?></h3>
    </div>
    <div class="box-body no-padding">
        <table id="left" class="table table-hover responsive">
            <tr class="bg-navy disabled">
                <th style="text-align: left; vertical-align: middle;"><?= __d('users', 'Field'); ?></th>
                <th style="text-align: left; vertical-align: middle;"><?= __d('users', 'Value'); ?></th>
            </tr>
            <tr>
                <th style="text-align: left; vertical-align: middle;"><?= __d('users', 'ID'); ?></th>
                <td style="text-align: left; vertical-align: middle;" width="75%"><?= $item->id; ?></td>
            <tr>
            <tr>
                <th style="text-align: left; vertical-align: middle;"><?= __d('users', 'Label'); ?></th>
                <td style="text-align: left; vertical-align: middle;" width="75%"><?= $item->title; ?></td>
            </tr>
            <tr>
                <th style="text-align: left; vertical-align: middle;"><?= __d('users', 'Name'); ?></th>
                <td style="text-align: left; vertical-align: middle;" width="75%"><?= $item->name; ?></td>
            </tr>
            <tr>
                <th style="text-align: left; vertical-align: middle;"><?= __d('users', 'Type'); ?></th>
                <td style="text-align: left; vertical-align: middle;" width="75%"><?= Str::title($type = $item->type); ?></td>
            </tr>
            <?php if (($type == 'select') || ($type == 'checkbox') || ($type == 'radio')) { ?>
            <tr>
                <th style="text-align: left; vertical-align: middle;"><?= __d('users', 'Choices'); ?></th>
                <td style="text-align: left; vertical-align: middle;" width="75%"><?= array_get($options, 'choices') ?: '-'; ?></td>
            </tr>
            <?php } else if ($type == 'textarea') { ?>
            <tr>
                <th style="text-align: left; vertical-align: middle;"><?= __d('users', 'Rows'); ?></th>
                <td style="text-align: left; vertical-align: middle;" width="75%"><?= array_get($options, 'rows') ?: '-'; ?></td>
            </tr>
            <?php } ?>
            <?php if (($type == 'text') || ($type == 'select')) { ?>
            <tr>
                <th style="text-align: left; vertical-align: middle;"><?= __d('users', 'Default Value'); ?></th>
                <td style="text-align: left; vertical-align: middle;" width="75%"><?= array_get($options, 'default') ?: '-'; ?></td>
            </tr>
            <?php } ?>
            <?php if (($type == 'text') || ($type == 'password') || ($type == 'select')) { ?>
            <tr>
                <th style="text-align: left; vertical-align: middle;"><?= __d('users', 'Placeholder'); ?></th>
                <td style="text-align: left; vertical-align: middle;" width="75%"><?= array_get($options, 'placeholder') ?: '-'; ?></td>
            </tr>
            <?php } ?>
            <tr>
                <th style="text-align: left; vertical-align: middle;"><?= __d('users', 'Rules'); ?></th>
                <td style="text-align: left; vertical-align: middle;" width="75%"><?= $item->rules; ?></td>
            </tr>
            <tr>
                <th style="text-align: left; vertical-align: middle;"><?= __d('users', 'Created At'); ?></th>
                <td style="text-align: left; vertical-align: middle;" width="75%"><?= $item->created_at->formatLocalized($format = __d('users', '%d %b %Y, %R')); ?></td>
            </tr>
            <tr>
                <th style="text-align: left; vertical-align: middle;"><?= __d('users', 'Updated At'); ?></th>
                <td style="text-align: left; vertical-align: middle;" width="75%"><?= $item->updated_at->formatLocalized($format); ?></td>
            <tr>
        </table>
    </div>
</div>

<a class="btn btn-primary col-sm-2" href="<?= site_url('admin/users/fields'); ?>"><?= __d('users', '<< Previous Page'); ?></a>

<div class="clearfix"></div>
<br>

</section>
