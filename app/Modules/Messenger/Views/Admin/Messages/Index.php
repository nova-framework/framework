<section class="content-header">
    <h1><?= __d('messenger', 'Messages'); ?></h1>
    <ol class="breadcrumb">
        <li><a href='<?= site_url('admin/dashboard'); ?>'><i class="fa fa-dashboard"></i> <?= __d('users', 'Dashboard'); ?></a></li>
        <li><?= __d('messenger', 'Messages'); ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= Session::getMessages(); ?>

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('messenger', 'Create a new Message'); ?></h3>
    </div>
    <div class="box-body">
        <a class='btn btn-success' href='<?= site_url('admin/messages/create'); ?>'><?= __d('messenger', 'Create a new Message'); ?></a>
    </div>
</div>

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('messenger', 'Message Threads'); ?></h3>
        <div class="box-tools">
        <?= $threads->links(); ?>
        </div>
    </div>
    <div class="box-body">
<?php if (! $threads->isEmpty()) { ?>
    <?php foreach ($threads->getItems() as $thread) { ?>
        <?php $class = $thread->isUnread($userId) ? 'info' : 'default'; ?>
        <div class="callout callout-<?= $class; ?>">
            <h4><strong><a href="<?= site_url('admin/messages/' . $thread->id); ?>" style="text-decoration: none;"><?= e($thread->subject); ?></a></strong></h4>
            <p><?= e($thread->latestMessage->body); ?></p>
            <hr style="margin-bottom: 10px;">
            <p class="last-child <?= ($class == 'default') ? 'text-muted' : ''; ?>">
                <small><strong>Creator:</strong> <?= $thread->creator()->username; ?></small> |
                <small><strong>Participants:</strong> <?= $thread->participantsString($userId); ?></small>
            </p>
        </div>
    <?php } ?>
<?php } else { ?>
        <div class="alert alert-warning" style="margin: 0 5px 5px;">
            <h4><i class="icon fa fa-warning"></i> <?php echo strftime("%d %b %Y, %R", time()) ." - "; ?> <?= __d('messenger', 'Sorry, no threads.'); ?></h4>
            <?= __d('users', 'There are no Message Threads.'); ?>
        </div>
<?php } ?>
    </div>
</div>

</section>
