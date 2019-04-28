<?php

use Modules\Content\Models\Taxonomy;
use Modules\Content\Support\Facades\ContentLabel as Labels;
use Modules\Content\Support\MenuItemsNestable as Nestable;

?>
<ol class="dd-list">
    <?php foreach ($items as $item) { ?>
    <?php $label = Labels::get($type = $item->menu_item_object, 'name', __d('content', 'Unknown [{0}]', $type)); ?>
    <?php if (empty($title = $item->title) && ! is_null($instance = $item->instance())) { ?>
    <?php $title = ($instance instanceof Taxonomy) ? $instance->name : $instance->title; ?>
    <?php } ?>
    <li class="dd-item dd3-item" data-id="<?= $itemId = $item->id; ?>">
        <div class="dd-handle dd3-handle"> </div>
        <div class="dd3-content">
            <div class="pull-left" style="margin-top: 5px;"><?= $label; ?> : <?= $title; ?></div>
            <div class="btn-group pull-right" role="group" aria-label="...">
                <a class="btn btn-sm btn-danger" href="#" data-toggle="modal" data-target="#modal-delete-dialog" data-id="<?= $itemId; ?>" title="<?= __d('content', 'Delete this Menu Item'); ?>" role="button"><i class="fa fa-remove"></i></a>
                <a class="btn btn-sm btn-success" href="#" data-toggle="modal" data-target="#modal-edit-dialog" data-id="<?= $itemId; ?>" data-name="<?= $title; ?>" title="<?= __d('content', 'Edit this Menu Item'); ?>" role="button"><i class="fa fa-pencil"></i></a>
            </div>
        </div>
        <?= Nestable::render($item->children); ?>
    </li>
    <?php } ?>
</ol>
