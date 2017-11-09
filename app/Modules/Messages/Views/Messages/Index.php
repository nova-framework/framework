<section class="content-header">
    <h1><?= __d('messages', 'Messages'); ?></h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('dashboard'); ?>"><i class="fa fa-dashboard"></i> <?= __d('messages', 'Dashboard'); ?></a></li>
        <li><?= __d('messages', 'Messages'); ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= Session::getMessages(); ?>

<?php if (Gate::allows('create', 'App\Modules\Messages\Models\Message')) { ?>

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('messages', 'Send a new Message'); ?></h3>
    </div>
    <div class="box-body">
        <a class="btn btn-success col-sm-2 pull-right" href="<?= site_url('messages/create'); ?>"><?= __d('messages', 'Send a new Message'); ?></a>
    </div>
</div>

<?php } ?>

<style>

.pagination {
    margin: 0;
}

</style>

<div class="box box-widget">
    <div class="box-header <?= ! $messages->isEmpty() ? 'with-border' : '' ?>">
        <h3 class="box-title"><?= __d('messages', 'Message Threads'); ?></h3>
        <div class="box-tools">
        <?= $messages->links(); ?>
        </div>
    </div>
    <div class="box-body no-padding">
        <?php if (! $messages->isEmpty()) { ?>
        <?php $count = 0; ?>
        <?php $total = $messages->count(); ?>

        <?php foreach($messages as $message) { ?>
        <?php $unread = $message->replies->where('receiver_id', $authUser->id)->where('is_read', 0)->count(); ?>
        <?php if (($message->sender_id !== $authUser->id) && ($message->is_read === 0)) $unread++; ?>

        <?php if ($count > 0) { ?>
        <hr style="margin: 0;">
        <?php } ?>
        <div class="media" style="margin-top: 0; padding: 10px;">
            <div class="pull-left">
                <img class="img-thumbnail media-object img-responsive" style="height: 80px; width: 80px" alt="<?= $message->sender->realname(); ?>" src="<?= $message->sender->picture(); ?>">
            </div>
            <div class="media-body">
                <div class="col-md-8">
                    <h4 class="media-heading"><a href="<?= site_url('messages/' .$message->id); ?>"><?= e($message->subject); ?></a> <?php if ($unread >  0) echo '<small class="label label-warning">' .$unread .'</small>'; ?></h4>
                    <p class="no-margin"><?= __d('messages', 'From <b>{0}</b>, to <b>{1}</b>', $message->sender->realname, $message->receiver->realname); ?></p>
                    <ul class="list-inline text-muted no-margin">
                        <li><?= __d('messages', '{0, plural, one{# reply} other{# replies}}', $message->replies->count()); ?></li>
                        <li><?= $message->created_at->diffForHumans(); ?></li>
                    </ul>
                </div>
                <div class="col-md-4 no-padding">
                    <a class="btn btn-sm btn-<?= ($unread > 0) ? 'warning' : 'primary'; ?> pull-right" title="<?= __d('messages', 'View this message and its replies'); ?>" href="<?= site_url('messages/' .$message->id); ?>"><i class="fa fa-search"></i> <?= __d('messages', 'View the Message(s)'); ?></a>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
        <?php $count++; ?>
        <?php } ?>
        <?php } else { ?>
        <div class="alert alert-info" style="margin: 0 5px 5px;">
            <h4><i class="icon fa fa-info"></i> <?= strftime("%d %b %Y, %R", time()) ." - "; ?> <?= __d('messages', 'No messages'); ?></h4>
            <?= __d('messages', 'You have no messages sent or received.'); ?>
        </div>
        <?php } ?>
    </div>
</div>

</section>

