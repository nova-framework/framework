<section class="content-header">
    <h1><?= __d('platform', 'Notifications'); ?></h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('dashboard'); ?>"><i class="fa fa-dashboard"></i> <?= __d('platform', 'Dashboard'); ?></a></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= Session::getMessages(); ?>

<form action="<?= site_url('notifications'); ?>" class="form-horizontal" method='POST' role="form">

<div class="box box-default">
    <div class="box-header">
        <h3 class="box-title"><?= __d('platform', 'Notifications'); ?></h3>
        <div class="box-tools">
        <?= $notifications->links(); ?>
        </div>
    </div>
    <div class="box-body no-padding">
        <?php $format = __d('platform', '%d %b %Y, %H:%M'); ?>
        <?php if (! $notifications->isEmpty()) { ?>
        <table class="table table-bordered table-striped table-hover responsive">
            <thead>
                <tr class="bg-navy disabled">
                    <th style="text-align: center; vertical-align: middle;">-</th>
                    <th style="text-align: left; vertical-align: middle;"><?= __d('platform', 'Message'); ?></th>
                    <th style="text-align: center; vertical-align: middle;"><?= __d('platform', 'URL'); ?></th>
                    <th style="text-align: center; vertical-align: middle;"><?= __d('platform', 'Sent At'); ?></th>
                    <th style="text-align: center; vertical-align: middle;"><?= __d('platform', 'Read At'); ?></th>
                    <th style="text-align: center; vertical-align: middle;"><input type="checkbox" value="" class="checkLeft1"></th>
                </tr>
            </thead>
            <tbody>
            <?php $url = Config::get('app.url'); ?>
            <?php foreach ($notifications->all() as $item) { ?>
            <?php $data = $item->data; ?>
                <tr>
                    <td style="text-align: center; vertical-align: middle; padding: 5px;" width="5%"><i class="fa fa-<?= isset($data['icon']) ? $data['icon'] : 'bell'; ?> text-<?= isset($data['color']) ? $data['color'] : 'aqua'; ?>"></i></td>
                    <td style="text-align: left; vertical-align: middle;" width='40%'><?= $data['message']; ?></td>
                    <td style="text-align: center; vertical-align: middle;" width='20%'><a href="<?= $data['link'] .'?read=' .$item->uuid; ?>" target="_blank"><?= str_replace($url, '/', $data['link']); ?></td>
                    <td style="text-align: center; vertical-align: middle;" width="15%"><?= $item->created_at->formatLocalized($format); ?></td>
                    <td style="text-align: center; vertical-align: middle;" width="15%"><?= ! empty($item->read_at) ? $item->read_at->formatLocalized($format) : '-'; ?></td>
                    <td style="text-align: center; vertical-align: middle; padding: 5px;" width="5%"><?php if (empty($item->read_at)) { ?><input type="checkbox" name="nid[]" value="<?= $item->id; ?>" class="checkGroup1"><?php } else { ?>-<?php } ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
        <?php } else { ?>
        <div class="alert alert-info" style="margin: 0 5px 5px;">
            <h4><i class="icon fa fa-info"></i> <?= strftime("%d %b %Y, %R", time()) ." - "; ?> <?= __d('platform', 'No notifications'); ?></h4>
            <?= __d('platform', 'You have no notifications.'); ?>
        </div>
        <?php } ?>
    </div>
    <?php if (! $notifications->isEmpty()) { ?>
    <div class="box-footer">
        <input type="submit" name="submit" id="submitButton1" class="btn btn-success col-sm-2 pull-right" value="<?= __d('users', 'Mark as read'); ?>" disabled="disabled">
    </div>
    <?php } ?>
</div>

<?= csrf_field(); ?>

</form>

</section>

<script language="javascript">

$(':checkbox[class=checkLeft1]').on('ifChanged', function() {
    var checkboxes = $(':checkbox[class=checkGroup1]');

    checkboxes.prop('checked', $(this).is(':checked'));

    checkboxes.iCheck('update');

    $('#submitButton1').prop('disabled', $(':checkbox[class="checkGroup1"]:checked').length == 0);
});

$(':checkbox[class=checkGroup1]').on('ifChanged', function (e) {
    $('#submitButton1').prop('disabled', $(':checkbox[class="checkGroup1"]:checked').length == 0);
});

</script>
