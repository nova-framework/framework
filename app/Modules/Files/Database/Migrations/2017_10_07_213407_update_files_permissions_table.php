<?php

use Nova\Database\Schema\Blueprint;
use Nova\Database\Migrations\Migration;
use Nova\Support\Facades\Cache;

use App\Models\Permission;


class UpdateFilesPermissionsTable extends Migration
{

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
        $permissions = Permission::where('group', 'files')->get();

        foreach ($permissions as $permission) {
            $permission->roles()->detach();

            $permission->delete();
        }

        // Invalidate the cached system permissions.
        Cache::forget('system_permissions');
    }
}
