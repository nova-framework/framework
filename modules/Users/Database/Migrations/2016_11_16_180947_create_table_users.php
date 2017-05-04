<?php

use Nova\Database\Schema\Blueprint;
use Nova\Database\Migrations\Migration;


class CreateTableUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function($table)
        {
            $table->increments('id');
            $table->integer('role_id')->unsigned();
            $table->string('username', 100)->unique();
            $table->string('password');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('location')->nullable();
            $table->string('image')->nullable();
            $table->string('email', 100)->unique();
            $table->tinyInteger('activated')->unsigned()->default(0);
            $table->string('activation_code')->nullable();
            $table->string('remember_token')->nullable();
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
        Schema::dropIfExists('users');
    }
}
