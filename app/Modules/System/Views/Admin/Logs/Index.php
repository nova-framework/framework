<section class="content-header">
    <h1><?= __d('system', 'Logs'); ?></h1>
    <ol class="breadcrumb">
        <li><a href='<?= site_url('admin/dashboard'); ?>'><i class="fa fa-dashboard"></i> <?= __d('system', 'Dashboard'); ?></a></li>
        <li><?= __d('system', 'Logs'); ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= Session::getMessages(); ?>

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('users', 'Logs Management'); ?></h3>
    </div>
    <div class="box-body">
        <a class='btn btn-danger' href='#' data-toggle='modal' data-target='#confirm_clearing' title='<?= __d('system', 'Clear the Logs'); ?>' role='button'><i class='fa fa-bomb'></i> <?= __d('system', 'Clear the Logs'); ?></a>
    </div>
</div>

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('system', 'User Actions'); ?></h3>
        <div class="box-tools">
        <?= $links; ?>
        </div>
    </div>
    <div class="box-body no-padding">
<?php if (! empty($logs)) { ?>
        <table id='left' class='table table-striped table-hover responsive'>
            <tr class="bg-navy disabled">
                <th style='text-align: center; vertical-align: middle;'><?= __d('system', 'ID'); ?></th>
                <th style='text-align: center; vertical-align: middle;'><?= __d('system', 'Date'); ?></th>
                <th style='text-align: center; vertical-align: middle;'><?= __d('system', 'Author'); ?></th>
                <th style='text-align: center; vertical-align: middle;'><?= __d('system', 'Group'); ?></th>
                <th style='text-align: center; vertical-align: middle;'><?= __d('system', 'Message'); ?></th>
            </tr>
<?php
    foreach ($logs as $log) {
        echo "
<tr>
    <td style='text-align: center; vertical-align: middle;' width='5%'>" .$log['id'] ."</td>
    <td style='text-align: center; vertical-align: middle;' width='15%'>" .$log['date'] ."</td>
    <td style='text-align: center; vertical-align: middle;' width='15%'>" .$log['username'] ."</td>
    <td style='text-align: center; vertical-align: middle;' width='15%'>" .$log['group'] ."</td>
    <td style='text-align: left; vertical-align: middle;' width='50%'>" .$log['message'] ."</td>
</tr>";

    }
?>
        </table>
<?php } else { ?>
        <div class="alert alert-warning" style="margin: 0 5px 5px;">
            <h4><i class="icon fa fa-warning"></i> <?php echo strftime("%d %b %Y, %R", time()) ." - "; ?> <?= __d('system', 'No registered Logs'); ?></h4>
            <?= __d('system', 'There are no registered Logs.'); ?>
        </div>
<?php } ?>
    </div>
</div>

<div class="modal modal-default" id="confirm_clearing">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-label="Close" data-dismiss="modal" class="close" type="button">
                <span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><?= __d('users', 'Clear the Logs?'); ?></h4>
            </div>
            <div class="modal-body">
                <p><?= __d('users', 'Are you sure you want to clear the Logs, the operation being irreversible?'); ?></p>
                <p><?= __d('users', 'Please click the button <b>Clear the Logs</b> to proceed, or <b>Cancel</b> to abandon the operation.'); ?></p>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-primary pull-left col-md-3" type="button"><?= __d('users', 'Cancel'); ?></button>
                <form action="<?= site_url('admin/logs/clear'); ?>" method="POST">
                    <input type="hidden" name="_token" value="<?= csrf_token(); ?>" />
                    <input type="submit" name="button" class="btn btn btn-danger pull-right" value="<?= __d('users', 'Clear the Logs'); ?>">
                </form>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
</div>

</section>
