<section class="content-header">
    <h1><?= $title; ?></h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('admin/dashboard'); ?>"><i class="fa fa-dashboard"></i> <?= __d('contacts', 'Dashboard'); ?></a></li>
        <li><a href="<?= site_url('admin/contacts'); ?>"><?= __d('contacts', 'Contacts'); ?></a></li>
        <li><?= $title; ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= Session::getMessages(); ?>

<div class="box box-default">
    <div class="box-header">
        <h3 class="box-title"><?= __d('contacts', 'Message details'); ?></h3>
    </div>
    <div class="box-body no-padding">
        <table id="left" class="table table-hover responsive">
            <tr class="bg-navy disabled">
                <th style="text-align: left; vertical-align: middle;"><?= __d('contacts', 'Field'); ?></th>
                <th style="text-align: left; vertical-align: middle;"><?= __d('contacts', 'Value'); ?></th>
            </tr>
            <tr>
                <th style="text-align: left; vertical-align: middle;"><?= __d('contacts', 'Author IP'); ?></th>
                <td style="text-align: left; vertical-align: middle;" width="75%"><?= $message->contact_author_ip; ?></td>
            <tr>
            <?php
            foreach ($message->meta as $meta) {
                if (! Str::is('contact_*', $name = $meta->key) || ($name == 'contact_author_ip') || ($name == 'contact_path')) {
                    continue;
                }

                $value = $meta->value;

                if ('select' == Arr::get($elements, $name .'.type')) {
                    $value = Arr::get($elements, $value, $value);
                }

                $label = Arr::get($elements, $name .'.label', __d('contacts', 'Unknown'));
            ?>
            <tr>
                <th style="text-align: left; vertical-align: middle;"><?= $label; ?></th>
                <td style="text-align: left; vertical-align: middle;" width="75%"><?= nl2br(e($value)); ?></td>
            <tr>
            <?php } ?>
            <tr>
                <th style="text-align: left; vertical-align: middle;"><?= __d('contacts', 'Path'); ?></th>
                <td style="text-align: left; vertical-align: middle;" width="75%"><?= $message->contact_path; ?></td>
            <tr>
            <tr>
                <th style="text-align: left; vertical-align: middle;"><?= __d('contacts', 'Submitted On'); ?></th>
                <td style="text-align: left; vertical-align: middle;" width="75%"><?= $message->created_at->formatLocalized(__d('contacts', '%d %b %Y, %R')); ?></td>
            <tr>
        </table>
    </div>
</div>

<a class="btn btn-primary col-sm-2" href="<?= site_url('admin/contacts/' .$contact->id .'/messages?offset=' .Input::get('offset', 1)); ?>"><?= __d('contacts', '<< Previous Page'); ?></a>

<div class="clearfix"></div>
<br>

</section>
