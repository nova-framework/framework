<div class="container">
    <div class="row" style="margin-top: 10%; margin-bottom: 10%;">
        <div class="col-sm-6 col-md-4 col-md-offset-4">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-lock"></i> <?= __d('content', 'Enter password to unlock'); ?></h3>
                </div>
                <div class="panel-body">
                    <div class="center-block text-center">
                        <img class="img-thumbnail img-circle" style="margin-bottom: 20px;" src="<?= resource_url('images/protected-content.jpg', 'Content'); ?>" alt="">
                        <form action="<?= site_url('content/' .$post->id); ?>" method="POST" role="form">
                            <div class="input-group" style="margin-bottom: 15px;">
                                <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                                <input name="password" id="unlock-content-input" type="password" class="form-control" placeholder="Password" required autofocus>
                            </div>
                            <input name="submit" id="unlock-content-submit" type="submit" class="btn btn-success col-md-6 pull-right" value="<?= __d('content', 'Unlock'); ?>" />
                            <?= csrf_field(); ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

