<?php
/**
 * Sample layout
 */

use Core\Language,
	Helpers\Form;

?>

<div class="page-header">
	<h1><?php echo $data['title'] ?></h1>
</div>

<p><?php echo $data['welcome_message'] ?></p>

<a class="btn btn-md btn-success" href="<?php echo DIR;?>subpage">
	<?php echo Language::show('open_subpage', 'Welcome'); ?>
</a>
	<a class="btn btn-md btn-default" href="<?php echo DIR;?>form">
		Form Examples
	</a>
<hr>
