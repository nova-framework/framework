<div class='btn-group pull-right' role='group' aria-label='...'>
	<a class='btn btn-sm btn-warning' href='<?= site_url('admin/roles/' .$role->id); ?>' title='<?= __d('backend', 'Show the Details'); ?>' role='button'><i class='fa fa-search'></i></a>
	<a class='btn btn-sm btn-success' href='<?= site_url('admin/roles/' .$role->id .'/edit'); ?>' title='<?= __d('backend', 'Edit this Role'); ?>' role='button'><i class='fa fa-pencil'></i></a>
	<a class='btn btn-sm btn-danger' href='#' data-toggle='modal' data-target='#modal_delete_user' data-id='<?= $role->id ?>' title='<?= __d('backend', 'Delete this Role'); ?>' role='button'><i class='fa fa-remove'></i></a>
</div>
