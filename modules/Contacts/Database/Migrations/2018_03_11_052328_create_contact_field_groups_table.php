<?php

use Nova\Database\Schema\Blueprint;
use Nova\Database\Migrations\Migration;


class CreateContactFieldGroupsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('contact_field_groups');

        Schema::create('contact_field_groups', function (Blueprint $table)
        {
            $table->increments('id');

            $table->integer('contact_id')->unsigned();
            $table->string('title', 255);
            $table->text('content')->nullable();
            $table->integer('order')->default(0);

            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();

            $table->timestamps();

            //
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contact_field_groups');
    }
}
