<?php

namespace Modules\Content\Platform\Types\Posts;

use Modules\Content\Platform\Types\Post as BasePost;


class Post extends BasePost
{
    /**
     * @var string
     */
    protected $name = 'post';

    /**
     * @var string
     */
    protected $model = 'Modules\Content\Models\Post';

    /**
     * @var string
     */
    protected $view = 'Modules/Content::Content/Post';

    /**
     * @var bool
     */
    protected $hidden = false;

    /**
     * @var bool
     */
    protected $public = true;

    /**
     * @var bool
     */
    protected $hierarchical = false;

    /**
     * @var bool
     */
    protected $hasArchive = true;

    /**
     * @var array
     */
    protected $rewrite = array(
        'slug' => 'posts'
    );


    /**
     * @return string
     */
    public function description()
    {
        return __d('content', 'A type of content for blogging, featuring categories, tags and comments.');
    }

    /**
     * @return array
     */
    public static function labels()
    {
        return array(
            'name'        => __d('content', 'Post'),
            'title'       => __d('content', 'Posts'),

            'searchItems' => __d('content', 'Search Posts'),
            'allItems'    => __d('content', 'All Posts'),
            'notFound'    => __d('content', 'No posts found'),

            'parentItem'      => null,
            'parentItemColon' => null,

            'addNew'      => __d('content', 'Add New'),
            'addNewItem'  => __d('content', 'Add New Post'),
            'editItem'    => __d('content', 'Edit Post'),
            'updateItem'  => __d('content', 'Update Post'),
            'deleteItem'  => __d('content', 'Delete Post'),
            'newItem'     => __d('content', 'New Post'),
            'viewItem'    => __d('content', 'View Post'),

            'menuName'    => __d('content', 'Posts'),
        );
    }
}
