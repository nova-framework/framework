<section class="content-header">
    <h1><?= __d('messages', 'Messages'); ?></h1>
    <ol class="breadcrumb">
        <li><a href='<?= site_url('admin/dashboard'); ?>'><i class="fa fa-dashboard"></i> <?= __d('messages', 'Dashboard'); ?></a></li>
        <li><?= __d('messages', 'Messages'); ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= Session::getMessages(); ?>

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('messages', 'Messages'); ?></h3>
        <div class="box-tools">
        <?= $messages->links(); ?>
        </div>
    </div>
    <div class="box-body">
<?php
if (! $messages->isEmpty()) {
    $count = 0; $total = $messages->count();

    foreach($messages as $message) {
        $count++;

        // Calculate the number of unread replies on the current message.
        $unread = $message->replies->where('receiver_id', $authUser->id)->where('is_read', 0)->count();

        // If the parent message was not read yet by the receiver, count it too.
        if (($message->sender_id !== $authUser->id) && ($message->is_read === 0)) $unread++;
?>
        <!-- Statuses -->
        <div class="media" style="margin-top: 0;">
            <div class="pull-left">
                <img class="media-object img-responsive" style="height: 65px; width: 65px" alt="<?= $message->sender->present()->name(); ?>" src="<?= $message->sender->present()->picture(); ?>">
            </div>
            <div class="media-body">
                <div class="col-md-8 no-padding">
                    <h4 class="media-heading"><?= e($message->subject); ?> <?php if ($unread >  0) echo '<small class="label label-warning">' .$unread .'</small>'; ?></h4>
                    <p class="no-margin"><?= __d('messages', 'By <b>{0}</b>', $message->sender->present()->name()); ?></p>
                    <ul class="list-inline text-muted no-margin">
                        <li><?= __d('messages', '{0, plural, one{# reply} other{# replies}}', $message->replies->count()); ?></li>
                        <li><?= $message->created_at->diffForHumans(); ?></li>
                    </ul>
                </div>
                <div class="col-md-4 no-padding">
                    <a class="btn btn-sm btn-primary pull-right" title="<?= __d('messages', 'View this message and its replies'); ?>" href="<?= site_url('admin/messages/' .$message->id); ?>"><i class='fa fa-envelope'></i> <?= __d('messages', 'View the Message(s)'); ?></a>
                </div>
            </div>
        </div>
        <?php if ($count < $total) { ?>
        <hr style="margin-top: 10px; margin-bottom: 10px;">
        <?php } ?>
        <?php } ?>
        <?php } else { ?>
        <p><strong><?= __d('messages', 'You have no messages sent or received.'); ?></strong></p>
        <br>
        <?php } ?>
    </div>
    <div class="box-footer with-border">
        <a class='btn btn-success' href='<?= site_url('admin/messages/create'); ?>'><i class='fa fa-send'></i> <?= __d('messages', 'Send a new Message'); ?></a>
    </div>
</div>

</section>
