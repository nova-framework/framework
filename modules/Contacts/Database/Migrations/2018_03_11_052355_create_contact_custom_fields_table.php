<?php

use Nova\Database\Schema\Blueprint;
use Nova\Database\Migrations\Migration;


class CreateContactCustomFieldsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('contact_custom_fields');

        Schema::create('contact_custom_fields', function (Blueprint $table)
        {
            $table->increments('id');

            $table->integer('message_id')->unsigned();
            $table->integer('field_item_id')->unsigned();
            $table->string('type', 255);
            $table->string('name', 255);
            $table->text('value')->nullable();

            $table->timestamps();

            $table->foreign('message_id')->references('id')->on('contact_messages')->onDelete('cascade');
            $table->foreign('field_item_id')->references('id')->on('contact_field_items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contact_custom_fields');
    }
}
