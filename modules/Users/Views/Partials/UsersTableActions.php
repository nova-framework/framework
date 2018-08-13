<div class='btn-group pull-right' role='group' aria-label='...'>
    <?php if (Gate::allows('delete', $user)) { ?>
    <a class='btn btn-sm btn-danger' href='#' data-toggle='modal' data-target='#modal-delete-dialog' data-id='<?= $user->id ?>' title='<?= __d('users', 'Delete this User'); ?>' role='button'><i class='fa fa-remove'></i></a>
    <?php } ?>
    <?php if (Gate::allows('update', $user)) { ?>
    <a class='btn btn-sm btn-success' href='<?= site_url('admin/users/{0}/edit', $user->id); ?>' title='<?= __d('users', 'Edit this User'); ?>' role='button'><i class='fa fa-pencil'></i></a>
    <?php } ?>
    <?php if (Gate::allows('view', $user)) { ?>
    <a class='btn btn-sm btn-warning' href='<?= site_url('admin/users/{0}', $user->id); ?>' title='<?= __d('users', 'Show the Details'); ?>' role='button'><i class='fa fa-search'></i></a>
    <?php } ?>
</div>
