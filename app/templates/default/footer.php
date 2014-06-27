</div>

<script src="<?php echo \helpers\url::get_template_path();?>js/jquery.js"></script>
<?php echo $data['js']."\n";?>

<script>
$(document).ready(function(){
	<?php echo $data['jq']."\n";?>
});
</script>

</body>
</html>
