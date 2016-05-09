<?php
/**
 * Default Layout - a Layout similar with the classic Header and Footer files.
 */
?>
<!DOCTYPE html>
<html lang="<?php echo LANGUAGE_CODE; ?>">
<head>
    <meta charset="utf-8">
    <title><?= $title .' - ' .SITETITLE; ?></title>
<?php
echo $meta; //place to pass data / plugable hook zone

Assets::css([
    'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css',
    template_url('css/style.css', 'Default'),
]);

echo $css; //place to pass data / plugable hook zone
?>
</head>
<body>
<?= $afterBody; //place to pass data / plugable hook zone ?>

<div class="container">
    <p class="pull-right">
        <a href='<?= site_url('language/cs'); ?>'>Czech</a> |
        <a href='<?= site_url('language/en'); ?>'>English</a> |
        <a href='<?= site_url('language/de'); ?>'>German</a> |
        <a href='<?= site_url('language/fr'); ?>'>French</a> |
        <a href='<?= site_url('language/it'); ?>'>Italian</a> |
        <a href='<?= site_url('language/nl'); ?>'>Dutch</a> |
        <a href='<?= site_url('language/fa'); ?>'>Persian</a> |
        <a href='<?= site_url('language/pl'); ?>'>Polish</a> |
        <a href='<?= site_url('language/ro'); ?>'>Romanian</a> |
        <a href='<?= site_url('language/ru'); ?>'>Russian</a> |
        <a href='<?= site_url('language/es'); ?>'>Spanish</a>
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
    'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js',
]);

echo $js; //place to pass data / plugable hook zone
echo $footer; //place to pass data / plugable hook zone
?>

</body>
</html>
