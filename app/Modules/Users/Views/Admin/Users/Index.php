<section class="content-header">
    <h1><?= __('Users'); ?></h1>
    <ol class="breadcrumb">
        <li><a href='<?= site_url('admin/dashboard'); ?>'><i class="fa fa-dashboard"></i> <?= __('Dashboard'); ?></a></li>
        <li><?= __('Users'); ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= Session::message('status'); ?>

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __('Create a new User'); ?></h3>
    </div>
    <div class="box-body">
        <a class='btn btn-success' href='<?= site_url('admin/users/create'); ?>'><?= __('Create a new User'); ?></a>
    </div>
</div>

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __('Registered Users'); ?></h3>
        <div class="box-tools">
        <?= $users->links(); ?>
        </div>
    </div>
    <div class="box-body no-padding">
<?php if (! $users->isEmpty()) { ?>
        <table id='left' class='table table-striped table-hover responsive'>
            <tr class="bg-navy disabled">
                <th style='text-align: center; vertical-align: middle;'><?= __('ID'); ?></th>
                <th style='text-align: center; vertical-align: middle;'><?= __('Username'); ?></th>
                <th style='text-align: center; vertical-align: middle;'><?= __('Name and Surname'); ?></th>
                <th style='text-align: center; vertical-align: middle;'><?= __('Created At'); ?></th>
                <th style='text-align: center; vertical-align: middle;'><?= __('Updated At'); ?></th>
                <th style='text-align: right; vertical-align: middle;'><?= __('Operations'); ?></th>
            </tr>
<?php
    foreach ($users->getItems() as $user) {
        echo "
<tr>
    <td style='text-align: center; vertical-align: middle;' width='5%'>" .$user->id ."</td>
    <td style='text-align: center; vertical-align: middle;' width='25%'>" .$user->username ."</td>
    <td style='text-align: center; vertical-align: middle;' width='25%'>" .$user->realname ."</td>
    <td style='text-align: center; vertical-align: middle;' width='15%'>" .$user->created_at->formatLocalized('%d %b %Y, %R') ."</td>
    <td style='text-align: center; vertical-align: middle;' width='15%'>" .$user->updated_at->formatLocalized('%d %b %Y, %R') ."</td>
    <td style='text-align: right; vertical-align: middle;' width='15%'>
        <div class='btn-group' role='group' aria-label='...'>
            <a class='btn btn-sm btn-warning' href='" .site_url('admin/users/' .$user->id). "' title='". __('Show the Details') ."' role='button'><i class='fa fa-search'></i></a>
            <a class='btn btn-sm btn-success' href='" .site_url('admin/users/' .$user->id .'/edit') ."' title='" .__('Edit this User') ."' role='button'><i class='fa fa-pencil'></i></a>
            <a class='btn btn-sm btn-danger' href='#' data-toggle='modal' data-target='#confirm_" .$user->id ."' title='" .__('Delete this User') ."' role='button'><i class='fa fa-remove'></i></a>
        </div>
    </td>
</tr>";

    }
?>
        </table>
<?php } else { ?>
        <div class="alert alert-warning" style="margin: 0 5px 5px;">
            <h4><i class="icon fa fa-warning"></i> <?php echo strftime("%d %b %Y, %R", time()) ." - "; ?> <?= __('No registered Users'); ?></h4>
            <?= __('There are no registered Users.'); ?>
        </div>
<?php } ?>
    </div>
</div>

</section>

<?php
if (! $users->isEmpty()) {
    foreach ($users->getItems() as $user) {
?>
<div class="modal modal-default" id="confirm_<?= $user->id ?>">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-label="Close" data-dismiss="modal" class="close" type="button">
                <span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><?= __('Delete the User?'); ?></h4>
            </div>
            <div class="modal-body">
                <p><?= __('Are you sure you want to delete the User <b>{0}</b>, the operation being irreversible?', $user->name); ?></p>
                <p><?= __('Please click the button <b>Delete the User</b> to proceed, or <b>Cancel</b> to abbandon the operation.'); ?></p>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-primary pull-left col-md-3" type="button"><?= __('Cancel'); ?></button>
                <form action="<?= site_url('admin/users/' .$user->id .'/destroy'); ?>" method="POST">
                    <input type="hidden" name="csrfToken" value="<?= $csrfToken; ?>" />
                    <input type="submit" name="button" class="btn btn btn-danger pull-right" value="<?= __('Delete the User'); ?>">
                </form>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
</div>
<?php
    }
}

?>


