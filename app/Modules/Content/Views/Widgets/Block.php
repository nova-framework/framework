<div class="content-block">
    <?php if ($block->block_show_title == 1) { ?>
    <h4 class="block-title"><?= $block->title; ?></h4>
    <?php } ?>
    <?php if (! empty($block->content)) { ?>
    <?php $blockContent = Template::compileString($block->getContent()); ?>
    <?php  eval('?>' .$blockContent); ?>
    <?php } ?>
    <?= $content; ?>
</div>
