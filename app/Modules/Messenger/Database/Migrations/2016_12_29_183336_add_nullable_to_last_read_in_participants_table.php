<?php

use Nova\Database\Schema\Blueprint;
use Nova\Database\Migrations\Migration;


class AddNullableToLastReadInParticipantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tablePrefix = DB::getTablePrefix();

        DB::statement('ALTER TABLE `' .$tablePrefix .'participants` CHANGE COLUMN `last_read` `last_read` timestamp NULL ON UPDATE CURRENT_TIMESTAMP DEFAULT CURRENT_TIMESTAMP;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
