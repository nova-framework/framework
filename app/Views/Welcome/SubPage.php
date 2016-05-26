<div class="page-header">
	<h1><?=$title;?></h1>
</div>

<pre>'<?= var_export(Session::all(), true); ?></pre>

<p><?=$welcomeMessage;?></p>

<a class="btn btn-md btn-success" href="<?=DIR;?>">
	<?php echo Language::show('backHome', 'Welcome'); ?>
</a>
