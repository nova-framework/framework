<?php
use Helpers\Assets;
use Helpers\Url;
use Helpers\Hooks;

//initialise hooks
$hooks = Hooks::get();
?>
<!DOCTYPE html>
<html lang="<?php echo LANGUAGE_CODE; ?>">
<head>
	<meta charset="utf-8">
	<title><?php echo $title.' - '.SITETITLE;?></title>
	<?php
    echo $meta;//place to pass data / plugable hook zone
	Assets::css([
		'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css',
		Url::templatePath().'assets/css/style.css',
	]);
    echo $css; //place to pass data / plugable hook zone
	?>
</head>
<body>
<?php echo $afterBody; //place to pass data / plugable hook zone?>

<div class="container">
