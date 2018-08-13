<div class="btn-group" role="group" aria-label="...">
    <?php if (Gate::allows('delete', $role)) { ?>
    <a class="btn btn-sm btn-danger" href="#" data-toggle="modal" data-target="#modal-delete-dialog" data-id="<?= $role->id; ?>" title="<?= __d('roles', 'Delete this Role'); ?>" role="button"><i class="fa fa-remove"></i></a>
    <?php } ?>
    <?php if (Gate::allows('update', $role)) { ?>
    <a class="btn btn-sm btn-success" href="<?= site_url('admin/roles/' .$role->id .'/edit'); ?>" title="<?= __d('roles', 'Edit this Role'); ?>" role="button"><i class="fa fa-pencil"></i></a>
    <?php } ?>
    <?php if (Gate::allows('view', $role)) { ?>
    <a class="btn btn-sm btn-warning" href="<?= site_url('admin/roles/' .$role->id); ?>" title="<?= __d('roles', 'Show the Details'); ?>" role="button"><i class="fa fa-search"></i></a>
    <?php } ?>
</div>
