<?php

namespace Modules\Content\Platform\Types\Posts;

use Modules\Content\Platform\Types\Post as BasePost;


class MenuItem extends BasePost
{
    /**
     * @var string
     */
    protected $name = 'menuItem';

    /**
     * @var string
     */
    protected $model = 'Modules\Content\Models\MenuItem';

    /**
     * @var string
     */
    protected $view = 'Modules/Content::Content/MenuItem';

    /**
     * @var bool
     */
    protected $hidden = true;

    /**
     * @var bool
     */
    protected $public = true;

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
        'slug' => 'menu-items'
    );


    /**
     * @return string
     */
    public function description()
    {
        return __d('content', 'A menu item representing a link to one of the site pages.');
    }

    /**
     * @return array
     */
    public function labels()
    {
        return array(
            'item'        => __d('content', 'Menu Item'),
            'items'       => __d('content', 'Menu Items'),

            'searchItems' => __d('content', 'Search Menu Items'),
            'allItems'    => __d('content', 'All Menu Items'),
            'notFound'    => __d('content', 'No menu items found'),

            'parentItem'      => __d('content', 'Parent Menu Item'),
            'parentItemColon' => __d('content', 'Parent Menu Item:'),

            'addNew'      => __d('content', 'Add New'),
            'addNewItem'  => __d('content', 'Add New Menu Item'),
            'editItem'    => __d('content', 'Edit Menu Item'),
            'updateItem'  => __d('content', 'Update Menu Item'),
            'deleteItem'  => __d('content', 'Delete Menu Item'),
            'newItem'     => __d('content', 'New Menu Item'),
            'viewItem'    => __d('content', 'View Menu Item'),

            'menuName'    => __d('content', 'Menu Items'),
        );
    }
}
