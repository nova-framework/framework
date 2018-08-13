<style>
<?= View::fetch('Modules/Users::Partials/UsersDataTable'); ?>
</style>

<section class="content-header">
    <h1><?= $title; ?></h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('admin/dashboard'); ?>"><i class="fa fa-dashboard"></i> <?= __d('users', 'Dashboard'); ?></a></li>
        <li><?= __d('users', 'Users'); ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= View::fetch('Partials/Messages'); ?>

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('users', 'Manage the Users'); ?></h3>
    </div>
    <div class="box-body">
        <?php if (Gate::allows('lists', 'Modules\Users\Models\FieldItem')) { ?>
        <a class="btn btn-primary col-sm-2 pull-left" href="<?= site_url('admin/users/fields'); ?>"><?= __d('users', 'Custom Fields / Profile'); ?></a>
        <?php } ?>
        <?php if (Gate::allows('create', 'Modules\Users\Models\User')) { ?>
        <a class="btn btn-success col-sm-2 pull-right" href="<?= site_url('admin/users/create'); ?>"><?= __d('users', 'Create a new User'); ?></a>
        <?php } ?>
    </div>
</div>

<div class="box box-widget">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('users', 'Registered Users'); ?></h3>
    </div>
    <div class="box-body no-padding">
        <table id='usersTable' class='table table-striped table-hover responsive' style="width: 100%;">
            <thead>
                <tr class="bg-navy disabled">
                    <th width='5%'><?= __d('users', 'ID'); ?></th>
                    <th width='18%'><?= __d('users', 'Username'); ?></th>
                    <th width='11%'><?= __d('users', 'Roles'); ?></th>
                    <th width='18%'><?= __d('users', 'Name and Surname'); ?></th>
                    <th width='18%'><?= __d('users', 'E-mail'); ?></th>
                    <th width='15%'><?= __d('users', 'Created At'); ?></th>
                    <th width='15%'><?= __d('users', 'Actions'); ?></th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<script>

$(function () {
    $('#usersTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.15/i18n/<?= Language::info(); ?>.json'
        },
        responsive: true,
        stateSave:  true,
        processing: true,
        serverSide: true,
        ajax: {
            type: 'POST',
            url: '<?= site_url('admin/users/data'); ?>'
        },
        pageLength: 15,
        lengthMenu: [ 5, 10, 15, 20, 25, 50, 100 ],

        columns: [
            { data: 'id',         name: 'id',         orderable: true,  searchable: false, className: "text-center" },
            { data: 'username',   name: 'username',   orderable: true,  searchable: true,  className: "text-center" },
            { data: 'roles',      name: 'roles.name', orderable: true,  searchable: true,  className: "text-center" },
            { data: 'realname',   name: 'realname',   orderable: true,  searchable: true,  className: "text-center" },
            { data: 'email',      name: 'email',      orderable: true,  searchable: true,  className: "text-center" },
            { data: 'created_at', name: 'created_at', orderable: true,  searchable: false, className: "text-center" },
            { data: 'actions',    name: 'actions',    orderable: false, searchable: false, className: "text-right compact" },
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
