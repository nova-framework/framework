<?php

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

if (Config::get('app.debug') !== true):
    throw new NotFoundException('Please replace app/Views/Pages/Home.php with your own version.');
endif;

?>

<div class="row">
    <h1 class="text-center" style="margin-bottom: 25px;">Welcome to Nova Framework <?= VERSION; ?></h1>
    <br>

    <div class="alert alert-warning text-center">
        <p>Please be aware that this page will not be shown if you turn off debug mode unless you replace <code>app/Views/Pages/Home.php</code> with your own version.</p>
    </div>
    <div id="url-rewriting-warning" class="alert alert-danger url-rewriting">
        <p><i class='fa fa-close'></i> URL rewriting is NOT properly configured on your server.</p>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <h4><strong>Environment</strong></h4>
        <hr style="margin-top: 0">

        <ul class="list-unstyled">
        <?php if (version_compare(PHP_VERSION, '5.6.0', '>=')): ?>
            <li><i class='fa fa-check'></i> Your version of PHP is 5.6.0 or higher (detected <b><?= PHP_VERSION; ?></b>).</li>
        <?php else: ?>
            <li><i class='fa fa-close'></i> Your version of PHP is too low. You need PHP 5.6.0 or higher to use Mini Nova (detected <b><?= PHP_VERSION; ?></b>).</li>
        <?php endif; ?>

        <?php if (extension_loaded('fileinfo')): ?>
            <li><i class='fa fa-check'></i> Your version of PHP has the <b>FileInfo</b> extension loaded.</li>
        <?php else: ?>
            <li><i class='fa fa-close'></i> Your version of PHP does NOT have the <b>FileInfo</b> extension loaded.</li>
        <?php endif; ?>

        <?php if (extension_loaded('openssl')): ?>
            <li><i class='fa fa-check'></i> Your version of PHP has the <b>OpenSSL</b> extension loaded.</li>
        <?php else: ?>
            <li><i class='fa fa-close'></i> Your version of PHP does NOT have the <b>OpenSSL</b> extension loaded.</li>
        <?php endif; ?>

        <?php if (extension_loaded('intl')): ?>
            <li><i class='fa fa-check'></i> Your version of PHP has the <b>INTL</b> extension loaded.</li>
        <?php else: ?>
            <li><i class='fa fa-close'></i> Your version of PHP does NOT have the <b>INTL</b> extension loaded.</li>
        <?php endif; ?>

        <?php if (extension_loaded('mbstring')): ?>
            <li><i class='fa fa-check'></i> Your version of PHP has the <b>MBString</b> extension loaded.</li>
        <?php else: ?>
            <li><i class='fa fa-close'></i> Your version of PHP does NOT have the <b>MBString</b> extension loaded.</li>;
        <?php endif; ?>
        </ul>
    </div>
    <div class="col-md-6">
        <h4><strong>Filesystem</strong></h4>
        <hr style="margin-top: 0">

        <ul class="list-unstyled">
        <?php if (is_writable(base_path('assets'))): ?>
            <li><i class='fa fa-check'></i> Your assets directory is writable.</li>
        <?php else: ?>
            <li><i class='fa fa-close'></i> Your assets directory is NOT writable.</li>
        <?php endif; ?>

        <?php if (is_writable(storage_path('cache'))): ?>
            <li><i class='fa fa-check'></i> Your cache directory is writable.</li>
        <?php else: ?>
            <li><i class='fa fa-close'></i> Your cache directory is NOT writable.</li>
        <?php endif; ?>

        <?php if (is_writable(storage_path('logs'))): ?>
            <li><i class='fa fa-check'></i> Your logs directory is writable.</li>
        <?php else: ?>
            <li><i class='fa fa-close'></i> Your logs directory is NOT writable.</li>
        <?php endif; ?>

        <?php if (is_writable(storage_path('sessions'))): ?>
            <li><i class='fa fa-check'></i> Your sessions directory is writable.</li>
        <?php else: ?>
            <li><i class='fa fa-close'></i> Your sessions directory is NOT writable.</li>
        <?php endif; ?>

        <?php if (is_writable(storage_path('views'))): ?>
            <li><i class='fa fa-check'></i> Your compiled views directory is writable.</li>
        <?php else: ?>
            <li><i class='fa fa-close'></i> Your compiled views directory is NOT writable.</li>
        <?php endif; ?>
    </div>
</div>
