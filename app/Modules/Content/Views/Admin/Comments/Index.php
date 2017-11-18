<section class="content-header">
    <h1><?= $title; ?></h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('admin/dashboard'); ?>"><i class="fa fa-dashboard"></i> <?= __d('content', 'Dashboard'); ?></a></li>
        <li><?= __d('content', 'Comments'); ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= Session::getMessages(); ?>

<div class="box box-default">
    <div class="box-header">
        <h3 class="box-title"><?= __d('content', 'The submitted Comments'); ?></h3>
        <div class="box-tools">
        <?= $comments->links(); ?>
        </div>
    </div>
    <div class="box-body no-padding">
        <?php $deletables = 0; ?>
        <?php if (! $comments->isEmpty()) { ?>
        <table id="left" class="table table-striped table-hover responsive">
            <tr class="bg-navy disabled">
                <th style="text-align: left; vertical-align: middle;"><?= __d('content', 'Author'); ?></th>
                <th style="text-align: left; vertical-align: middle;"><?= __d('content', 'Comment'); ?></th>
                <th style="text-align: center; vertical-align: middle;"><?= __d('content', 'In Response To'); ?></th>
                <th style="text-align: center; vertical-align: middle;"><?= __d('content', 'Submitted On'); ?></th>
                <th style="text-align: right; vertical-align: middle;"><?= __d('content', 'Operations'); ?></th>
            </tr>
            <?php foreach ($comments as $comment) { ?>
            <tr>
                <td style="text-align: left; vertical-align: top;" width="20%">
                    <div style="padding-bottom: 5px;">
                        <a style="font-weight: bold;" href="<?= site_url('admin/comments/' .$comment->id .'/edit'); ?>"><?= $comment->author; ?></a>
                    </div>
                    <div style="padding-bottom: 5px;">
                        <a href="mailto:<?= $comment->author_email; ?>"><?= $comment->author_email; ?></a>
                    </div>
                    <div style="padding-bottom: 5px; font-weight: bold;"><?= $comment->author_ip; ?></div>
                </td>
                <td style="text-align: left; vertical-align: top;" width="35%"><?= nl2br($comment->content); ?></td>
                <td style="text-align: center; vertical-align: top; font-weight: bold;" width="20%">
                    <a target="_blank" href="<?= site_url('content/' .$comment->post->name); ?>" title="<?= __d('content', 'View the Post'); ?>"><?= $comment->post->title; ?></a>
                </td>
                <td style="text-align: center; vertical-align: top;" width="15%"><?= $comment->created_at->formatLocalized(__d('content', '%d %b %Y, %R')); ?></td>
                <td style="text-align: right; vertical-align: middle;" width="10%">
                    <?php if( $comment->approved == 1) { ?>
                    <form action="<?= site_url('admin/comments/' .$comment->id .'/approve'); ?>" method="POST">
                        <?= csrf_field(); ?>
                        <input type="submit" value="<?= __d('content', 'Unapprove'); ?>" class="btn btn-xs btn-block btn-primary" />
                    </form>
                    <?php } else { ?>
                    <form action="<?= site_url('admin/comments/' .$comment->id .'/unapprove'); ?>" method="POST">
                        <?= csrf_field(); ?>
                        <input type="submit" value="<?= __d('content', 'Approve'); ?>" class="btn btn-xs btn-block btn-primary" />
                    </form>
                    <?php } ?>
                    <a class="btn btn-xs btn-success btn-block" style="min-width: 80%; margin-top: 5px; margin-bottom: 5px;" href="#" data-toggle="modal" data-target="#modal-edit-dialog" data-id="<?= $comment->id; ?>" title="<?= __d('content', 'Edit this Comment'); ?>" role="button"><?= __d('content', 'Edit'); ?></a>
                    <form action="<?= site_url('admin/comments/' .$comment->id .'/destroy'); ?>" method="POST">
                        <?= csrf_field(); ?>
                        <input type="submit" value="<?= __d('content', 'Delete'); ?>" class="btn btn-xs btn-block btn-danger" />
                    </form>
                </td>
            </tr>
            <?php } ?>
        </table>
        <?php } else { ?>
        <div class="alert alert-warning" style="margin: 0 5px 5px;">
            <h4><i class="icon fa fa-warning"></i> <?= strftime("%d %b %Y, %R", time()) ." - "; ?> <?= __d('content', 'No registered Comments'); ?></h4>
            <?= __d('content', 'There are no registered Comments.'); ?>
        </div>
        <?php } ?>
    </div>
</div>

</section>
