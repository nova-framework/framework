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

<?= View::fetch('Partials/Messages'); ?>

<div class="col-md-7" style="padding: 0;">

<div class="box box-default" style="min-height: 400px;">
    <div class="box-header">
        <h3 class="box-title"><?= __d('contacts', 'Message'); ?></h3>
    </div>
    <div class="box-body no-padding">
        <table id="left" class="table table-hover responsive">
            <tr class="bg-navy disabled">
                <th style="text-align: left; vertical-align: middle;"><?= __d('contacts', 'Field'); ?></th>
                <th style="text-align: left; vertical-align: middle;"><?= __d('contacts', 'Value'); ?></th>
            </tr>
            <tr>
                <th style="text-align: left; vertical-align: middle;"><?= __d('contacts', 'Submitted On'); ?></th>
                <td style="text-align: left; vertical-align: middle;" width="75%"><?= $message->created_at->formatLocalized(__d('contacts', '%d %b %Y, %R')); ?></td>
            <tr>
            <tr>
                <th style="text-align: left; vertical-align: middle;"><?= __d('contacts', 'Site Path'); ?></th>
                <td style="text-align: left; vertical-align: middle;" width="75%"><?= $message->path; ?></td>
            <tr>
            <tr>
                <th style="text-align: left; vertical-align: middle;"><?= __d('contacts', 'Remote IP'); ?></th>
                <td style="text-align: left; vertical-align: middle;" width="75%"><?= $message->author_ip; ?></td>
            <tr>
            <tr>
                <th style="text-align: left; vertical-align: middle;"><?= __d('contacts', 'Author'); ?></th>
                <td style="text-align: left; vertical-align: middle;" width="75%"><?= e($message->author); ?></td>
            <tr>
            <tr>
                <th style="text-align: left; vertical-align: middle;"><?= __d('contacts', 'E-mail Address'); ?></th>
                <td style="text-align: left; vertical-align: middle;" width="75%"><?= e($message->author_email); ?></td>
            <tr>
            <tr>
                <th style="text-align: left; vertical-align: middle;"><?= __d('contacts', 'Website'); ?></th>
                <td style="text-align: left; vertical-align: middle;" width="75%"><?= e($message->author_url ?: '-'); ?></td>
            <tr>
            <tr>
                <th style="text-align: left; vertical-align: middle;"><?= __d('contacts', 'Message'); ?></th>
                <td style="text-align: left; vertical-align: middle;" width="75%"><?= nl2br(e($message->content)); ?></td>
            <tr>
        </table>
    </div>
</div>

</div>

<div class="col-md-5" style="padding-right: 0;">

<?php $previewables = 0; ?>
<div class="box box-primary attachments" style="min-height: 400px;">
    <div class="box-header">
        <h3 class="box-title"><?= __d('requests', 'Attachments'); ?></h3>
    </div>
    <div class="box-body no-padding">
        <?php $attachments = $message->attachments; ?>
        <?php if (! $attachments->isEmpty()) { ?>
        <table id="files-table" class="table table-striped table-hover responsive">
            <tr class="bg-navy disabled">
                <th style="text-align: center; vertical-align: middle;" width="10%"><?= __d('requests', 'ID'); ?></th>
                <th style="text-align: center; vertical-align: middle;" width="55%"><?= __d('requests', 'File'); ?></th>
                <th style="text-align: center; vertical-align: middle;" width="15%"><?= __d('requests', 'Size'); ?></th>
                <th style="vertical-align: middle;" width="15%"><?= __d('requests', 'Operations'); ?></th>
            </tr>
            <?php foreach ($attachments as $attachment) { ?>
            <tr>
                <td style="text-align: center; vertical-align: middle; border-left: 0; border-bottom: 1px solid #f4f4f4;" width="10%"><?= $attachment->id; ?></td>
                <td style="text-align: center; vertical-align: middle; border-left: 0; border-bottom: 1px solid #f4f4f4;" width="55%"><?= $attachment->name; ?></td>
                <td style="text-align: center; vertical-align: middle; border-left: 0; border-bottom: 1px solid #f4f4f4;" width="15%"><?= human_size($attachment->size, 1); ?></td>
                <td style="vertical-align: middle; border-left: 0; border-bottom: 1px solid #f4f4f4;" width="15%">
                    <div class="btn-group pull-right actions" role="group" aria-label='...'>
                        <a class="btn btn-sm btn-success" href="<?= $attachment->url(true); ?>" title="<?= __d('requests', 'Download this Attachment'); ?>" role="button"><i class="fa fa-download"></i></a>
                        <?php if ($attachment->previewable()) { ?>
                        <?php $previewables++; ?>
                        <a class="btn btn-sm btn-warning" href="#" data-toggle="modal" data-target="#modal-preview-dialog" data-name="<?= $attachment->name; ?>" data-url="<?= $attachment->url(); ?>" title="<?= __d('requests', 'Show this Attachment'); ?>" role="button"><i class="fa fa-search"></i></a>
                        <?php } ?>
                    </div>
                </td>
            </tr>
            <?php } ?>
        </table>
        <?php } else { ?>
        <div class="alert alert-info" style="margin: 0 5px 5px;">
            <h4><i class="icon fa fa-info-circle"></i> <?= strftime("%d %b %Y, %R", time()) ." - "; ?> <?= __d('users', 'No attachments'); ?></h4>
            <?= __d('users', 'This request has no attached files.'); ?>
        </div>
        <?php } ?>
    </div>
</div>

</div>

<a class="btn btn-primary col-sm-2" href="<?= site_url('admin/contacts/' .$contact->id .'/messages?offset=' .Input::get('offset', 1)); ?>"><?= __d('contacts', '<< Previous Page'); ?></a>

<div class="clearfix"></div>
<br>

</section>

<?php if ($previewables > 0) { ?>
<?= View::fetch('Modules/Contacts::Partials/AttachmentPreview'); ?>
<?php } ?>
