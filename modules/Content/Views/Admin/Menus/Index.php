<section class="content-header">
    <h1><?= $title; ?></h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('admin/dashboard'); ?>"><i class="fa fa-dashboard"></i> <?= __d('content', 'Dashboard'); ?></a></li>
        <li><?= __d('content', 'Menus'); ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= View::fetch('Partials/Messages'); ?>

<div class="row">

<div class="col-md-3">

<form id="page-form" action="<?= site_url('admin/menus'); ?>" method='POST' role="form">

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('content', 'Create a new Menu'); ?></h3>
    </div>
    <div class="box-body">
        <div class="form-group">
            <label class="control-label" for="name"><?= __d('content', 'Name'); ?></label>
            <input name="name" id="name" type="text" class="form-control" value="<?= Input::old('name'); ?>" placeholder="<?= __d('content', 'Name'); ?>">
        </div>
        <div class="form-group" style=" margin-bottom: 0;">
            <label class="control-label" for="description"><?= __d('content', 'Description'); ?></label>
            <textarea name="description" id="description" class="form-control" rows="8" style="resize: none;" placeholder="<?= __d('content', 'Description'); ?>"><?= Input::old('description'); ?></textarea>
        </div>
    </div>
    <div class="box-footer">
        <input type="submit" name="submit"  class="btn btn-success col-sm-6 pull-right" value="<?= __d('content', 'Add new Menu'); ?>" />
    </div>
</div>

<input type="hidden" name="_token"   value="<?= csrf_token(); ?>" />

</form>

</div>

<div class="col-md-9">

<div class="box box-default">
    <div class="box-header">
        <h3 class="box-title"><?= __d('content', 'The registered {0}', $title); ?></h3>
        <div class="box-tools">
        <?= $menus->links(); ?>
        </div>
    </div>
    <div class="box-body no-padding">
        <?php $deletables = 0; ?>
        <?php if (! $menus->isEmpty()) { ?>
        <table id="left" class="table table-striped table-hover responsive">
            <tr class="bg-navy disabled">
                <th style="text-align: left; vertical-align: middle;"><?= __d('content', 'Name'); ?></th>
                <th style="text-align: left; vertical-align: middle;"><?= __d('content', 'Description'); ?></th>
                <th style="text-align: center; vertical-align: middle;"><?= __d('content', 'Count'); ?></th>
                <th style="text-align: right; vertical-align: middle;"><?= __d('content', 'Operations'); ?></th>
            </tr>
            <?php foreach ($menus as $menu) { ?>
            <tr>
                <td style="text-align: left; vertical-align: middle;" width="35%"><?= $menu->name; ?></td>
                <td style="text-align: left; vertical-align: middle;" width="40%"><?= $menu->description ?: __d('content', 'No description'); ?></td>
                <td style="text-align: center; vertical-align: middle;" width="5%"><?= $menu->count; ?></td>
                <td style="text-align: right; vertical-align: middle;" width="20%">
                    <div class="btn-group" role="group" aria-label="...">
                        <a class="btn btn-sm btn-danger" href="#" data-toggle="modal" data-target="#modal-delete-dialog" data-id="<?= $menu->id; ?>" title="<?= __d('content', 'Delete this Menu'); ?>" role="button"><i class="fa fa-remove"></i></a>
                        <a class="btn btn-sm btn-success" href="#" data-toggle="modal" data-target="#modal-edit-dialog" data-id="<?= $menu->id; ?>" data-name="<?= $menu->name; ?>" data-description="<?= $menu->description; ?>" title="<?= __d('content', 'Edit this Menu'); ?>" role="button"><i class="fa fa-pencil"></i></a>
                        <a class="btn btn-sm btn-warning" href="<?= site_url('admin/menus/' .$menu->id); ?>" title="<?= __d('content', 'Manage the Items on this Menu'); ?>" role="button"><i class="fa fa-search"></i></a>
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

<div id="modal-edit-dialog" class="modal modal-default fade" tabindex="-1" role="dialog" aria-labelledby="...">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="modal-edit-form" class="form-horizontal" action="" method='POST' role="form">

            <div class="modal-header">
                <button aria-label="Close" data-dismiss="modal" class="close" type="button">
                <span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><?= __d('content', 'Edit a Menu Item'); ?></h4>
            </div>
            <div class="modal-body">
                <div class="col-md-12">

                <div class="form-group">
                    <label class="control-label" for="name"><?= __d('content', 'Name'); ?></label>
                    <input name="name" id="modal-edit-name" type="text" class="form-control" value="" placeholder="<?= __d('content', 'Name'); ?>">
                </div>
                <div class="form-group" style=" margin-bottom: 0;">
                    <label class="control-label" for="description"><?= __d('content', 'Description'); ?></label>
                    <textarea name="description" id="modal-edit-description" class="form-control" rows="8" style="resize: none;" placeholder="<?= __d('content', 'Description'); ?>"></textarea>
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
    $('#modal-edit-dialog').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal

        var id   = button.data('id');
        var name = button.data('name');
        var text = button.data('description');

        $('#modal-edit-name').val(name);
        $('#modal-edit-description').val(text);

        // The title.
        var title = sprintf("<?= __d('content', 'Edit the Menu Item : <b>%s</b>'); ?>", name);

        $('.modal-edit-title').html(title);

        // The form action.
        $('#modal-edit-form').attr('action', '<?= site_url("admin/menus"); ?>/' + id);
    });
});

</script>

<div id="modal-delete-dialog" class="modal modal-default fade" tabindex="-1" role="dialog" aria-labelledby="...">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-label="Close" data-dismiss="modal" class="close" type="button">
                <span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><?= __d('content', 'Delete this Menu?'); ?></h4>
            </div>
            <div class="modal-body">
                <p><?= __d('content', 'Are you sure you want to remove this Menu, the operation being irreversible?'); ?></p>
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

        $('#modal-delete-form').attr('action', '<?= site_url("admin/menus/"); ?>/' + id + '/destroy');
    });
});

</script>
