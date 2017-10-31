<section class="content-header">
    <h1><?= __d('roles', 'Roles'); ?></h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('admin/dashboard'); ?>"><i class="fa fa-dashboard"></i> <?= __d('roles', 'Dashboard'); ?></a></li>
        <li><?= __d('roles', 'Roles'); ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= Session::getMessages(); ?>

<?php if (Gate::allows('create', 'App\Modules\Roles\Models\Role')) { ?>

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('roles', 'Create a new Role'); ?></h3>
    </div>
    <div class="box-body">
        <a class="btn btn-success col-sm-2 pull-right" href="<?= site_url('admin/roles/create'); ?>"><?= __d('roles', 'Create a new Role'); ?></a>
    </div>
</div>

<?php } ?>

<div class="box box-widget">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('roles', 'Registered Roles'); ?></h3>
        <div class="box-tools">
        <?= $roles->links(); ?>
        </div>
    </div>
    <div class="box-body no-padding">
        <?php $deletables = 0; ?>
        <?php if (! $roles->isEmpty()) { ?>
        <table id="left" class="table table-striped table-hover responsive">
            <tr class="bg-navy disabled">
                <th style="text-align: center; vertical-align: middle;"><?= __d('roles', 'ID'); ?></th>
                <th style="text-align: center; vertical-align: middle;"><?= __d('roles', 'Name'); ?></th>
                <th style="text-align: center; vertical-align: middle;"><?= __d('roles', 'Slug'); ?></th>
                <th style="text-align: center; vertical-align: middle;"><?= __d('roles', 'Description'); ?></th>
                <th style="text-align: center; vertical-align: middle;"><?= __d('roles', 'Users'); ?></th>
                <th style="text-align: right; vertical-align: middle;"><?= __d('roles', 'Operations'); ?></th>
            </tr>
            <?php foreach ($roles->getItems() as $role) { ?>
            <tr>
                <td style="text-align: center; vertical-align: middle;" width="5%"><?= $role->id; ?></td>
                <td style="text-align: center; vertical-align: middle;" width="17%"><?= $role->name; ?></td>
                <td style="text-align: center; vertical-align: middle;" width="17%"><?= $role->slug; ?></td>
                <td style="text-align: left; vertical-align: middle;" width="40%"><?= $role->description; ?></td>
                <td style="text-align: center; vertical-align: middle;" width="6%"><?= $role->users->count(); ?></td>
                <td style="text-align: right; vertical-align: middle;" width="15%">
                    <div class="btn-group" role="group" aria-label="...">
                        <?php if (Gate::allows('delete', $role)) { ?>
                        <?php $deletables++; ?>
                        <a class="btn btn-sm btn-danger" href="#" data-toggle="modal" data-target="#modal-delete-dialog" data-id="<?= $role->id; ?>" title="<?= __d('roles', 'Delete this Role'); ?>" role="button"><i class="fa fa-remove"></i></a>
                        <?php } ?>
                        <?php if (Gate::allows('update', $role)) { ?>
                        <a class="btn btn-sm btn-success" href="<?= site_url('admin/roles/' .$role->id .'/edit'); ?>" title="<?= __d('roles', 'Edit this Role'); ?>" role="button"><i class="fa fa-pencil"></i></a>
                        <?php } ?>
                        <?php if (Gate::allows('view', $role)) { ?>
                        <a class="btn btn-sm btn-warning" href="<?= site_url('admin/roles/' .$role->id); ?>" title="<?= __d('roles', 'Show the Details'); ?>" role="button"><i class="fa fa-search"></i></a>
                        <?php } ?>
                    </div>
                </td>
            </tr>
            <?php } ?>
        </table>
        <?php } else { ?>
        <div class="alert alert-warning" style="margin: 0 5px 5px;">
            <h4><i class="icon fa fa-warning"></i> <?= strftime("%d %b %Y, %R", time()) ." - "; ?> <?= __d('roles', 'No registered Roles'); ?></h4>
            <?= __d('roles', 'There are no registered Roles.'); ?>
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
                <button aria-label="<?= __d('roles', 'Close'); ?>" data-dismiss="modal" class="close" type="button">
                <span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><?= __d('roles', 'Delete this Role?'); ?></h4>
            </div>
            <div class="modal-body">
                <p><?= __d('roles', 'Are you sure you want to remove this Role, the operation being irreversible?'); ?></p>
                <p><?= __d('roles', 'Please click the button <b>Delete</b> to proceed, or <b>Cancel</b> to abandon the operation.'); ?></p>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-primary pull-left col-md-3" type="button"><?= __d('roles', 'Cancel'); ?></button>
                <form id="modal-delete-form" action="" method="POST">
                    <input type="hidden" name="id" id="delete-record-id" value="0" />
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
        $('#delete-record-id').val(id);

        $('#modal-delete-form').attr('action', '<?= site_url("admin/roles"); ?>/' + id + '/destroy');
    });
});

</script>

<?php } ?>

