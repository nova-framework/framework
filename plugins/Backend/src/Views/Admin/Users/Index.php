<style>

#usersTable td {
    vertical-align: middle;
}

#usersTable td.compact {
    padding: 5px;
}

#usersTable_paginate .pagination {
    margin: 5px 0 -3px;
}

tfoot input {
    width: 100%;
    padding: 3px;
    box-sizing: border-box;
}

#usersTable .dataTables_wrapper {
    clear: both;
    min-height: 400px;
    position: relative;
}

</style>

<div class="row">
    <h1><?= __d('backend', 'Users'); ?></h1>
    <ol class="breadcrumb">
        <li><a href='<?= site_url('admin/dashboard'); ?>'><?= __d('backend', 'Dashboard'); ?></a></li>
        <li><?= __d('backend', 'Users'); ?></li>
    </ol>
</div>

<?= View::fetch('Partials/Messages'); ?>

<!-- Main content -->
<div class="row">
    <h3><?= __d('backend', 'Manage the Users'); ?></h3>
    <br>

    <a class='btn btn-success' href='<?= site_url('admin/users/create'); ?>'><i class='fa fa-user-plus'></i> <?= __d('backend', 'Create a new User'); ?></a>
    <hr>
</div>

<div class="row">
    <h3><?= __d('backend', 'Registered Users'); ?></h3>
    <br>

    <table id='usersTable' class='table table-bordered table-striped table-hover responsive' style="width: 100%;">
        <thead>
            <tr>
                <th width='5%'><?= __d('backend', 'ID'); ?></th>
                <th width='13%'><?= __d('backend', 'Username'); ?></th>
                <th width='12%'><?= __d('backend', 'Role'); ?></th>
                <th width='13%'><?= __d('backend', 'First Name'); ?></th>
                <th width='13%'><?= __d('backend', 'Last Name'); ?></th>
                <th width='20%'><?= __d('backend', 'E-mail'); ?></th>
                <th width='12%'><?= __d('backend', 'Created At'); ?></th>
                <th width='12%'><?= __d('backend', 'Actions'); ?></th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>

<div class="modal modal-default" id="modal_delete_user">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-label="Close" data-dismiss="modal" class="close" type="button">
                <span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><?= __d('backend', 'Delete this User?'); ?></h4>
            </div>
            <div class="modal-body">
                <p><?= __d('backend', 'Are you sure you want to remove this User, the operation being irreversible?'); ?></p>
                <p><?= __d('backend', 'Please click the button <b>Delete</b> to proceed, or <b>Cancel</b> to abandon the operation.'); ?></p>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-primary pull-left col-md-3" type="button"><?= __d('backend', 'Cancel'); ?></button>
                <form id="modal_delete_form" action="" method="POST">
                    <input type="hidden" name="userId" id="delete_user_id" value="0" />
                    <input type="hidden" name="_token" value="<?= csrf_token(); ?>" />
                    <input type="submit" name="button" class="btn btn btn-danger pull-right col-md-3" value="<?= __d('backend', 'Delete'); ?>">
                </form>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
</div>

<script>

$(function () {
    $('#usersTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.15/i18n/<?= $langInfo; ?>.json'
        },
        responsive: true,
        stateSave: true,
        processing: true,
        serverSide: true,
        ajax: {
            type: 'POST',
            url: '<?= site_url('admin/users/data'); ?>',
            data: function (data) {
                data._token = '<?= csrf_token(); ?>';
            }
        },
        pageLength: 15,
        lengthMenu: [ 5, 10, 15, 20, 25, 50, 75, 100 ],

        columns: [
            { data: 'userid',   orderable: true,  searchable: false },
            { data: 'username', orderable: true,  searchable: true,  className: "text-center"  },
            { data: 'role',         orderable: true,  searchable: false, className: "text-center" },
            { data: 'name',         orderable: true,  searchable: true,  className: "text-center"  },
            { data: 'surname',  orderable: true,  searchable: true,  className: "text-center"  },
            { data: 'email',    orderable: true,  searchable: true,  className: "text-center" },
            { data: 'date',         orderable: true,  searchable: false, className: "text-center" },
            { data: 'actions',  orderable: false, searchable: false, className: "text-right compact" },
        ],

        drawCallback: function(settings)
        {
            var pagination = $(this).closest('.dataTables_wrapper').find('.dataTables_paginate');

            pagination.toggle(this.api().page.info().pages > 1);
        },
    });

    $('#modal_delete_user').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal

        var id = button.data('id'); // Extract the Role ID from data-* attributes

        //
        $('#delete_user_id').val(id);

        $('#modal_delete_form').attr('action', "<?= site_url('admin/users'); ?>" + '/' + id + '/destroy');
    });
});

</script>
