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
    <?php if (Auth::check()) { ?>
    <p class="pull-right" style="margin-top: 10px;">
        <a class="btn btn-sm btn-primary" href='<?= site_url('logout'); ?>'>Logout</a>
    </p>
    <div class="clearfix"></div>
    <?php } ?>

    <div style="padding-top: 50px;">
    <?= $content; ?>
    </div>
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
