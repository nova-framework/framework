<section class="content-header">
    <h1><?= $title; ?></h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('admin/dashboard'); ?>"><i class="fa fa-dashboard"></i> <?= __d('content', 'Dashboard'); ?></a></li>
        <li><?= __d('content', 'Comments'); ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= View::fetch('Partials/Messages'); ?>

<div class="box box-default">
    <div class="box-header">
        <h3 class="box-title"><?= __d('content', 'Submitted Comments'); ?></h3>
        <div class="box-tools">
        <?= $comments->links(); ?>
        </div>
    </div>
    <div class="box-body no-padding">
        <?php $deletables = $editables = 0; ?>
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
            <?php $editables++; ?>
            <?php $deletables++; ?>
            <tr>
                <td style="text-align: left; vertical-align: top;" width="20%">
                    <div style="padding-bottom: 5px;">
                        <a style="font-weight: bold;" href="<?= site_url('admin/comments/' .$comment->id .'/edit'); ?>"><?= e($comment->author); ?></a>
                    </div>
                    <div style="padding-bottom: 5px;">
                        <a href="mailto:<?= $comment->author_email; ?>"><?= e($comment->author_email); ?></a>
                    </div>
                    <div style="padding-bottom: 5px; font-weight: bold;"><?= $comment->author_ip; ?></div>
                </td>
                <td style="text-align: left; vertical-align: top;" width="35%"><?= nl2br(e($comment->content)); ?></td>
                <td style="text-align: center; vertical-align: top; font-weight: bold;" width="20%">
                    <a target="_blank" href="<?= site_url('content/' .$comment->post->name); ?>" title="<?= __d('content', 'View the Post'); ?>"><?= $comment->post->title; ?></a>
                </td>
                <td style="text-align: center; vertical-align: top;" width="15%"><?= $comment->created_at->formatLocalized(__d('content', '%d %b %Y, %R')); ?></td>
                <td style="text-align: right; vertical-align: middle; padding-bottom: 30px;" width="10%">
                    <?php if( $comment->approved == 1) { ?>
                    <form action="<?= site_url('admin/comments/' .$comment->id .'/unapprove'); ?>" method="POST">
                        <?= csrf_field(); ?>
                        <input type="submit" value="<?= __d('content', 'Unapprove'); ?>" class="btn btn-xs btn-block btn-warning" />
                    </form>
                    <?php } else { ?>
                    <form action="<?= site_url('admin/comments/' .$comment->id .'/approve'); ?>" method="POST">
                        <?= csrf_field(); ?>
                        <input type="submit" value="<?= __d('content', 'Approve'); ?>" class="btn btn-xs btn-block btn-success" />
                    </form>
                    <?php } ?>
                    <a class="btn btn-xs btn-primary btn-block" style="min-width: 80%; margin-top: 5px; margin-bottom: 5px;" href="#" data-toggle="modal" data-target="#modal-edit-dialog" data-id="<?= $comment->id; ?>" title="<?= __d('content', 'Edit this Comment'); ?>" role="button"><?= __d('content', 'Edit'); ?></a>
                    <a class="btn btn-xs btn-danger btn-block" style="min-width: 80%; margin-top: 5px; margin-bottom: 5px;" href="#" data-toggle="modal" data-target="#modal-delete-dialog" data-id="<?= $comment->id; ?>" title="<?= __d('content', 'Delete this Comment'); ?>" role="button"><?= __d('content', 'Delete'); ?></a>
                </td>
            </tr>
            <?php } ?>
        </table>
        <?php } else { ?>
        <div class="alert alert-warning" style="margin: 0 5px 5px;">
            <h4><i class="icon fa fa-warning"></i> <?= strftime("%d %b %Y, %R", time()) ." - "; ?> <?= __d('content', 'No Comments'); ?></h4>
            <?= __d('content', 'No comment has been added yet!'); ?>
        </div>
        <?php } ?>
    </div>
</div>

</section>

