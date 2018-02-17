<section class="content-header">
    <h1><?= __d('messages', 'Show Message'); ?></h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('dashboard'); ?>"><i class="fa fa-dashboard"></i> <?= __d('messages', 'Dashboard'); ?></a></li>
        <li><a href="<?= site_url('messages'); ?>"><?= __d('backend', 'Messages'); ?></a></li>
        <li><?= __d('messages', 'Show Message'); ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= Session::getMessages();

use App\Models\User;

?>

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= $message->subject; ?></h3>
    </div>
    <div class="box-body">
        <div class="col-md-10 col-md-offset-1 col-sm-8 col-sm-offset-2">
            <!-- Status -->
            <div class="media" style="margin-top: 5px;">
                <div class="pull-left">
                    <img class="img-thumbnail" style="height: 75px; width: 75px" src="<?= $message->sender->picture(); ?>" alt="<?= $message->sender->realname(); ?>" class="media-object">
                </div>
                <div class="media-body">
                    <h4 class="media-heading"><?= $message->sender->realname(); ?></h4>
                    <p><?= e($message->body); ?></p>
                    <ul class="list-inline text-muted">
                        <li><?= $message->created_at->diffForHumans(); ?></li>
                    </ul>
                </div>
            </div>

            <!-- Replies -->
            <?php foreach($message->replies as $reply) { ?>
            <hr style="margin: 0;">
            <div class="media comment-block" style="margin-top: 10px;">
                <a class="pull-left" href="<?= site_url('user/' .$reply->sender->username); ?>">
                    <img class="img-thumbnail" style="height: 75px; width: 75px" src="<?= $reply->sender->picture(); ?>" alt="<?= $reply->sender->realname(); ?>" class="media-object">
                </a>
                <div class="media-body">
                    <h4 class="media-heading"><?= $reply->sender->realname(); ?></h4>
                    <p><?= e($reply->body); ?></p>
                    <ul class="list-inline text-muted">
                        <li><?= $reply->created_at->diffForHumans(); ?></li>
                    </ul>
                </div>
            </div>
            <?php } ?>
            <br>

            <!-- Reply Form -->
            <form action="<?= site_url('messages/' .$message->id); ?>" role="form" method="POST">

            <div class="form-group <?= $errors->has('reply') ? 'has-error' : ''; ?>">
                <textarea style="resize: none" name="reply" class="form-control" placeholder="<?= __d('backend', 'Reply to this {0, select, 0 {message} other {thread}}...', $message->replies->count()); ?>" rows="3"></textarea>
                <?php if ($errors->has('reply')) { ?>
                <span class="help-block"><?= $errors->first(); ?></span>
                <?php } ?>
            </div>
            <button type="submit" class="btn btn-success col-sm-2 pull-right" style="margin-bottom: 5px;"><i class="fa fa-reply"></i> <?= __d('backend', 'Reply'); ?></button>

            <?= csrf_field(); ?>

            </form>
        </div>
    </div>
</div>

<a class="btn btn-primary col-sm-2" href="<?= site_url('messages'); ?>"><?= __d('backend', '<< Previous Page'); ?></a>

<div class="clearfix"></div>
<br>

</section>

