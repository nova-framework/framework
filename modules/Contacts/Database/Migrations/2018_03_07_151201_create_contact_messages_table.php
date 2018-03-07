<?php

use Nova\Database\Schema\Blueprint;
use Nova\Database\Migrations\Migration;


class CreateContactMessagesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contact_messages', function (Blueprint $table)
        {
            $table->increments('id');

            $table->integer('contact_id')->unsigned()->index();
            $table->string('author')->nullable();
            $table->string('author_email')->nullable();
            $table->string('author_url')->nullable();
            $table->string('author_ip')->nullable();
            $table->text('content')->nullable();
            $table->integer('user_id')->unsigned()->default(0)->index();
            $table->string('path')->nullable();

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
        Schema::dropIfExists('contact_messages');
    }
}
