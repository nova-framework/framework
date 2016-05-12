<!DOCTYPE html>
<html lang="<?php echo LANGUAGE_CODE; ?>">
<head>
    <meta charset="utf-8">
    <title><?= $title .' - ' .SITETITLE; ?></title>
<?php
echo $meta; // Place to pass data / plugable hook zone

Assets::css([
    'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css',
    'https://maxcdn.bootstrapcdn.com/font-awesome/4.6.2/css/font-awesome.min.css',
    Url::templatePath() .'css/style.css',
]);

echo $css; // Place to pass data / plugable hook zone
?>
</head>
<body>
<?= $afterBody; // Place to pass data / plugable hook zone ?>

<div class="container">

<p class="pull-right">
<a href='<?=DIR;?>language/cs'>Czech</a> |
<a href='<?=DIR;?>language/en'>English</a> |
<a href='<?=DIR;?>language/de'>German</a> |
<a href='<?=DIR;?>language/fr'>French</a> |
<a href='<?=DIR;?>language/it'>Italian</a> |
<a href='<?=DIR;?>language/ja'>Japanese</a> |
<a href='<?=DIR;?>language/nl'>Dutch</a> |
<a href='<?=DIR;?>language/fa'>Persian</a> |
<a href='<?=DIR;?>language/pl'>Polish</a> |
<a href='<?=DIR;?>language/ro'>Romanian</a> |
<a href='<?=DIR;?>language/ru'>Russian</a> |
<a href='<?=DIR;?>language/es'>Spanish</a>
</p>
<div class="clearfix"></div>

<p><img src='<?= Url::templatePath(); ?>images/nova.png' alt='<?= SITETITLE; ?>'></p>
