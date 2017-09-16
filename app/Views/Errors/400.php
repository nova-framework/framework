<div class="container content">
    <div class="row">
        <div class="col-md-12">

            <h1>400</h1>

            <?= __('Referrer: {0}', Request::header('referer')); ?>

            <hr />

            <h3><?= __('Bad Request'); ?></h3>
            <p><?= __('This could be the result of an invalid Page request.'); ?></p>

        </div>
    </div>
</div>
