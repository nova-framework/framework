<h3><?= __d('contacts', 'Contact Form'); ?></h3>
<hr>

<form action="<?= site_url('contacts'); ?>" method="POST">

<input type="hidden" name="_token" value="<?= csrf_token(); ?>" />
<input type="hidden" name="path" value="<?= $path; ?>" />

<div class="col-md-6 col-md-offset-1" style="margin-bottom: 50px;">
<?= $content; ?>
</form>

</div>

<div class="clear"></div>

