<?php

use Nova\Database\Schema\Blueprint;
use Nova\Database\Migrations\Migration;


class CreateProfilesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table)
        {
            $table->increments('id');

            $table->string('name');
            $table->string('slug');
            $table->text('description');

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
        Schema::dropIfExists('profiles');
    }
}
