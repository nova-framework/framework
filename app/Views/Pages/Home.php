<?php

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

if (Config::get('app.debug') !== true) {
    throw new NotFoundHttpException('Please replace app/Views/Pages/Home.php with your own version.');
}

?>

<div class="row">
    <h1 class="text-center" style="margin-bottom: 15px;">Welcome to Nova Framework</h1>
    <br>

    <div class="alert alert-warning text-center">
        <p>Please be aware that this page will not be shown if you turn off debug mode unless you replace <code>app/Views/Pages/Home.php</code> with your own version.</p>
    </div>
    <div id="url-rewriting-warning" class="alert alert-danger url-rewriting">
        <p><i class='fa fa-close '></i> URL rewriting is NOT properly configured on your server.</p>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <h4><strong>Environment</strong></h4>
        <hr style="margin-top: 0">

        <ul class="list-unstyled">

            <?php if (version_compare(PHP_VERSION, '7.1.3', '>=')) { ?>
            <li><i class='fa fa-check text-success'></i> Your version of PHP is 7.1.3 or higher (detected <b><?= PHP_VERSION; ?></b>).</li>
            <?php } else { ?>
            <li><i class='fa fa-close text-danger'></i> Your version of PHP is too low. You need PHP 7.1.3 or higher to use Mini Nova (detected <b><?= PHP_VERSION; ?></b>).</li>
            <?php } ?>

            <?php if (extension_loaded('fileinfo')) { ?>
            <li><i class='fa fa-check text-success'></i> Your version of PHP has the <b>FileInfo</b> extension loaded.</li>
            <?php } else { ?>
            <li><i class='fa fa-close text-danger'></i> Your version of PHP does NOT have the <b>FileInfo</b> extension loaded.</li>
            <?php } ?>

            <?php if (extension_loaded('openssl')) { ?>
            <li><i class='fa fa-check text-success'></i> Your version of PHP has the <b>OpenSSL</b> extension loaded.</li>
            <?php } else { ?>
            <li><i class='fa fa-close text-danger'></i> Your version of PHP does NOT have the <b>OpenSSL</b> extension loaded.</li>
            <?php } ?>

            <?php if (extension_loaded('intl')) { ?>
            <li><i class='fa fa-check text-success'></i> Your version of PHP has the <b>INTL</b> extension loaded.</li>
            <?php } else { ?>
            <li><i class='fa fa-close text-danger'></i> Your version of PHP does NOT have the <b>INTL</b> extension loaded.</li>
            <?php } ?>

            <?php if (extension_loaded('mbstring')) { ?>
            <li><i class='fa fa-check text-success'></i> Your version of PHP has the <b>MBString</b> extension loaded.</li>
            <?php } else { ?>
            <li><i class='fa fa-close text-danger'></i> Your version of PHP does NOT have the <b>MBString</b> extension loaded.</li>;
            <?php } ?>

        </ul>
    </div>
    <div class="col-md-6">
        <h4><strong>Filesystem</strong></h4>
        <hr style="margin-top: 0">

        <ul class="list-unstyled">

            <?php $path = Config::get('routing.assets.path', base_path('assets')); ?>
            <?php if (is_writable($path)) { ?>
            <li><i class='fa fa-check text-success'></i> Your assets directory is writable.</li>
            <?php } else { ?>
            <li><i class='fa fa-close text-danger'></i> Your assets directory is NOT writable.</li>
            <?php } ?>

            <?php $path = storage_path('framework/cache'); ?>
            <?php if (is_writable($path)) { ?>
            <li><i class='fa fa-check text-success'></i> Your cache directory is writable.</li>
            <?php } else { ?>
            <li><i class='fa fa-close text-danger'></i> Your cache directory is NOT writable.</li>
            <?php } ?>

            <?php $path = storage_path('logs'); ?>
            <?php if (is_writable($path)) { ?>
            <li><i class='fa fa-check text-success'></i> Your logs directory is writable.</li>
            <?php } else { ?>
            <li><i class='fa fa-close text-danger'></i> Your logs directory is NOT writable.</li>
            <?php } ?>

            <?php $path = storage_path('framework/sessions'); ?>
            <?php if (is_writable($path)) { ?>
            <li><i class='fa fa-check text-success'></i> Your sessions directory is writable.</li>
            <?php } else { ?>
            <li><i class='fa fa-close text-danger'></i> Your sessions directory is NOT writable.</li>
            <?php } ?>

            <?php $path = storage_path('framework/views'); ?>
            <?php if (is_writable($path)) { ?>
            <li><i class='fa fa-check text-success'></i> Your compiled views directory is writable.</li>
            <?php } else { ?>
            <li><i class='fa fa-close text-danger'></i> Your compiled views directory is NOT writable.</li>
            <?php } ?>

        </ul>
    </div>
</div>
