<?php

use Nova\Database\Schema\Blueprint;
use Nova\Database\Migrations\Migration;


class CreateChatVideoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat_video', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('sender_id')->unsigned();
            $table->integer('receiver_id')->unsigned();
            $table->string('room_name');
            $table->timestamp('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chat_video');
    }
}
