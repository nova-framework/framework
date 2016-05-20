<?php
/**
 * Default RTL Layout - a Layout similar with the classic Header and Footer files.
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
<html lang="<?php echo LANGUAGE_CODE; ?>" dir="rtl">
<head>
    <meta charset="utf-8">
    <title><?= $title .' - ' .SITETITLE; ?></title>
<?php
echo $meta; // Place to pass data / plugable hook zone

Assets::css([
    template_url('css/bootstrap-rtl.min.css', 'Default'),
    template_url('css/bootstrap-rtl-theme.min.css', 'Default'),
    'https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css',
    template_url('css/style-rtl.css', 'Default'),
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
    <p>
        <img src='<?= template_url('images/nova.png', 'Default'); ?>' alt='<?= SITETITLE; ?>'>
    </p>

    <?= $content; ?>
</div>

<?php
Assets::js([
    'https://code.jquery.com/jquery-1.12.1.min.js',
    template_url('js/bootstrap-rtl.min.js', 'Default'),
]);

echo $js; // Place to pass data / plugable hook zone
echo $footer; // Place to pass data / plugable hook zone
?>

</body>
</html>
