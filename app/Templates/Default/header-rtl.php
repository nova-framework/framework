<?php
/**
 * Default Header.
 */

// Generate the Language Changer menu.
$language = Language::code();

$languages = Config::get('languages');

//
$html = '';

foreach ($languages as $code => $info) {
    // Make bold the name of curent Language
    $linkName = ($language == $code) ? '<b>' .$info['name'] .'</b>' : $info['name'];

    $html .= '<a href="' .site_url('language/' .$code) .'">' .$linkName .'</a> | ' .PHP_EOL;
}

$langMenu = rtrim(trim($html), ' |') .PHP_EOL;
?>
<!DOCTYPE html>
<html lang="<?php echo LANGUAGE_CODE; ?>">
<head>
    <meta charset="utf-8">
    <title><?= $title .' - ' .SITETITLE; ?></title>
<?php
echo $meta; // Place to pass data / plugable hook zone

Assets::css([
    template_url('css/bootstrap-rtl.min.css', 'Default'),
    'https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css',
    Url::templatePath() .'css/style-rtl.css',
]);

echo $css; // Place to pass data / plugable hook zone
?>
</head>
<body>
<?= $afterBody; // Place to pass data / plugable hook zone ?>

<div class="container">

<p class="pull-right">
<?= $langMenu; ?>
</p>
<div class="clearfix"></div>

<p><img src='<?= Url::templatePath(); ?>images/nova.png' alt='<?= SITETITLE; ?>'></p>
