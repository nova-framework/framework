<?php

use Nova\Database\Schema\Blueprint;
use Nova\Database\Migrations\Migration;
use Nova\Support\Facades\Cache;


class CreatePermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permissions', function (Blueprint $table)
        {
            $table->increments('id');
            $table->string('name');
            $table->string('slug', 100)->unique();
            $table->string('group', 100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('permissions');

        // Invalidate the cached system permissions.
        Cache::forget('system_permissions');
    }

}
