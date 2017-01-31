<!-- small box -->
<div class="small-box bg-<?= $color; ?>">
    <div class="inner">
        <h3><?= $title; ?></h3>

        <p><?= $text; ?></p>
    </div>
    <div class="icon">
        <i class="ion ion-<?= $icon; ?>"></i>
    </div>
    <a href="<?= $url; ?>" class="small-box-footer"><?= __d('users', 'More info'); ?> <i class="fa fa-arrow-circle-right"></i></a>
</div>
