<link rel="stylesheet" type="text/css" href="<?= asset_url('css/jquery.nestable.css', 'modules/content'); ?>">

<section class="content-header">
    <h1><?= __d('content', 'Manage the Menu: {0}', $menu->name); ?></h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('admin/dashboard'); ?>"><i class="fa fa-dashboard"></i> <?= __d('content', 'Dashboard'); ?></a></li>
        <li><?= __d('content', 'Manage a Menu'); ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= View::fetch('Partials/Messages'); ?>

<div class="row">

<div class="col-md-4">

<?= implode("\n", $posts); ?>
<?= implode("\n", $taxonomies); ?>

<form id="page-form" action="<?= site_url('admin/menus/{0}/custom', $menu->id); ?>" method='POST' role="form">

<div class="box box-widget">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('content', 'Custom Links'); ?></h3>
    </div>
    <div class="box-body">
        <div class="form-group">
            <label class="control-label" for="name"><?= __d('content', 'Name'); ?></label>
            <input name="name" id="custom-name" type="text" class="form-control" value="<?= Input::old('name'); ?>" placeholder="<?= __d('content', 'Name'); ?>">
        </div>
        <div class="form-group">
            <label class="control-label" for="link"><?= __d('content', 'URL'); ?></label>
            <input name="link" id="custom-link" type="text" class="form-control" value="<?= Input::old('link'); ?>" placeholder="<?= __d('content', 'URL'); ?>">
        </div>
            <div class="form-group">
                <div class="col-md-1" style="padding: 0;">
                    <input type="checkbox" name="local" id="custom-local" value="1" <?= (1 == Input::old('local')) ? 'checked="checked"' : ''; ?> />
                </div>
                <div class="col-md-11" style="padding: 2px 10px;">
                    <label class="control-label" for="custom-local" style="margin-right: 10px;"><?= __d('content', 'Use a local URI'); ?></label>
                </div>
            </div>
    </div>
    <div class="box-footer">
        <input type="submit" name="submit" class="btn btn-primary col-sm-5 pull-right" value="<?= __d('content', 'Add to Menu'); ?>" />
    </div>
</div>

<input type="hidden" name="_token"   value="<?= csrf_token(); ?>" />

</form>

</div>

<div class="col-md-8">

<form id="menu-items-form" action="<?= site_url('admin/menus/' .$menu->id .'/items'); ?>" method='POST' role="form">

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('content', 'Menu Items in the {0}', $menu->name); ?></h3>
    </div>
    <div class="box-body" style="min-height: 550px;">
        <div class="dd">
            <?php $items = $menu->items->where('parent_id', 0); ?>
            <?= View::fetch('Modules/Content::Partials/MenuItemsNestable', array('menu' => $menu, 'items' => $items)); ?>
        </div>
    </div>
    <div class="box-footer">
        <input type="submit" name="submit" class="btn btn-success col-sm-3 pull-right" value="<?= __d('content', 'Save the Items'); ?>" />
    </div>
</div>

<input type="hidden" name="_token"   value="<?= csrf_token(); ?>" />

</form>

<script type="text/javascript" src="<?= asset_url('js/jquery.nestable.js', 'modules/content'); ?>"></script>

<script>

$(function() {
    $('.dd').nestable({
        listNodeName: 'ol',
        expandBtnHTML: '',
        collapseBtnHTML: '',

        //
        maxDepth: 7,
    });

    $('#menu-items-form').submit(function(event) {
        $(this).find('.items-form-value').remove();

        var data = JSON.stringify($('.dd').nestable('serialize'));

        $(this).append('<input class="items-form-value" type="hidden" name="items" value=\'' + data + '\'>');
    });
});

</script>

</div>

</div>

<div class="clearfix"></div>
<br>
<br>
<a class="btn btn-primary col-sm-2" href="<?= site_url('admin/menus'); ?>"><?= __d('content', '<< Previous Page'); ?></a>

<div class="clearfix"></div>

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

        var id          = button.data('id');
        var name        = button.data('name');

        $('#modal-edit-name').val(name);

        // The title.
        var title = sprintf("<?= __d('content', 'Edit the Menu Item : <b>%s</b>'); ?>", name);

        $('.modal-edit-title').html(title);

        // The form action.
        $('#modal-edit-form').attr('action', '<?= site_url("admin/menus/" .$menu->id ."/items"); ?>/' + id);
    });
});

</script>

<div id="modal-delete-dialog" class="modal modal-default fade" tabindex="-1" role="dialog" aria-labelledby="...">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-label="Close" data-dismiss="modal" class="close" type="button">
                <span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><?= __d('content', 'Delete this Item?'); ?></h4>
            </div>
            <div class="modal-body">
                <p><?= __d('content', 'Are you sure you want to remove this Item, the operation being irreversible?'); ?></p>
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

        $('#modal-delete-form').attr('action', '<?= site_url("admin/menus/" .$menu->id ."/items"); ?>/' + id + '/destroy');
    });
});

</script>
