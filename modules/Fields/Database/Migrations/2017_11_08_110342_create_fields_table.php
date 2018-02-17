<?php

use Nova\Database\Schema\Blueprint;
use Nova\Database\Migrations\Migration;


class CreateFieldsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fields', function (Blueprint $table)
        {
            $table->increments('id');
            $table->morphs('model');
            $table->string('name');
            $table->string('key');
            $table->string('type');
            $table->string('validate')->nullable();
            $table->smallInteger('order')->default(1);
            $table->tinyInteger('columns')->default(8);
            $table->tinyInteger('hidden')->default(0);
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
        Schema::dropIfExists('fields');
    }
}
