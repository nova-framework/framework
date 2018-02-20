<section class="content-header">
    <h1><?= $title; ?></h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('admin/dashboard'); ?>"><i class="fa fa-dashboard"></i> <?= __d('content', 'Dashboard'); ?></a></li>
        <li><?= __d('content', 'Content'); ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= View::fetch('Partials/Messages'); ?>

<div class="row">

<div class="col-md-4">

<form class="form-horizontal" action="<?= site_url('admin/taxonomies'); ?>" method='POST' role="form">

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('content', 'Create a new {0}', $name); ?></h3>
    </div>
    <div class="box-body">
        <div class="col-md-12">

        <div class="form-group">
            <label class="control-label" for="name"><?= __d('content', 'Name'); ?></label>
            <input name="name" id="name" type="text" class="form-control" value="<?= Input::old('name'); ?>" placeholder="<?= __d('content', 'Name'); ?>">
        </div>
        <div class="form-group">
            <label class="control-label" for="slug"><?= __d('content', 'Slug'); ?></label>
            <input name="slug" id="slug" type="text" class="form-control" value="<?= Input::old('slug'); ?>" placeholder="<?= __d('content', 'Slug'); ?>">
        </div>
        <?php if ($type == 'category') { ?>
        <div class="form-group">
            <label class="control-label" for="slug"><?= __d('content', 'Parent Category'); ?></label>
            <select name="parent" id="parent" class="form-control select2" placeholder="" data-placeholder="<?= __d('content', 'Select a Category'); ?>" style="width: 100%;" autocomplete="off">
                <option value="0"><?= __d('content', 'None'); ?></option>
                <?= $categories; ?>
            </select>
        </div>
        <?php } ?>
        <div class="form-group" style=" margin-bottom: 0;">
            <label class="control-label" for="description"><?= __d('content', 'Description'); ?></label>
            <textarea name="description" id="description" class="form-control" rows="8" style="resize: none;" placeholder="<?= __d('content', 'Description'); ?>"><?= Input::old('description'); ?></textarea>
        </div>

        </div>
    </div>
    <div class="box-footer">
        <input type="submit" name="submit" class="btn btn-success col-sm-6 pull-right" value="<?= __d('platform', 'Create a New {0}', $name); ?>">
    </div>
</div>

<input type="hidden" name="taxonomy" value="<?= ($type == 'tag') ? 'post_tag' : $type; ?>" />
<input type="hidden" name="_token"   value="<?= csrf_token(); ?>" />

</form>

</div>

<div class="col-md-8">

<div class="box box-default">
    <div class="box-header">
        <h3 class="box-title"><?= __d('content', 'The registered {0}', $title); ?></h3>
        <div class="box-tools">
        <?= $items->links(); ?>
        </div>
    </div>
    <div class="box-body no-padding">
        <?php $deletables = $editables = 0; ?>
        <?php if (! $items->isEmpty()) { ?>
        <table id="left" class="table table-striped table-hover responsive">
            <tr class="bg-navy disabled">
                <th style="text-align: left; vertical-align: middle;"><?= __d('content', 'Name'); ?></th>
                <th style="text-align: left; vertical-align: middle;"><?= __d('content', 'Slug'); ?></th>
                <th style="text-align: center; vertical-align: middle;"><?= __d('content', 'Count'); ?></th>
                <th style="text-align: right; vertical-align: middle;"><?= __d('content', 'Operations'); ?></th>
            </tr>
            <?php foreach ($items as $item) { ?>
            <?php $deletables++; ?>
            <?php $editables++; ?>
            <tr>
                <td style="text-align: left; vertical-align: middle;" title="<?= $item->description ?: __d('content', 'No description'); ?>" width="40%"><?= $item->name; ?></td>
                <td style="text-align: left; vertical-align: middle;" width="35%"><?= $item->slug; ?></td>
                <td style="text-align: center; vertical-align: middle;" width="5%"><?= $item->count; ?></td>
                <td style="text-align: right; vertical-align: middle;" width="20%">
                    <div class="btn-group" role="group" aria-label="...">
                        <a class="btn btn-sm btn-danger" href="#" data-toggle="modal" data-target="#modal-delete-dialog" data-id="<?= $item->id; ?>" title="<?= __d('content', 'Delete this {0}', $name); ?>" role="button"><i class="fa fa-remove"></i></a>
                        <a class="btn btn-sm btn-success" href="#" data-toggle="modal" data-target="#modal-edit-dialog" data-id="<?= $item->id; ?>" data-name="<?= $item->name; ?>" data-slug="<?= $item->slug; ?>" data-parent="<?= $item->parent_id; ?>" data-description="<?= $item->description; ?>" title="<?= __d('users', 'Edit this {0}', $name); ?>" role="button"><i class="fa fa-pencil"></i></a>
                        <a class="btn btn-sm btn-warning" href="<?= site_url('admin/content/' .$type .'/' .$item->slug); ?>" title="<?= __d('content', 'View the Posts on this {0}', $name); ?>" target="_blank" role="button"><i class="fa fa-search"></i></a>
                    </div>
                </td>
            </tr>
            <?php } ?>
        </table>
        <?php } else { ?>
        <div class="alert alert-warning" style="margin: 0 5px 5px;">
            <h4><i class="icon fa fa-warning"></i> <?= strftime("%d %b %Y, %R", time()) ." - "; ?> <?= __d('content', 'No registered Posts'); ?></h4>
            <?= __d('content', 'There are no registered Posts.'); ?>
        </div>
        <?php } ?>
    </div>