<?php if ($editables > 0) { ?>

<div id="modal-edit-dialog" class="modal modal-default fade" tabindex="-1" role="dialog" aria-labelledby="...">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="modal-edit-form" class="form-horizontal" action="" method='POST' role="form">

            <div class="modal-header">
                <button aria-label="Close" data-dismiss="modal" class="close" type="button">
                <span aria-hidden="true">×</span></button>
                <h4 class="modal-edit-title"><?= __d('content', 'Edit a Comment'); ?></h4>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="control-label" for="author"><?= __d('content', 'Author'); ?></label>
                        <input name="author" id="modal-edit-author" type="text" class="form-control" value="" placeholder="<?= __d('content', 'Author'); ?>">
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="author_email"><?= __d('content', 'Author E-mail'); ?></label>
                        <input name="author_email" id="modal-edit-author-email" type="text" class="form-control" value="" placeholder="<?= __d('content', 'Author E-mail'); ?>">
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="author_url"><?= __d('content', 'Author URL'); ?></label>
                        <input name="author_url" id="modal-edit-author-url" type="text" class="form-control" value="" placeholder="<?= __d('content', 'Author URL'); ?>">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="control-label" for="content"><?= __d('content', 'Message'); ?></label>
                        <textarea name="content" id="modal-edit-content" class="form-control" style="resize: none;" rows="10" value="" placeholder="<?= __d('content', 'Message'); ?>"></textarea>
                    </div>
                </div>

                <div class="clearfix"></div>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" aria-hidden="true" class="btn btn-primary col-md-3"><?= __d('content', 'Cancel'); ?></button>
                <input type="submit" name="button" class="update-item-button btn btn-success col-md-3 pull-right" value="<?= __d('content', 'Save'); ?>" />
            </div>

            <input type="hidden" name="_token" value="<?= csrf_token(); ?>" />

            </form>
        </div>
    </div>
</div>

<script>

$(function () {
    function setupModalEditComment(id, data) {
        console.log(data);

        $('#modal-edit-author').val(data.author);

        $('#modal-edit-author-email').val(data.author_email);

        $('#modal-edit-author-url').val(data.author_url);

        $('#modal-edit-content').val(data.content);

        // The title.
        var title = sprintf("<?= __d('content', 'Edit the Comment <b>#%s</b>'); ?>", id);

        $('#modal-edit-title').html(title);

        // The form action.
        $('#modal-edit-form').attr('action', '<?= site_url("admin/comments/"); ?>/' + id);
    }

    $('#modal-edit-dialog').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal

        var id = button.data('id');

        $.ajax({
            url: "<?= site_url('admin/comments'); ?>/" + id,
            type: 'GET',
            data: {
                id: id
            },
            success: function(response) {
                setupModalEditComment(id, response);
            },
            error: function () {
                console.log("Error on loading the Comment information");
            }
        });
    });
});

</script>

<?php } ?>

<?php if ($deletables > 0) { ?>

<div id="modal-delete-dialog" class="modal modal-default fade" tabindex="-1" role="dialog" aria-labelledby="...">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-label="Close" data-dismiss="modal" class="close" type="button">
                <span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><?= __d('content', 'Delete this Comment?'); ?></h4>
            </div>
            <div class="modal-body">
                <p><?= __d('content', 'Are you sure you want to remove this Comment, the operation being irreversible?'); ?></p>
                <p><?= __d('content', 'Please click the button <b>Delete</b> to proceed, or <b>Cancel</b> to abandon the operation.'); ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" aria-hidden="true" class="btn btn-primary col-md-3"><?= __d('content', 'Cancel'); ?></button>
                <form id="modal-delete-form" action="" method="POST">
                    <input type="hidden" name="_token" value="<?= csrf_token(); ?>" />
                    <button type="submit" name="button" class="btn btn-danger col-md-3 pull-right"><?= __d('content', 'Delete'); ?></button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>

$(function() {
    // The Modal Delete dialog.
    $('#modal-delete-dialog').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal

        var id = button.data('id');

        $('#modal-delete-form').attr('action', '<?= site_url("admin/comments/"); ?>/' + id + '/destroy');
    });
});

</script>

<?php } ?>


