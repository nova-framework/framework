</div>

<!--
You can use jquery from JS dir or get latest script from jQuery servers
<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
-->

<script src="<?php echo \helpers\url::get_template_path();?>js/jquery.js"></script>
<?php if(isset($data['js'])) { ?>
	<!-- JS plugins -->
	<?php 
	if(is_array($data['js'])){
		foreach ($data['js'] as $row){
			echo "<script src='$row'></script>\n";
		}
	} else {
		echo "<script src='".$data['js']."'></script>\n";
	}
}
if(isset($data['jq'])){ ?>
	<!-- JS scripts -->
	<script>
		$(document).ready(function(){
			<?php echo $data['jq']."\n";?>
		});
	</script>

<?php } ?>
</body>
</html>
