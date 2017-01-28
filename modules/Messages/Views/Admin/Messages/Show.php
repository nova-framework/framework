<section class="content-header">
    <h1><?= __d('messages', 'Show Message'); ?></h1>
    <ol class="breadcrumb">
        <li><a href='<?= site_url('admin/dashboard'); ?>'><i class="fa fa-dashboard"></i> <?= __d('messages', 'Dashboard'); ?></a></li>
        <li><a href='<?= site_url('admin/messages'); ?>'><i class="fa fa-envelope"></i> <?= __d('messages', 'Messages'); ?></a></li>
        <li><?= __d('messages', 'Show Message'); ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= Session::getMessages(); ?>

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= $message->subject; ?></h3>
    </div>
    <div class="box-body">
        <div class="col-md-8 col-md-offset-2 col-sm-8 col-sm-offset-2">
            <div class="clearfix"></div>
            <br>

        <!-- Status -->
            <div class="media">
                <div class="pull-left">
                    <img  style="height: 50px; width: 50px" src="<?= $message->sender->present()->picture(); ?>" alt="<?= $message->sender->present()->name(); ?>" class="media-object">
                </div>
                <div class="media-body">
                    <h4 class="media-heading"><?= $message->sender->present()->name(); ?></h4>
                    <p><?= e($message->body); ?></p>
                    <ul class="list-inline text-muted">
                        <li><?= $message->created_at->diffForHumans(); ?></li>
                    </ul>
                </div>
            </div>
            <?php if (! $message->replies->isEmpty()) { ?>
            <hr style="margin-bottom: 0;">
            <?php } else { ?>
            <br>
            <?php } ?>
            <!-- Replies -->
            <?php foreach($message->replies as $reply) { ?>
            <div class="media comment-block">
                <a class="pull-left" href="<?= site_url('user/' .$reply->sender->username); ?>">
                    <img  style="height: 50px; width: 50px" src="<?= $reply->sender->present()->picture(); ?>" alt="<?= $reply->sender->present()->name(); ?>" class="media-object">
                </a>
                <div class="media-body">
                    <h4 class="media-heading"><?= $reply->sender->present()->name(); ?></h4>
                    <p><?= e($reply->body); ?></p>
                    <ul class="list-inline text-muted">
                        <li><?= $reply->created_at->diffForHumans(); ?></li>
                    </ul>
                </div>
            </div>
            <?php } ?>
            <!-- Reply Form -->
            <form action="<?= site_url('admin/messages/' .$message->id); ?>" role="form" method="POST">

            <div class="form-group <?= $errors->has('reply') ? 'has-error' : ''; ?>">
                <textarea style="resize: none" name="reply" class="form-control" placeholder="<?= __d('messages', 'Reply to this {0, select, 0 {message} other {thread}}...', $message->replies->count()); ?>" rows="3"></textarea>
                <?php if ($errors->has('reply')) { ?>
                <span class="help-block"><?= $errors->first(); ?></span>
                <?php } ?>
            </div>
            <button type="submit" class="btn btn-success col-sm-2 pull-right"><i class='fa fa-reply'></i> <?= __d('messages', 'Reply'); ?></button>
            <input type="hidden" name="_token" value="<?= csrf_token(); ?>">

            </form>
        </div>
    </div>
</div>

<a class='btn btn-primary' href='<?= site_url('messages'); ?>'><?= __d('messages', '<< Previous Page'); ?></a>

</section>
