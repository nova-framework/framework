<section class="content-header">
    <h1><?= __d('users', 'Users'); ?></h1>
    <ol class="breadcrumb">
        <li><a href='<?= site_url('admin/dashboard'); ?>'><i class="fa fa-dashboard"></i> <?= __d('users', 'Dashboard'); ?></a></li>
        <li><?= __d('users', 'Users'); ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= Session::getMessages(); ?>

<style>
#usersTable .even {
    border-bottom: 1px solid #f9f9f9;
}

#usersTable td {
    vertical-align: middle;
}
</style>

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('users', 'Registered Users'); ?></h3>
    </div>
    <div class="box-body">
        <table id='usersTable' class='table table-striped table-hover responsive' style="width: 100%;">
            <thead>
                <tr class="bg-navy disabled">
                    <th><?= __d('users', 'ID'); ?></th>
                    <th><?= __d('users', 'Username'); ?></th>
                    <th><?= __d('users', 'Role'); ?></th>
                    <th><?= __d('users', 'First Name'); ?></th>
                    <th><?= __d('users', 'Last Name'); ?></th>
                    <th><?= __d('users', 'E-mail'); ?></th>
                    <th><?= __d('users', 'Created At'); ?></th>
                    <th class="text-right"><?= __d('users', 'Actions'); ?></th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

</section>

<div class="modal modal-default" id="modal_delete_user">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-label="Close" data-dismiss="modal" class="close" type="button">
                <span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><?= __d('users', 'Delete this User?'); ?></h4>
            </div>
            <div class="modal-body">
                <p><?= __d('users', 'Are you sure you want to remove this User, the operation being irreversible?'); ?></p>
                <p><?= __d('users', 'Please click the button <b>Delete</b> to proceed, or <b>Cancel</b> to abandon the operation.'); ?></p>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-primary pull-left col-md-3" type="button"><?= __d('users', 'Cancel'); ?></button>
                <form id="modal_delete_form" action="" method="POST">
                    <input type="hidden" name="userId" id="delete_user_id" value="0" />
                    <input type="hidden" name="_token" value="<?= csrf_token(); ?>" />
                    <input type="submit" name="button" class="btn btn btn-danger pull-right col-md-3" value="<?= __d('users', 'Delete'); ?>">
                </form>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
</div>

<script>
$(function ()
{
    $('#modal_delete_user').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal

        var id = button.data('id'); // Extract the Role ID from data-* attributes

        //
        $('#delete_user_id').val(id);

        $('#modal_delete_form').attr('action', "<?= site_url('admin/users'); ?>" + '/' + id + '/destroy');
    });

    $('#usersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            type: 'POST',
            url: '<?= site_url('admin/users/data'); ?>',
            data: function (data) {
                data._token = '<?= csrf_token(); ?>';
                data.offset = $('#usersTable').DataTable().page();
            }
        },
        pageLength: 25,
        lengthMenu: [ 3, 10, 25, 50, 75, 100 ],

        // We need to disable the ordering in some columns.
        columns: [
            { data: 'userid',   orderable: true,  searchable: false },
            { data: 'username', orderable: true,  searchable: true  },
            { data: 'role',     orderable: false, searchable: false },
            { data: 'name',     orderable: true,  searchable: true  },
            { data: 'surname',  orderable: true,  searchable: true  },
            { data: 'email',    orderable: true,  searchable: true  },
            { data: 'date',     orderable: true,  searchable: false },
            { data: 'actions',  orderable: false, searchable: false },
        ]
    });
});

</script>
