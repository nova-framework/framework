<section class="content-header">
    <h1><?= __d('users', 'Roles'); ?></h1>
    <ol class="breadcrumb">
        <li><a href='<?= site_url('admin/dashboard'); ?>'><i class="fa fa-dashboard"></i> <?= __d('users', 'Dashboard'); ?></a></li>
        <li><?= __d('users', 'Roles'); ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= Session::getMessages(); ?>

<style>

#rolesTable {
    border-bottom: 1px solid #d2d6de;
}

#rolesTable_paginate .pagination {
    margin: 5px 0 -3px;
}

</style>

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('users', 'Registered Roles'); ?></h3>
    </div>
    <div class="box-body">
        <table id='rolesTable' class='table table-striped table-hover responsive' style="width: 100%;">
            <thead>
            <tr class="bg-primary disabled">
                <th width='5%'><?= __d('users', 'ID'); ?></th>
                <th width='17%'><?= __d('users', 'Name'); ?></th>
                <th width='17%'><?= __d('users', 'Slug'); ?></th>
                <th width='43%'><?= __d('users', 'Description'); ?></th>
                <th width='6%'><?= __d('users', 'Users'); ?></th>
                <th width='13%'><?= __d('users', 'Operations'); ?></th>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
    <div class="box-footer with-border">
        <a class='btn btn-success' href='<?= site_url('admin/roles/create'); ?>'><i class='fa fa-plus-square'></i> <?= __d('users', 'Create a new Role'); ?></a>
    </div>
</div>

</section>

<div class="modal modal-default" id="modal_delete_role">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-label="Close" data-dismiss="modal" class="close" type="button">
                <span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><?= __d('users', 'Delete this Role?'); ?></h4>
            </div>
            <div class="modal-body">
                <p><?= __d('users', 'Are you sure you want to remove this Role, the operation being irreversible?'); ?></p>
                <p><?= __d('users', 'Please click the button <b>Delete</b> to proceed, or <b>Cancel</b> to abandon the operation.'); ?></p>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-primary pull-left col-md-3" type="button"><?= __d('users', 'Cancel'); ?></button>
                <form id="modal_delete_form" action="" method="POST">
                    <input type="hidden" name="roleId" id="delete_role_id" value="0" />
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
    $('#rolesTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.15/i18n/<?= $langInfo; ?>.json'
        },
        stateSave: true,
        processing: true,
        serverSide: true,
        ajax: {
            type: 'POST',
            url: '<?= site_url('admin/roles/data'); ?>',
            data: function (data) {
                data._token = '<?= csrf_token(); ?>';
            }
        },
        pageLength: 15,
        lengthMenu: [ 5, 10, 15, 20, 25, 50, 75, 100 ],

        columns: [
            { data: 'roleid',   orderable: true,  searchable: false },
            { data: 'name',     orderable: true,  searchable: true  },
            { data: 'slug',     orderable: true,  searchable: true  },
            { data: 'details',  orderable: false, searchable: false },
            { data: 'users',    orderable: false, searchable: false },
            { data: 'actions',  orderable: false, searchable: false },
        ],

        drawCallback: function(settings) {
            var pagination = $(this).closest('.dataTables_wrapper').find('.dataTables_paginate');

            pagination.toggle(this.api().page.info().pages > 1);
        },
    });

    $('#modal_delete_role').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal

        var id = button.data('id'); // Extract the Role ID from data-* attributes

        //
        $('#delete_user_id').val(id);

        $('#modal_delete_form').attr('action', "<?= site_url('admin/roles'); ?>" + '/' + id + '/destroy');
    });
});

</script>
