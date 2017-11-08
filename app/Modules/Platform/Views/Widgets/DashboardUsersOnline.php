<div class="col-sm-12">

<div class="box box-default">
    <div class="box-header">
        <h3 class="box-title"><?= __d('platform', 'Users on-line'); ?></h3>
        <div class="box-tools">
        <?= $users->links(); ?>
        </div>
    </div>
    <div class="box-body no-padding">
        <?php if (! $users->isEmpty()) { ?>
        <table id="left" class="table table-striped table-hover responsive">
            <tr class="bg-navy disabled">
                <th style="text-align: center; vertical-align: middle;"><?= __d('platform', 'ID'); ?></th>
                <th style="text-align: center; vertical-align: middle;"><?= __d('platform', 'Username'); ?></th>
                <th style="text-align: center; vertical-align: middle;"><?= __d('platform', 'Name and Surname'); ?></th>
                <th style="text-align: center; vertical-align: middle;"><?= __d('platform', 'E-mail'); ?></th>
                <th style="text-align: center; vertical-align: middle;"><?= __d('platform', 'Roles'); ?></th>
                <th style="text-align: right; vertical-align: middle;"><?= __d('platform', 'Operations'); ?></th>
            </tr>
            <?php foreach ($users->getItems() as $user) { ?>
            <tr>
                <td style="text-align: center; vertical-align: middle;" width='5%'><?= $user->id; ?></td>
                <td style="text-align: center; vertical-align: middle;" width='20%'><?= $user->username; ?></td>
                <td style="text-align: center; vertical-align: middle;" width='25%'><?= $user->realname(); ?></td>
                <td style="text-align: center; vertical-align: middle;" width='20%'><?= $user->email; ?></td>
                <td style="text-align: center; vertical-align: middle;" width='15%'><?= implode(', ', $user->roles->lists('name')); ?></td>
                <td style="text-align: right; vertical-align: middle;" width='15%'>
                    <div class='btn-group' role='group' aria-label='...'>-</div>
                </td>
            </tr>
            <?php } ?>
        </table>
        <?php } else { ?>
        <div class="alert alert-info" style="margin: 0 5px 5px;">
            <h4><i class="icon fa fa-warning"></i> <?= strftime("%d %b %Y, %R", time()) ." - "; ?> <?= __d('platform', 'No Users on-line'); ?></h4>
            <?= __d('platform', 'There are no Users on-line.'); ?>
        </div>
        <?php } ?>
    </div>
</div>

</div>
