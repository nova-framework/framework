<?php

use Nova\Database\Schema\Blueprint;
use Nova\Database\Migrations\Migration;


class CreateTablePasswordReminders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('password_reminders', function(Blueprint $table) {
            $table->string('email')->index();
            $table->string('token')->index();
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
        Schema::dropIfExists('password_reminders');
    }
}
