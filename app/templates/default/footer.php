</div>

<!--
You can use jquery from JS dir or get latest script from jQuery servers
<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
-->

<script src="<?php echo \helpers\url::get_template_path();?>js/jquery.js"></script>

<?php if(isset($data['js'])) : ?>

	<!-- JS plugins -->
	<?php foreach ($data['js'] as $row): ?>
		<script src="<?php echo $row; ?>"></script>
	<?php endforeach ?>

<?php endif ?>

<?php if(isset($data['jq'])) : ?>

	<!-- JS scripts -->
	<script>
		$(document).ready(function(){
			<?php echo $data['jq']."\n";?>
		});
	</script>

<?php endif ?>

</body>
</html>
