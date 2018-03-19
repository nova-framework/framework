<link rel="stylesheet" type="text/css" href="<?= resource_url('css/jquery.nestable.css', 'content'); ?>">
<script type="text/javascript" src="<?= resource_url('js/jquery.nestable.js', 'content'); ?>"></script>

<section class="content-header">
    <h1><?= $title; ?></h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('admin/dashboard'); ?>"><i class="fa fa-dashboard"></i> <?= __d('content', 'Dashboard'); ?></a></li>
        <li><?= __d('content', 'Block Positions'); ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= View::fetch('Partials/Messages'); ?>

<div class="box box-default">
    <div class="box-header <?= ($positions->count() > 0) ? 'with-border' : ''; ?>">
        <h3 class="box-title"><?= __d('content', 'Registered Widget Positions'); ?></h3>
    </div>
    <div class="box-body">
        <?php if (! $positions->isEmpty()) { ?>
        <div style="padding: 5px; text-align: center;"><big><?= __d('content', '<b>{0}</b> Widget Position(s) have Blocks registered.', $positions->count()); ?></big></div>
        <div style="padding-bottom: 5px; text-align: center;"><?= __d('content', 'Please use the handles to order the Blocks, then submit your changes clicking the button bellow.'); ?></div>
        <?php } else { ?>
        <div class="alert alert-warning" style="margin: 0 5px 5px;">
            <h4><i class="icon fa fa-warning"></i> <?= strftime("%d %b %Y, %R", time()) ." - "; ?> <?= __d('content', 'No registered Block Positions'); ?></h4>
            <?= __d('content', 'There are no registered Block Positions.'); ?>
        </div>
        <?php } ?>
    </div>
</div>

<div class="row">

<?php $count = 0; ?>
<?php foreach ($positions as $name => $blocks) { ?>

<?php

usort($blocks, function ($a, $b)
{
    if ($a->menu_order == $b->menu_order) return 0;

    return ($a->menu_order > $b->menu_order) ? 1 : -1;
});

?>

<?php if (($count % 3) == 0) { ?>
<div class="clearfix"></div>
<?php }; ?>
<?php $count++; ?>

<div class="col-md-4">

<form id="position-items-form-<?= $name; ?>" action="<?= site_url('admin/blocks'); ?>" method='POST' role="form">

<div class="box box-widget">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('content', 'Widgets Position: <b>{0}</b>', $name); ?></h3>
    </div>
    <div class="box-body">
        <div class="dd" id="widgets-position-<?= $name; ?>">
            <ol class="dd-list">
                <?php foreach ($blocks as $block) { ?>
                <li class="dd-item dd3-item dd-nodrag" data-id="<?= $block->id; ?>">
                    <div class="dd-handle dd3-handle"> </div>
                    <div class="dd3-content">
                        <div style="margin-top: 5px;"><?= $block->title; ?></div>
                    </div>
                </li>
                <?php } ?>
            </ol>
        </div>
    </div>
    <div class="box-footer">
        <input type="submit" name="submit" class="btn btn-success col-sm-5 pull-right" value="<?= __d('content', 'Save the order'); ?>" />
    </div>
</div>

<input type="hidden" name="position" value="<?= $name; ?>" />
<input type="hidden" name="_token"   value="<?= csrf_token(); ?>" />

</form>

</div>

<script>

$(function() {
    $('#widgets-position-<?= $name; ?>').nestable({
        listNodeName: 'ol',
        expandBtnHTML: '',
        collapseBtnHTML: '',

        //
        maxDepth: 1,
        group: '<?= $name; ?>',
    });

    $('#position-items-form-<?= $name; ?>').submit(function(event) {
        $(this).find('.items-form-value').remove();

        var items = $('#widgets-position-<?= $name; ?>').nestable('serialize');

        var data = JSON.stringify(items);

        $(this).append('<input class="items-form-value" type="hidden" name="items" value=\'' + data + '\'>');
    });
});

</script>

<?php } ?>

</div>

</section>
