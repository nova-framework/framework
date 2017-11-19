<?php if ($block->block_show_title == 1) { ?>
<h4><?= $block->title; ?></h4>
<?php } ?>
<?php  eval('?>' .$content); ?>
