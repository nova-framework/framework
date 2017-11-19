<section class="content-header">
    <h1><?= $title; ?></h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('admin/dashboard'); ?>"><i class="fa fa-dashboard"></i> <?= __d('content', 'Dashboard'); ?></a></li>
        <li><?= __d('content', 'Content'); ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= Session::getMessages(); ?>

<?php if (! isset($simple)) { ?>

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= $postType->label('addNewItem'); ?></h3>
    </div>
    <div class="box-body">
        <a class="btn btn-success col-sm-2 pull-right" href="<?= site_url('admin/content/create/' .$type); ?>"><?= $postType->label('addNewItem'); ?></a>
    </div>
</div>

<?php } ?>

<div class="box box-default">
    <div class="box-header">
        <h3 class="box-title"><?= $postType->label('allItems'); ?></h3>
        <div class="box-tools">
        <?= $posts->links(); ?>
        </div>
    </div>
    <div class="box-body no-padding">
        <?php $deletables = 0; ?>
        <?php if (! $posts->isEmpty()) { ?>
        <table id="left" class="table table-striped table-hover responsive">
            <tr class="bg-navy disabled">
                <th style="text-align: center; vertical-align: middle;"><?= __d('content', 'ID'); ?></th>
                <th style="text-align: left; vertical-align: middle;"><?= __d('content', 'Title'); ?></th>
                <th style="text-align: center; vertical-align: middle;"><?= __d('content', 'Author'); ?></th>
                <th style="text-align: center; vertical-align: middle;"><?= __d('content', 'Status'); ?></th>
                <th style="text-align: center; vertical-align: middle;"><?= __d('content', 'Updated At'); ?></th>
                <th style="text-align: right; vertical-align: middle;"><?= __d('content', 'Operations'); ?></th>
            </tr>
            <?php foreach ($posts as $post) { ?>
            <?php $deletables++; ?>
            <tr>
                <td style="text-align: center; vertical-align: middle;" width="5%"><?= $post->id; ?></td>
                <td style="text-align: left; vertical-align: middle;" width="41%" title="<?= $post->slug; ?>"><?= ! empty($post->title) ? $post->title : __d('content', 'Untitled'); ?></td>
                <td style="text-align: center; vertical-align: middle;" width="15%"><?= $post->author->username; ?></td>
                <td style="text-align: center; vertical-align: middle;" width="12%" title="<?= $post->status; ?>"><?= Arr::get($statuses, $post->status, __d('content', 'Unknown ({0})', $post->status)); ?></td>
                <td style="text-align: center; vertical-align: middle;" width="12%"><?= $post->updated_at->formatLocalized(__d('content', '%d %b %Y, %R')); ?></td>
                <td style="text-align: right; vertical-align: middle;" width="15%">
                    <div class="btn-group" role="group" aria-label="...">
                        <a class="btn btn-sm btn-danger" href="#" data-toggle="modal" data-target="#modal-delete-dialog" data-id="<?= $post->id; ?>" title="<?= $postType->label('deleteItem'); ?>" role="button"><i class="fa fa-remove"></i></a>
                        <a class="btn btn-sm btn-success" href="<?= site_url('admin/content/' .$post->id .'/edit'); ?>" title="<?= $postType->label('editItem'); ?>" role="button"><i class="fa fa-pencil"></i></a>
                        <a class="btn btn-sm btn-warning" href="<?= site_url('content/' .$post->slug); ?>" title="<?= $postType->label('viewItem'); ?>" target="_blank" role="button"><i class="fa fa-search"></i></a>
                    </div>
                </td>
            </tr>
            <?php } ?>
        </table>
        <?php } else { ?>
        <div class="alert alert-warning" style="margin: 0 5px 5px;">
            <h4><i class="icon fa fa-warning"></i> <?= strftime("%d %b %Y, %R", time()) ." - "; ?> <?= __d('content', 'No registered {0}', $title); ?></h4>
            <?= __d('content', 'There are no registered {0}.', $title); ?>
        </div>
        <?php } ?>
    </div>
</div>

</section>

<?php if ($deletables > 0) { ?>

<div class="modal modal-default" id="modal-delete-dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-label="<?= __d('content', 'Close'); ?>" data-dismiss="modal" class="close" type="button">
                <span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><?= __d('content', 'Delete this {0}?', $name); ?></h4>
            </div>
            <div class="modal-body">
                <p><?= __d('content', 'Are you sure you want to remove this {0}, the operation being irreversible?', $name); ?></p>
                <p><?= __d('content', 'Please click the button <b>Delete</b> to proceed, or <b>Cancel</b> to abandon the operation.'); ?></p>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-primary pull-left col-md-3" type="button"><?= __d('content', 'Cancel'); ?></button>
                <form id="modal-delete-form" action="" method="POST">
                    <input type="hidden" name="id" id="delete-record-id" value="0" />
                    <input type="hidden" name="_token" value="<?= csrf_token(); ?>" />
                    <input type="submit" name="button" class="btn btn btn-danger pull-right col-md-3" value="<?= __d('content', 'Delete'); ?>">
                </form>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
</div>

<script>

$(function () {
    $('#modal-delete-dialog').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal

        var id = button.data('id');

        //
        $('#delete-record-id').val(id);

        $('#modal-delete-form').attr('action', '<?= site_url("admin/content"); ?>/' + id + '/destroy');
    });
});

</script>

<?php } ?>
