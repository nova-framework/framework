<?php

use Nova\Database\Schema\Blueprint;
use Nova\Database\Migrations\Migration;


class CreateContactAttachmentsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('contact_attachments');

        Schema::create('contact_attachments', function (Blueprint $table)
        {
            $table->increments('id');

            $table->integer('message_id')->unsigned()->index();
            $table->string('name');
            $table->integer('size')->unsigned();
            $table->string('type', 40)->nullable();
            $table->string('path');

            $table->timestamps();

            $table->foreign('message_id')->references('id')->on('contact_messages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contact_attachments');
    }
}
