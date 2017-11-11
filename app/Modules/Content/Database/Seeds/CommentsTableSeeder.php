<?php

namespace App\Modules\Content\Database\Seeds;

use Nova\Database\ORM\Model;
use Nova\Database\Seeder;

use App\Modules\Content\Models\Comment;
use App\Modules\Content\Models\Post;


class CommentsTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Truncate the table before seeding.
        Comment::truncate();

        //
        $post = Post::find(1);

        $comment = Comment::create(array(
            'id'           => 1,
            'post_id'      => $post->id,
            'author'       => 'A Nova Commenter',
            'author_email' => 'rookie@novaframework.dev',
            'author_url'   => 'https://novaframework.dev',
            'author_ip'    => '',
            'content'      => 'Hi, this is a comment.
To get started with moderating, editing, and deleting comments, please visit the Comments screen in the dashboard.
Commenter avatars come from <a href="https://gravatar.com">Gravatar</a>.',

            'karma'        => 0,
            'approved'     => 1,
            'agent'        => '',
            'type'         => '',
            'parent_id'    => 0,
            'user_id'      => 0,
        ));

        $post->updateCommentCount();
    }
}
