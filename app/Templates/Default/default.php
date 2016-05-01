<?php
/**
 * Default Composed Layout - make a layout from the classic Header and Footer files.
 */

require dirname(__FILE__) .'/header.php';

echo $content;

require dirname(__FILE__) .'/footer.php';
