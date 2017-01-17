<section class="content-header">
    <h1><?= __d('system', 'Notifications'); ?></h1>
    <ol class="breadcrumb">
        <li><a href='<?= site_url('admin/dashboard'); ?>'><i class="fa fa-dashboard"></i> <?= __d('system', 'Dashboard'); ?></a></li>
        <li><?= __d('system', 'Notifications'); ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= Session::getMessages(); ?>

<div class="row">
    <div class="col-md-12">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title"><?= __d('system', 'Unread Notifications'); ?></h3>
            </div>
            <div class="box-body <?= ! $notifications->isEmpty() ? 'no-padding' : '' ?>">
                <?php if (! $notifications->isEmpty()) { ?>

                <table class='table table-striped table-hover responsive'>
                    <tr class="bg-navy disabled">
                        <th style='text-align: center; vertical-align: middle;'><?= __d('system', 'Sent At'); ?></th>
                        <th style='text-align: center; vertical-align: middle;'><?= __d('system', 'Subject'); ?></th>
                        <th style='text-align: center; vertical-align: middle;'><?= __d('system', 'Message'); ?></th>
                    </tr>
                    <?php foreach ($notifications->all() as $item) { ?>
                    <tr>
                        <td style="text-align: center; vertical-align: middle;" width="15%"><?= $item->created_at->formatLocalized('%d %b %Y, %H:%M'); ?></td>
                        <td style="text-align: center; vertical-align: middle;" width='30%'><?= $item->subject; ?></td>
                        <td style="text-align: left; vertical-align: middle;" width="55%"><?= $item->body; ?></td>
                    </tr>
                    <?php } ?>
                </table>

                <?php } else { ?>
                <p><?= __d('system', 'You have no unread notifications.'); ?></p>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

</section>
