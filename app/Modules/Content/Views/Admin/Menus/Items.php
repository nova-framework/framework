<link rel="stylesheet" type="text/css" href="<?= resource_url('css/jquery.nestable.css', 'Content'); ?>">

<section class="content-header">
    <h1><?= __d('content', 'Manage the Menu: {0}', $menu->name); ?></h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('admin/dashboard'); ?>"><i class="fa fa-dashboard"></i> <?= __d('content', 'Dashboard'); ?></a></li>
        <li><?= __d('content', 'Manage a Menu'); ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= Session::getMessages(); ?>

<div class="row">

<div class="col-md-4">

<form id="page-form" action="<?= site_url('admin/menus/' .$menu->id .'/post'); ?>" method='POST' role="form">

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('content', 'Pages'); ?></h3>
    </div>
    <div class="box-body" style="min-height: 150px; max-height: 270px; padding-bottom: 20px;">
    <?= $pages; ?>
    </div>
    <div class="box-footer">
        <input type="submit" name="submit" class="btn btn-primary col-sm-5 pull-right" value="<?= __d('users', 'Add to Menu'); ?>" />
    </div>
</div>

<input type="hidden" name="_token"   value="<?= csrf_token(); ?>" />
<input type="hidden" name="type" value="page" />

</form>

<form id="page-form" action="<?= site_url('admin/menus/' .$menu->id .'/post'); ?>" method='POST' role="form">

<div class="box box-widget">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('content', 'Posts'); ?></h3>
    </div>
    <div class="box-body" style="min-height: 150px; max-height: 270px; padding-bottom: 20px;">
    <?= $posts; ?>
    </div>
    <div class="box-footer">
        <input type="submit" name="submit" class="btn btn-primary col-sm-5 pull-right" value="<?= __d('users', 'Add to Menu'); ?>" />
    </div>
</div>

<input type="hidden" name="_token"   value="<?= csrf_token(); ?>" />
<input type="hidden" name="type" value="post" />

</form>

<form id="page-form" action="<?= site_url('admin/menus/' .$menu->id .'/category'); ?>" method='POST' role="form">

<div class="box box-widget">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('content', 'Categories'); ?></h3>
    </div>
    <div class="box-body" style="min-height: 150px; max-height: 270px; padding-bottom: 20px;">
    <?= $categories; ?>
    </div>
    <div class="box-footer">
        <input type="submit" name="submit" class="btn btn-primary col-sm-5 pull-right" value="<?= __d('users', 'Add to Menu'); ?>" />
    </div>
</div>

<input type="hidden" name="_token"   value="<?= csrf_token(); ?>" />

</form>

<form id="page-form" action="<?= site_url('admin/menus/' .$menu->id .'/custom'); ?>" method='POST' role="form">

<div class="box box-widget">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('content', 'Custom Links'); ?></h3>
    </div>
    <div class="box-body">
        <div class="form-group">
            <label class="control-label" for="name"><?= __d('content', 'Name'); ?></label>
            <input name="name" id="name" type="text" class="form-control" value="<?= Input::old('name'); ?>" placeholder="<?= __d('content', 'Name'); ?>">
        </div>
        <div class="form-group">
            <label class="control-label" for="link"><?= __d('content', 'URL'); ?></label>
            <input name="link" id="link" type="text" class="form-control" value="<?= Input::old('name'); ?>" placeholder="<?= __d('content', 'URL'); ?>">
        </div>
    </div>
    <div class="box-footer">
        <input type="submit" name="submit" class="btn btn-primary col-sm-5 pull-right" value="<?= __d('users', 'Add to Menu'); ?>" />
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
            <?= View::fetch('Partials/MenuItemsNestable', array('menu' => $menu, 'items' => $items), 'Content'); ?>
        </div>
    </div>
    <div class="box-footer">
        <input type="submit" name="submit" class="btn btn-success col-sm-3 pull-right" value="<?= __d('users', 'Save'); ?>" />
    </div>
</div>

<input type="hidden" name="_token"   value="<?= csrf_token(); ?>" />

</form>

<script type="text/javascript" src="<?= resource_url('js/jquery.nestable.js', 'Content'); ?>"></script>

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
