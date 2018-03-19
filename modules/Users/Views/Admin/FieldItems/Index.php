<section class="content-header">
    <h1><?= $title; ?></h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('admin/dashboard'); ?>"><i class="fa fa-dashboard"></i> <?= __d('users', 'Dashboard'); ?></a></li>
        <li><a href="<?= site_url('admin/users'); ?>"><?= __d('users', 'Users'); ?></a></li>
        <li><?= __d('users', 'Manage the Custom Fields'); ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= View::fetch('Partials/Messages'); ?>

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('users', 'Create a new Custom Field'); ?></h3>
    </div>
    <div class="box-body">
        <a class="btn btn-success col-sm-2 pull-right" href="<?= site_url('admin/users/fields/create'); ?>"><?= __d('users', 'Create a new Field'); ?></a>
    </div>
</div>

<div class="box box-widget">
    <div class="box-header">
        <h3 class="box-title"><?= __d('users', 'Registered Custom Fields'); ?></h3>
    </div>
    <div class="box-body no-padding">
        <?php if (! $items->isEmpty()) { ?>
        <table id="left" class="table table-striped table-hover responsive">
            <tr class="bg-navy disabled">
                <th style="text-align: center; vertical-align: middle;"><?= __d('roles', 'ID'); ?></th>
                <th style="text-align: center; vertical-align: middle;"><?= __d('roles', 'Label'); ?></th>
                <th style="text-align: center; vertical-align: middle;"><?= __d('roles', 'Name'); ?></th>
                <th style="text-align: center; vertical-align: middle;"><?= __d('roles', 'Type'); ?></th>
                <th style="text-align: center; vertical-align: middle;"><?= __d('roles', 'Order'); ?></th>
                <th style="text-align: left; vertical-align: middle;"><?= __d('roles', 'Rules'); ?></th>
                <th style="text-align: right; vertical-align: middle;"><?= __d('roles', 'Operations'); ?></th>
            </tr>
            <?php foreach ($items as $item) { ?>
            <tr>
                <td style="text-align: center; vertical-align: middle;" width="5%"><?= $item->id; ?></td>
                <td style="text-align: center; vertical-align: middle;" width="15%"><?= $item->title; ?></td>
                <td style="text-align: center; vertical-align: middle;" width="10%"><?= $item->name; ?></td>
                <td style="text-align: center; vertical-align: middle;" width="10%"><?= $item->type; ?></td>
                <td style="text-align: center; vertical-align: middle;" width="10%"><?= $item->order; ?></td>
                <td style="text-align: left; vertical-align: middle;" width="25%"><?= $item->rules ?: '-'; ?></td>
                <td style="text-align: right; vertical-align: middle;" width="15%">
                    <div class="btn-group" role="group" aria-label="...">
                        <a class="btn btn-sm btn-danger" href="#" data-toggle="modal" data-target="#modal-delete-dialog" data-id="<?= $item->id; ?>" title="<?= __d('roles', 'Delete this Role'); ?>" role="button"><i class="fa fa-remove"></i></a>
                        <a class="btn btn-sm btn-success" href="<?= site_url('admin/users/fields/{0}/edit', $item->id); ?>" title="<?= __d('roles', 'Edit this Role'); ?>" role="button"><i class="fa fa-pencil"></i></a>
                        <a class="btn btn-sm btn-warning" href="<?= site_url('admin/users/fields/{0}', $item->id); ?>" title="<?= __d('roles', 'Show the Details'); ?>" role="button"><i class="fa fa-search"></i></a>
                    </div>
                </td>
            </tr>
            <?php } ?>
        </table>
        <?php } else { ?>
        <div class="alert alert-warning" style="margin: 0 5px 5px;">
            <h4><i class="icon fa fa-warning"></i> <?= strftime("%d %b %Y, %R", time()) ." - "; ?> <?= __d('users', 'No registered Custom Fields'); ?></h4>
            <?= __d('users', 'There are no registered Fields for this Group.'); ?>
        </div>
        <?php } ?>
    </div>
</div>

<div class="clearfix"></div>
<br>

</section>

<div class="modal modal-default" id="modal-delete-dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-label="<?= __d('roles', 'Close'); ?>" data-dismiss="modal" class="close" type="button">
                <span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><?= __d('roles', 'Delete this Field Item?'); ?></h4>
            </div>
            <div class="modal-body">
                <p><?= __d('roles', 'Are you sure you want to remove this Field Item, the operation being irreversible?'); ?></p>
                <p><?= __d('roles', 'Please click the button <b>Delete</b> to proceed, or <b>Cancel</b> to abandon the operation.'); ?></p>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-primary pull-left col-md-3" type="button"><?= __d('roles', 'Cancel'); ?></button>
                <form id="modal-delete-form" action="" method="POST">
                    <input type="hidden" name="id" id="delete-field-item-id" value="0" />
                    <input type="hidden" name="_token" value="<?= csrf_token(); ?>" />
                    <input type="submit" name="button" class="btn btn btn-danger pull-right col-md-3" value="<?= __d('roles', 'Delete'); ?>">
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

        var id  = button.data('id');

        //
        $('#delete-field-item-id').val(id);

        $('#modal-delete-form').attr('action', '<?= site_url("admin/users/fields"); ?>/' + id + '/destroy');
    });
});

</script>
