<style>
<?= View::fetch('Modules/Roles::Partials/RolesDataTable'); ?>
</style>

<section class="content-header">
    <h1><?= __d('roles', 'Roles'); ?></h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('admin/dashboard'); ?>"><i class="fa fa-dashboard"></i> <?= __d('roles', 'Dashboard'); ?></a></li>
        <li><?= __d('roles', 'Roles'); ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= View::fetch('Partials/Messages'); ?>

<?php if (Gate::allows('create', 'Modules\Roles\Models\Role')) { ?>

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('roles', 'Create a new Role'); ?></h3>
    </div>
    <div class="box-body">
        <a class="btn btn-success col-sm-2 pull-right" href="<?= site_url('admin/roles/create'); ?>"><?= __d('roles', 'Create a new Role'); ?></a>
    </div>
</div>

<?php $boxType = 'widget'; ?>
<?php } else { ?>
<?php $boxType = 'default'; ?>
<?php } ?>

<div class="box box-<?= $boxType; ?>">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('roles', 'Registered Roles'); ?></h3>
    </div>
    <div class="box-body no-padding">
        <table id='rolesTable' class='table table-striped table-hover responsive' style="width: 100%;">
            <thead>
                <tr class="bg-navy disabled">
                    <th width='5%'><?= __d('roles', 'ID'); ?></th>
                    <th width='15%'><?= __d('roles', 'Name'); ?></th>
                    <th width='15%'><?= __d('roles', 'Slug'); ?></th>
                    <th width='25%'><?= __d('roles', 'Description'); ?></th>
                    <th width='10%'><?= __d('roles', 'Users'); ?></th>
                    <th width='15%'><?= __d('users', 'Created At'); ?></th>
                    <th width='15%'><?= __d('roles', 'Actions'); ?></th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<script>

$(function () {
    $('#rolesTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.15/i18n/<?= Language::info(); ?>.json'
        },
        responsive: true,
        stateSave:  true,
        processing: true,
        serverSide: true,
        ajax: {
            type: 'POST',
            url: '<?= site_url('admin/roles/data'); ?>'
        },
        pageLength: 15,
        lengthMenu: [ 5, 10, 15, 20, 25, 50, 100 ],

        columns: [
            { data: 'id',          name: 'id',          orderable: true,  searchable: false, className: "text-center" },
            { data: 'name',        name: 'name',        orderable: true,  searchable: true,  className: "text-center" },
            { data: 'slug',        name: 'slug',        orderable: true,  searchable: true,  className: "text-center" },
            { data: 'description', name: 'description', orderable: false, searchable: true,  className: "text-left" },
            { data: 'users',       name: 'users_count', orderable: true,  searchable: false, className: "text-center" },
            { data: 'created_at',  name: 'created_at',  orderable: true,  searchable: false, className: "text-center" },
            { data: 'actions',     name: 'actions',     orderable: false, searchable: false, className: "text-right compact" },
        ],

        drawCallback: function(settings)
        {
            var pagination = $(this).closest('.dataTables_wrapper').find('.dataTables_paginate');

            pagination.toggle(this.api().page.info().pages > 1);
        },
    });
});

</script>

</section>

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
