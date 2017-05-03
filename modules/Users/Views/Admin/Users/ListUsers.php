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

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('users', 'Registered Users'); ?></h3>
    </div>
    <div class="box-body">
        <table id='usersTable' class='table table-bordered table-striped table-hover responsive' style="width: 100%;">
            <thead>
                <tr>
                    <th><?= __d('users', 'ID'); ?></th>
                    <th><?= __d('users', 'Username'); ?></th>
                    <th><?= __d('users', 'Role'); ?></th>
                    <th><?= __d('users', 'Name and Surname'); ?></th>
                    <th><?= __d('users', 'E-mail'); ?></th>
                    <th><?= __d('users', 'Created At'); ?></th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th><?= __d('users', 'ID'); ?></th>
                    <th><?= __d('users', 'Username'); ?></th>
                    <th><?= __d('users', 'Role'); ?></th>
                    <th><?= __d('users', 'Name and Surname'); ?></th>
                    <th><?= __d('users', 'E-mail'); ?></th>
                    <th><?= __d('users', 'Created At'); ?></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

</section>

<script>

$(document).ready(function ()
{
    $('#usersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            type: 'POST',
            url: '<?= site_url('admin/users/data'); ?>',

            // Handle the framework's CSRF Token.
            data: function (data) {
                data._token = '<?= csrf_token(); ?>';
            }
        },
        pageLength: 25,
        lengthMenu: [ 3, 10, 25, 50, 75, 100 ],

        // We need to disable the ordering in some columns.
        columns: [
            { data: 'id',         orderable: true,  searchable: false },
            { data: 'username',   orderable: true,  searchable: true  },
            { data: 'role',       orderable: false, searchable: false },
            { data: 'realname',   orderable: false, searchable: false },
            { data: 'email',      orderable: true,  searchable: true  },
            { data: 'created_at', orderable: true,  searchable: false },
        ]
    });
});

</script>