</div>

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

        $('#modal-delete-form').attr('action', '<?= site_url("admin/taxonomies"); ?>/' + id + '/destroy');
    });
});

</script>

<?php } ?>

<?php if ($editables > 0) { ?>

<div class="modal modal-default" id="modal-edit-dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="modal-edit-form" class="form-horizontal" action="<?= site_url('admin/taxonomies'); ?>" method='POST' role="form">

            <div class="modal-header" style="padding: 10px;">
                <button aria-label="<?= __d('records', 'Close'); ?>" data-dismiss="modal" class="close" type="button">
                <span aria-hidden="true">×</span></button>
                <h4 class="modal-edit-title" style="margin: 0;"><?= __d('records', 'Edit a Field'); ?></h4>
            </div>
            <div class="modal-body">
                <div class="col-md-12">

                <div class="form-group">
                    <label class="control-label" for="name"><?= __d('content', 'Name'); ?></label>
                    <input name="name" id="modal-edit-name" type="text" class="form-control" value="<?= Input::old('name'); ?>" placeholder="<?= __d('content', 'Name'); ?>">
                </div>
                <div class="form-group">
                    <label class="control-label" for="slug"><?= __d('content', 'Slug'); ?></label>
                    <input name="slug" id="modal-edit-slug" type="text" class="form-control" value="<?= Input::old('slug'); ?>" placeholder="<?= __d('content', 'Slug'); ?>">
                </div>
                <?php if ($type == 'category') { ?>
                <div class="form-group">
                    <label class="control-label" for="slug"><?= __d('content', 'Parent Category'); ?></label>
                    <select name="parent" id="modal-edit-parent" class="form-control select2" placeholder="" data-placeholder="<?= __d('content', 'Select a Category'); ?>" style="width: 100%;" autocomplete="off">
                        <option></option>
                    </select>
                </div>
                <?php } ?>
                <div class="form-group" style=" margin-bottom: 0;">
                    <label class="control-label" for="description"><?= __d('content', 'Description'); ?></label>
                    <textarea name="description" id="modal-edit-description" class="form-control" rows="8" style="resize: none;" placeholder="<?= __d('content', 'Description'); ?>"><?= Input::old('description'); ?></textarea>
                </div>

                </div>

                <div class="clearfix"></div>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="id" id="edit-record-id" value="0" />
                <input type="hidden" name="taxonomy" value="<?= ($type == 'tag') ? 'post_tag' : $type; ?>" />
                <input type="hidden" name="_token" value="<?= csrf_token(); ?>" />
                <button data-dismiss="modal" class="btn btn-primary pull-left col-md-3" type="button"><?= __d('content', 'Cancel'); ?></button>
                <input type="submit" name="button" class="btn btn btn-success pull-right col-md-3" value="<?= __d('content', 'Save'); ?>">
            </div>

            <?= csrf_field(); ?>

            </form>
        </div>
        <!-- /.modal-content -->
    </div>
</div>

<script>

$(function () {
    $('#modal-edit-dialog').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal

        var id          = button.data('id');
        var name        = button.data('name');
        var slug        = button.data('slug');
        var description = button.data('description');

<?php if ($type == 'category') { ?>

       // Do an AJAX request to retrieve the Courses on the current Category.
        var parent = button.data('parent');

        var parentSelect = $('#modal-edit-parent');

        var url = "<?= site_url('admin/taxonomies'); ?>/" + id + '/' + parent;

        $.ajax({
            url: url,
            dataType: 'html',
            success: function(data, textStatus, xhr) {
                parentSelect.html(data);

                parentSelect.trigger('change');
            },
            error: function (xhr, textStatus, errorThrown) {
                parentSelect.html('<option></option>');

                parentSelect.trigger('change');

                // For debugging.
                alert(xhr.status + ' ' + errorThrown);
            }
        });

<?php } ?>

        $('#modal-edit-name').val(name);
        $('#modal-edit-slug').val(slug);

        $('#modal-edit-description').val(description);

        // The title.
        var title = sprintf("<?= __d('content', 'Edit the {0} : <b>%s</b>', $name); ?>", name);

        $('.modal-edit-title').html(title);

        // The form action.
        $('#modal-edit-form').attr('action', '<?= site_url("admin/taxonomies"); ?>/' + id);
    });
});

</script>

<?php } ?>

