<?php $format = __d('content', '%B %Y'); ?>
<?php foreach ($items as $name => $count) { ?>
<div style="padding: 10px 0 10px 0; border-bottom: 1px solid #eee;">
    <?php list ($year, $month) = explode('/', $name); ?>
    <a class="pull-left" href="<?= url('content/archive', array($year, $month)); ?>"><?= Carbon\Carbon::parse($name .'/1')->formatLocalized($format); ?></a> <span class="pull-right"><?= $count; ?></span>
    <div class="clearfix"></div>
</div>
<?php } ?>
