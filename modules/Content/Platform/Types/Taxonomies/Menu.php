<?php

namespace Modules\Content\Platform\Types\Taxonomies;

use Modules\Content\Platform\Types\Taxonomy;


class Menu extends Taxonomy
{
    /**
     * @var string
     */
    protected $name = 'tag';

    /**
     * @var string
     */
    protected $model = 'Modules\Content\Models\Menu';

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
     * @var array
     */
    protected $rewrite = array(
        'slug' => 'tags'
    );


    /**
     * @return string
     */
    public function description()
    {
        return __d('content', 'A taxonomy type which represents a menu.');
    }

    /**
     * @return array
     */
    public function labels()
    {
        return array(
            'name'        => __d('content', 'Menu'),
            'title'       => __d('content', 'Menus'),

            'searchItems' => __d('content', 'Search Menus'),
            'allItems'    => __d('content', 'All Menu'),

            'parentItem'      => null,
            'parentItemColon' => null,

            'editItem'    => __d('content', 'Edit Menu'),
            'updateItem'  => __d('content', 'Update Menu'),
            'deleteItem'  => __d('content', 'Delete Menu'),
            'addNewItem'  => __d('content', 'Add New Menu'),
            'newItemName' => __d('content', 'New Menu Name'),

            'menuName'    => __d('content', 'Menus'),
        );
    }
}

