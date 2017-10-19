<?php

use Nova\Database\Schema\Blueprint;
use Nova\Database\Migrations\Migration;

use App\Modules\Platform\Database\UninstallPermissionsTrait;


class SystemUpdatePermissionsTable extends Migration
{
    use UninstallPermissionsTrait;


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
        $this->uninstallPermissions('platform');
    }
}
