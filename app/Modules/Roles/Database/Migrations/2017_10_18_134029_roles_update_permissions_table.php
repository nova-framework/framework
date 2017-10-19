<?php

use Nova\Database\Schema\Blueprint;
use Nova\Database\Migrations\Migration;

use App\Modules\Platform\Database\DeletePermissionsTrait;


class RolesUpdatePermissionsTable extends Migration
{
    use DeletePermissionsTrait;


    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->deletePermissions('roles');
    }
}
