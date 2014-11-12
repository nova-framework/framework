</div>

<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<!-- uncomment to use a local hosted version -->
<!--<script src="<?php echo \helpers\url::get_template_path();?>js/jquery.js"></script>-->
<?php echo $data['js']."\n";?>

<script>
$(document).ready(function(){
	<?php echo $data['jq']."\n";?>
});
</script>

</body>
</html>
