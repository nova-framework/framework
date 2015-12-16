<?php
/**
 * Sample layout
 */

use Smvc\Core\Language;

?>

<div class="page-header">
	<h1><?php echo $data['title'] ?></h1>
</div>

<p><?php echo $data['welcome_message'] ?></p>

<a class="btn btn-md btn-success" href="<?php echo URI_PREFIX;?>subpage">
	<?php echo __('Open subpage'); ?>
</a>
