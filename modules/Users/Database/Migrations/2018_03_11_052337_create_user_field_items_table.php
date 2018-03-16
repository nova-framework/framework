<?php

use Nova\Database\Schema\Blueprint;
use Nova\Database\Migrations\Migration;


class CreateUserFieldItemsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('user_field_items');

        Schema::create('user_field_items', function (Blueprint $table)
        {
            $table->increments('id');

            $table->string('title', 255);
            $table->string('name', 255);
            $table->string('type', 100);
            $table->integer('order')->default(0)->nullable();
            $table->string('rules', 255)->nullable();
            $table->text('options')->nullable();

            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();

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
        Schema::dropIfExists('user_field_items');
    }
}
