<?php

use Nova\Database\Schema\Blueprint;
use Nova\Database\Migrations\Migration;


class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table)
        {
            $table->increments('id');
            $table->string('username', 100)->unique();
            $table->string('password');
            $table->string('realname');
            $table->string('email', 100)->unique();
            $table->string('image')->nullable();
            $table->tinyInteger('activated')->unsigned()->default(0);
            $table->string('activation_code')->nullable();
            $table->string('remember_token')->nullable();
            $table->string('api_token', 100)->unique()->nullable();

            // Custom timestamps.
            $table->timestamp('created_at')->default('0000-00-00 00:00:00');
            $table->timestamp('updated_at')->default('0000-00-00 00:00:00');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
