<?php

use Nova\Database\Schema\Blueprint;
use Nova\Database\Migrations\Migration;


class CreateContactFieldItemsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('contact_field_items');

        Schema::create('contact_field_items', function (Blueprint $table)
        {
            $table->increments('id');

            $table->integer('field_group_id')->unsigned();
            $table->string('title', 255);
            $table->string('name', 255);
            $table->string('type', 100);
            $table->integer('order')->default(0)->nullable();
            $table->string('rules', 255)->nullable();
            $table->text('options')->nullable();

            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();

            $table->timestamps();

            //
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');

            $table->foreign('field_group_id')->references('id')->on('contact_field_groups')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contact_field_items');
    }
}
