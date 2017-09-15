<?php

use Nova\Database\Schema\Blueprint;
use Nova\Database\Migrations\Migration;


class CreateMessagesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table)
        {
            $table->increments('id');
            $table->integer('sender_id')->unsigned()->default(0);
            $table->integer('receiver_id')->unsigned()->default(0);
            $table->integer('parent_id')->nullable()->unsigned();
            $table->string('subject')->nullable();
            $table->text('body');
            $table->tinyInteger('is_read')->default(0);
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
        Schema::dropIfExists('messages');
    }
}
