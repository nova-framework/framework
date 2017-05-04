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
