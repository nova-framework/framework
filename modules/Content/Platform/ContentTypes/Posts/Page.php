<?php

namespace Modules\Content\Platform\ContentTypes\Posts;

use Modules\Content\Platform\ContentTypes\Post as BasePost;


class Page extends BasePost
{
    /**
     * @var string
     */
    protected $name = 'page';

    /**
     * @var string
     */
    protected $model = 'Modules\Content\Models\Page';

    /**
     * @var string
     */
    protected $view = 'Modules/Content::Content/Page';

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
    protected $showInMenu = true;

    /**
     * @var bool
     */
    protected $showInNavMenus = true;

    /**
     * @var bool
     */
    protected $hierarchical = true;

    /**
     * @var bool
     */
    protected $hasArchive = false;

    /**
     * @var array
     */
    protected $rewrite = array(
        'item'  => 'page',
        'items' => 'pages'
    );


    /**
     * @return string
     */
    public function description()
    {
        return __d('content', 'A stand-alone page, optionally with menu items.');
    }

    /**
     * @return array
     */
    public static function labels()
    {
        return array(
            'name'        => __d('content', 'Page'),
            'title'       => __d('content', 'Pages'),

            'searchItems' => __d('content', 'Search Pages'),
            'allItems'    => __d('content', 'All Pages'),
            'notFound'    => __d('content', 'No posts found'),

            'parentItem'      => __d('content', 'Parent Page'),
            'parentItemColon' => __d('content', 'Parent Page:'),

            'addNew'      => __d('content', 'Add New'),
            'addNewItem'  => __d('content', 'Add New Page'),
            'editItem'    => __d('content', 'Edit Page'),
            'updateItem'  => __d('content', 'Update Page'),
            'deleteItem'  => __d('content', 'Delete Page'),
            'newItem'     => __d('content', 'New Page'),
            'viewItem'    => __d('content', 'View Page'),

            'menuName'    => __d('content', 'Pages'),
        );
    }
}
