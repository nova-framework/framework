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
        <h3 class="box-title"><?= __d('users', 'User Actions'); ?></h3>
        <div class="box-tools">
        <?= $links; ?>
        </div>
    </div>
    <div class="box-body no-padding">
<?php if (! empty($logs)) { ?>
        <table id='left' class='table table-striped table-hover responsive'>
            <tr class="bg-navy disabled">
                <th style='text-align: center; vertical-align: middle;'><?= __d('users', 'Date'); ?></th>
                <th style='text-align: center; vertical-align: middle;'><?= __d('users', 'Action'); ?></th>
                <th style='text-align: center; vertical-align: middle;'><?= __d('users', 'Model'); ?></th>
                <th style='text-align: center; vertical-align: middle;'><?= __d('users', 'Username'); ?></th>
                <th style='text-align: center; vertical-align: middle;'><?= __d('users', 'Real Name'); ?></th>
            </tr>
<?php
    foreach ($logs as $log) {
        echo "
<tr>
    <td style='text-align: center; vertical-align: middle;' width='15%'>" .$log['date'] ."</td>
    <td style='text-align: center; vertical-align: middle;' width='10%'>" .$log['action'] ."</td>
    <td style='text-align: center; vertical-align: middle;' width='40%'>" .$log['model'] ."</td>
    <td style='text-align: center; vertical-align: middle;' width='15%'>" .$log['username'] ."</td>
    <td style='text-align: center; vertical-align: middle;' width='20%'>" .$log['realname'] ."</td>
</tr>";

    }
?>
        </table>
<?php } else { ?>
        <div class="alert alert-warning" style="margin: 0 5px 5px;">
            <h4><i class="icon fa fa-warning"></i> <?php echo strftime("%d %b %Y, %R", time()) ." - "; ?> <?= __d('users', 'No registered Logs'); ?></h4>
            <?= __d('users', 'There are no registered Logs.'); ?>
        </div>
<?php } ?>
    </div>
</div>

</section>

</section>
