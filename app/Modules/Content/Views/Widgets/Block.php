<?php if (isset($post->block_show_title) && ($post->block_show_title === true)) { ?>
<h3><?= $post->title; ?></h3>
<?php } ?>
<?= $post->getContent(); ?>
