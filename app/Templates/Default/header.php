<!DOCTYPE html>
<html lang="<?php echo LANGUAGE_CODE; ?>">
<head>
    <meta charset="utf-8">
    <title><?php echo $title.' - '.SITETITLE;?></title>
    <?php
    echo $meta;//place to pass data / plugable hook zone
    Assets::css([
        'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css',
        Url::templatePath().'css/style.css',
    ]);
    echo $css; //place to pass data / plugable hook zone
    ?>
</head>
<body>
<?php echo $afterBody; //place to pass data / plugable hook zone?>

<div class="container">

<p>
<a href='<?=DIR;?>language/cs'>Cs</a>
<a href='<?=DIR;?>language/en'>English</a>
<a href='<?=DIR;?>language/de'>Dutch</a>
<a href='<?=DIR;?>language/fr'>French</a>
<a href='<?=DIR;?>language/it'>Italian</a>
<a href='<?=DIR;?>language/nl'>Nl</a>
<a href='<?=DIR;?>language/pl'>Pl</a>
<a href='<?=DIR;?>language/ro'>Ro</a>
<a href='<?=DIR;?>language/ru'>Ru</a>
</p>

<p><img src='<?=Url::templatePath();?>images/nova.png' alt='<?=SITETITLE;?>'></p>
