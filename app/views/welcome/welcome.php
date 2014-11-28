
<div class="page-header">
	<h1><?php echo $data['title'] ?></h1>
</div>

<p><?php echo $data['welcome_message'] ?></p>

<a class="btn btn-md btn-success" href="<?php echo DIR ?>subpage">
	<?php echo core\language::show('open_subpage', 'welcome') ?>
</a>