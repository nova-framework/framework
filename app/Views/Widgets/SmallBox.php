<!-- small box -->
<div class="small-box bg-<?= $color; ?>">
    <div class="inner">
        <h3><?= $users ?></h3>

        <p><?= __d('users', 'Registered Users'); ?></p>
    </div>
    <div class="icon">
        <i class="ion ion-person-add"></i>
    </div>
    <a href="<?= site_url('admin/users'); ?>" class="small-box-footer"><?= __d('users', 'More info'); ?> <i class="fa fa-arrow-circle-right"></i></a>
</div>
