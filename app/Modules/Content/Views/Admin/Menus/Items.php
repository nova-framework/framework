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

<form id="page-form" action="<?= site_url('admin/menus/' .$menu->id); ?>" method='POST' role="form">

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('content', 'Add a Page to Menu'); ?></h3>
    </div>
    <div class="box-body" style="height: 270px;">
    <?= $pages; ?>
    </div>
    <div class="box-footer">
        <a class="btn btn-primary col-sm-6 pull-right" href="<?= site_url('admin/menus/' .$menu->id .'/page'); ?>"><?= __d('users', 'Add to Menu'); ?></a>
    </div>
</div>

<input type="hidden" name="menuId" value="<?= $menu->id; ?>" />
<input type="hidden" name="type" value="page" />

</form>

<form id="page-form" action="<?= site_url('admin/content/' .$menu->id); ?>" method='POST' role="form">

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('content', 'Add a Post to Menu'); ?></h3>
    </div>
    <div class="box-body" style="height: 270px;">
    <?= $posts; ?>
    </div>
    <div class="box-footer">
        <a class="btn btn-primary col-sm-6 pull-right" href="<?= site_url('admin/menus/' .$menu->id .'/post'); ?>"><?= __d('users', 'Add to Menu'); ?></a>
    </div>
</div>

<input type="hidden" name="menuId" value="<?= $menu->id; ?>" />
<input type="hidden" name="type" value="post" />

</form>

<form id="page-form" action="<?= site_url('admin/content/' .$menu->id); ?>" method='POST' role="form">

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('content', 'Add a Category to Menu'); ?></h3>
    </div>
    <div class="box-body" style="height: 270px;">
    <?= $categories; ?>
    </div>
    <div class="box-footer">
        <a class="btn btn-primary col-sm-6 pull-right" href="<?= site_url('admin/menus/' .$menu->id .'/post'); ?>"><?= __d('users', 'Add to Menu'); ?></a>
    </div>
</div>

<input type="hidden" name="menuId" value="<?= $menu->id; ?>" />
<input type="hidden" name="type" value="category" />

</form>

<form id="page-form" action="<?= site_url('admin/content/' .$menu->id); ?>" method='POST' role="form">

<div class="box box-widget">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('content', 'Add a Custom Link to Menu'); ?></h3>
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
        <a class="btn btn-primary col-sm-6 pull-right" href="<?= site_url('admin/menus/' .$menu->id .'/customLink'); ?>"><?= __d('users', 'Add to Menu'); ?></a>
    </div>
</div>

<input type="hidden" name="menuId" value="<?= $menu->id; ?>" />
<input type="hidden" name="type" value="custom" />

</form>

</div>

<div class="col-md-8">

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('content', 'Menu Items'); ?></h3>
    </div>
    <div class="box-body" style="min-height: 550px;">
        <div class="dd">
            <?= View::fetch('Partials/MenuItemsNestable', array('menu' => $menu, 'items' => $menu->items), 'Content'); ?>
        </div>
    </div>
</div>

</div>

</div>

</section>

<div id="modal-edit-dialog" class="modal modal-default fade" tabindex="-1" role="dialog" aria-labelledby="...">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-label="Close" data-dismiss="modal" class="close" type="button">
                <span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><?= __d('content', 'Edit a Menu Item'); ?></h4>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" aria-hidden="true" class="btn btn-primary col-md-3"><?= __d('content', 'Cancel'); ?></button>
                <button type="button" class="update-item-button btn btn-success col-md-3 pull-right"><?= __d('content', 'Save'); ?></button>
            </div>
        </div>
    </div>
</div>

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
                    <input type="hidden" name="formId" id="delete-item-form-id" value=""/>
                    <input type="hidden" name="_token" value="<?= csrf_token(); ?>" />
                    <button type="submit" name="button" class="btn btn-danger col-md-3 pull-right"><?= __d('content', 'Delete'); ?></button>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="<?= resource_url('js/jquery.nestable.js', 'Content'); ?>"></script>

<script>

$(function() {
    $('.dd').nestable({
        listNodeName: 'ol',
        expandBtnHTML: '',
        collapseBtnHTML: '',
        maxDepth: 7,
    });

    $('.dd').on('change', function() {
        var url = '<?= site_url("admin/menus/" .$menu->id ."/items/order") ?>';

       var json = JSON.stringify($(this).nestable('serialize'));

       $.ajax({
            url: url,
            type: 'POST',
            data: {
                json
            },
            headers: {
                'X-CSRF-Token': '<?= csrf_token(); ?>',
            },
            dataType: 'json'
        });
    });

    $('.dd-handle input').on('mousedown', function(e) {
        e.stopPropagation();
    });

    // The Modal Delete dialog.
    $('#modal-delete-dialog').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal

        var id = button.data('id');

        //
        $('#delete-record-id').val(id);

        $('#modal-delete-form').attr('action', '<?= site_url("admin/menus/" .$menu->id ."/items"); ?>/' + id + '/destroy');
    });
});

</script>
