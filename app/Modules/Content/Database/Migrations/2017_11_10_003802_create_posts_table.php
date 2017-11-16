<?php

use Nova\Database\Schema\Blueprint;
use Nova\Database\Migrations\Migration;


class CreatePostsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table)
        {
            $table->increments('id');
            $table->integer('author_id')->unsigned()->index();

            $table->text('content')->nullable();
            $table->string('title')->nullable();
            $table->text('excerpt')->nullable();
            $table->string('status', 20)->nullable();
            $table->string('password')->nullable();
            $table->string('name', 200)->nullable();

            $table->text('content_filtered')->nullable();
            $table->integer('parent_id')->unsigned()->default(0)->index();
            $table->string('guid')->nullable();
            $table->integer('menu_order')->unsigned()->default(0);
            $table->string('type', 20)->nullable();
            $table->string('mime_type', 100)->nullable();

            $table->string('comment_status', 20)->nullable();
            $table->integer('comment_count')->unsigned()->default(0);

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
        Schema::dropIfExists('posts');
    }
}
