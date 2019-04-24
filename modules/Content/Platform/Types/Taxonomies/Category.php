<?php

namespace Modules\Content\Platform\Types\Taxonomies;

use Modules\Content\Platform\Types\Taxonomy;


class Category extends Taxonomy
{
    /**
     * @var string
     */
    protected $name = 'category';

    /**
     * @var string
     */
    protected $model = 'Modules\Content\Models\Taxonomy';

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
    protected $showInMenu = false;

    /**
     * @var bool
     */
    protected $showInNavMenus = true;

    /**
     * @var bool
     */
    protected $hierarchical = true;

    /**
     * @var array
     */
    protected $rewrite = array(
        'item'  => 'category',
        'items' => 'categories'
    );


    /**
     * @return string
     */
    public function description()
    {
        return __d('content', 'A type of hierarchical taxonomy.');
    }

    /**
     * @return array
     */
    public static function labels()
    {
        return array(
            'name'        => __d('content', 'Category'),
            'title'       => __d('content', 'Categories'),

            'searchItems' => __d('content', 'Search Categories'),
            'allItems'    => __d('content', 'All Category'),

            'parentItem'      => __d('content', 'Parent Category'),
            'parentItemColon' => __d('content', 'Parent Category:'),

            'editItem'    => __d('content', 'Edit Category'),
            'updateItem'  => __d('content', 'Update Category'),
            'deleteItem'  => __d('content', 'Delete Category'),
            'addNewItem'  => __d('content', 'Add New Category'),
            'newItemName' => __d('content', 'New Category Name'),

            'menuName'    => __d('content', 'Categories'),
        );
    }
}
