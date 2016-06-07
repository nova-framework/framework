<?php $targetUrl = site_url('register/verify/' .$token); // Calculate the target URL. ?>
<!DOCTYPE html>
<html lang="<?= LANGUAGE_CODE; ?>">
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <h2><?= __('Please verify your E-mail address'); ?></h2>

        <div>
            <?= __('Thanks for creating an Account with the {0}. Please follow the link below to verify your email address: {1}', SITETITLE, $targetUrl); ?><br/>
            <?= __('If you have problems, please paste the above URL into your web browser.'); ?>
        </div>
    </body>
</html>
