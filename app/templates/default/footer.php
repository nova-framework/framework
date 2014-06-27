</div>

<script src="<?php echo helpers\url::get_template_path();?>js/jquery.js"></script>
<?php echo $data['js']."\n";?>

<?php if (isset($data['jq'])): ?>
<script>
$(document).ready(function(){
	<?php echo $data['jq']."\n";?>
});
</script>
<?php endif; ?>

</body>
</html>
