<?php

use Nova\Database\Schema\Blueprint;
use Nova\Database\Migrations\Migration;


class CreateTermTaxonomyTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('term_taxonomy', function (Blueprint $table)
        {
            $table->increments('id');
            $table->integer('term_id')->unsigned()->default(0);
            $table->string('taxonomy', 32);
            $table->text('description')->nullable();
            $table->integer('parent_id')->unsigned()->default(0);
            $table->integer('count')->unsigned()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('term_taxonomy');
    }
}
