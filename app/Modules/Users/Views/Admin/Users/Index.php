<section class="content-header">
    <h1><?= $title; ?></h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('admin/dashboard'); ?>"><i class="fa fa-dashboard"></i> <?= __d('users', 'Dashboard'); ?></a></li>
        <li><?= __d('users', 'Users'); ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= Session::getMessages(); ?>

<?php if (Gate::allows('create', 'App\Modules\Users\Models\User')) { ?>

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('users', 'Create a new User'); ?></h3>
    </div>
    <div class="box-body">
        <a class="btn btn-success col-sm-2 pull-right" href="<?= site_url('admin/users/create'); ?>"><?= __d('users', 'Create a new User'); ?></a>
    </div>
</div>

<?php } ?>

<div class="box box-widget">
    <div class="box-header">
        <h3 class="box-title"><?= __d('users', 'Registered Users'); ?></h3>
        <div class="box-tools">
        <?= $users->links(); ?>
        </div>
    </div>
    <div class="box-body no-padding">
        <?php $deletables = 0; ?>
        <?php if (! $users->isEmpty()) { ?>
        <table id="left" class="table table-striped table-hover responsive">
            <tr class="bg-navy disabled">
                <th style="text-align: center; vertical-align: middle;"><?= __d('users', 'ID'); ?></th>
                <th style="text-align: center; vertical-align: middle;"><?= __d('users', 'Username'); ?></th>
                <th style="text-align: center; vertical-align: middle;"><?= __d('users', 'Roles'); ?></th>
                <th style="text-align: center; vertical-align: middle;"><?= __d('users', 'Name and Surname'); ?></th>
                <th style="text-align: center; vertical-align: middle;"><?= __d('users', 'E-mail'); ?></th>
                <th style="text-align: center; vertical-align: middle;"><?= __d('users', 'Created At'); ?></th>
                <th style="text-align: right; vertical-align: middle;"><?= __d('users', 'Operations'); ?></th>
            </tr>
            <?php foreach ($users->getItems() as $user) { ?>
            <tr>
                <td style="text-align: center; vertical-align: middle;" width="5%"><?= $user->id; ?></td>
                <td style="text-align: center; vertical-align: middle;" width="18%"><?= $user->username; ?></td>
                <td style="text-align: center; vertical-align: middle;" width="11%"><?= implode(', ', $user->roles->lists('name')); ?></td>
                <td style="text-align: center; vertical-align: middle;" width="18%"><?= $user->realname(); ?></td>
                <td style="text-align: center; vertical-align: middle;" width="18%"><?= $user->email; ?></td>
                <td style="text-align: center; vertical-align: middle;" width="15%"><?= $user->created_at->formatLocalized(__d('users', '%d %b %Y, %R')); ?></td>
                <td style="text-align: right; vertical-align: middle;" width="15%">
                    <div class="btn-group" role="group" aria-label="...">
                        <?php if (Gate::allows('delete', $user)) { ?>
                        <?php $deletables++; ?>
                        <a class="btn btn-sm btn-danger" href="#" data-toggle="modal" data-target="#modal-delete-dialog" data-id="<?= $user->id; ?>" title="<?= __d('users', 'Delete this User'); ?>" role="button"><i class="fa fa-remove"></i></a>
                        <?php } ?>
                        <?php if (Gate::allows('update', $user)) { ?>
                        <a class="btn btn-sm btn-success" href="<?= site_url('admin/users/' .$user->id .'/edit'); ?>" title="<?= __d('users', 'Edit this User'); ?>" role="button"><i class="fa fa-pencil"></i></a>
                        <?php } ?>
                        <?php if (Gate::allows('view', $user)) { ?>
                        <a class="btn btn-sm btn-warning" href="<?= site_url('admin/users/' .$user->id); ?>" title="<?= __d('users', 'Show the Details'); ?>" role="button"><i class="fa fa-search"></i></a>
                        <?php } ?>
                    </div>
                </td>
            </tr>
            <?php } ?>
        </table>
        <?php } else { ?>
        <div class="alert alert-warning" style="margin: 0 5px 5px;">
            <h4><i class="icon fa fa-warning"></i> <?= strftime("%d %b %Y, %R", time()) ." - "; ?> <?= __d('users', 'No registered Users'); ?></h4>
            <?= __d('users', 'There are no registered Users.'); ?>
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
                <button aria-label="<?= __d('users', 'Close'); ?>" data-dismiss="modal" class="close" type="button">
                <span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><?= __d('users', 'Delete this User?'); ?></h4>
            </div>
            <div class="modal-body">
                <p><?= __d('users', 'Are you sure you want to remove this User, the operation being irreversible?'); ?></p>
                <p><?= __d('users', 'Please click the button <b>Delete</b> to proceed, or <b>Cancel</b> to abandon the operation.'); ?></p>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-primary pull-left col-md-3" type="button"><?= __d('users', 'Cancel'); ?></button>
                <form id="modal-delete-form" action="" method="POST">
                    <input type="hidden" name="id" id="delete-record-id" value="0" />
                    <input type="hidden" name="_token" value="<?= csrf_token(); ?>" />
                    <input type="submit" name="button" class="btn btn btn-danger pull-right col-md-3" value="<?= __d('users', 'Delete'); ?>">
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

        $('#modal-delete-form').attr('action', '<?= site_url("admin/users"); ?>/' + id + '/destroy');
    });
});

</script>

<?php } ?>
