<?php

use Nova\Database\Schema\Blueprint;
use Nova\Database\Migrations\Migration;

use App\Database\UninstallPermissionsTrait;


class FilesUpdatePermissionsTable extends Migration
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
        $this->uninstallPermissions('files');
    }
}
