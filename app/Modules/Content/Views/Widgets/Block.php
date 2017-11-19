<?php if ($post->block_show_title == 1) { ?>
<h4><?= e($post->title); ?></h4>
<?php } ?>
<?php  eval('?>' .$content); ?>
