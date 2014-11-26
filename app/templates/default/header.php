<!DOCTYPE html>
<html lang="<?php echo LANGUAGE_CODE; ?>">
<head>
	<meta charset="utf-8">
	<title><?php echo $data['title'].' - '.SITETITLE; //SITETITLE defined in app/core/config.php ?></title>
	<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css" rel="stylesheet">
	<link href="<?php echo \helpers\url::get_template_path();?>css/style.css" rel="stylesheet">
	<?php if(isset($data['css'])) { ?>
		<?php 
		if(is_array($data['css'])){
			foreach ($data['css'] as $row){
				echo "<link href='$row' rel='stylesheet'>";
			}
		} else {
			echo "<link href='".$data['css']."' rel='stylesheet'>";
		}
	}
	?>
</head>
<body>

<div class="wrapper">
