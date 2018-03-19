<?php

use Nova\Database\Schema\Blueprint;
use Nova\Database\Migrations\Migration;


class CreateUserFieldsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('user_fields');

        Schema::create('user_fields', function (Blueprint $table)
        {
            $table->increments('id');

            $table->integer('user_id')->unsigned();
            $table->integer('field_item_id')->unsigned();
            $table->string('type', 255);
            $table->string('name', 255);
            $table->text('value')->nullable();

            $table->timestamps();

            $table->foreign('field_item_id')->references('id')->on('user_field_items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_fields');
    }
}
