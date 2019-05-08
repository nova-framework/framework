<?php

namespace Modules\Content\Platform\ContentTypes\Taxonomies;

use Modules\Content\Platform\ContentTypes\Taxonomy;


class Tag extends Taxonomy
{
    /**
     * @var string
     */
    protected $name = 'post_tag';

    /**
     * @var string
     */
    protected $model = 'Modules\Content\Models\Tag';

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
    protected $showInNavMenus = false;

    /**
     * @var bool
     */
    protected $hierarchical = false;

    /**
     * @var array
     */
    protected $rewrite = array(
        'item'  => 'tag',
        'items' => 'tags'
    );


    /**
     * @return string
     */
    public function description()
    {
        return __d('content', 'A type of non-hierarchical taxonomy.');
    }

    /**
     * @return array
     */
    public static function labels()
    {
        return array(
            'name'        => __d('content', 'Tag'),
            'title'       => __d('content', 'Tags'),

            'searchItems' => __d('content', 'Search Tags'),
            'allItems'    => __d('content', 'All Tag'),

            'parentItem'      => null,
            'parentItemColon' => null,

            'editItem'    => __d('content', 'Edit Tag'),
            'updateItem'  => __d('content', 'Update Tag'),
            'deleteItem'  => __d('content', 'Delete Tag'),
            'addNewItem'  => __d('content', 'Add New Tag'),
            'newItemName' => __d('content', 'New Tag Name'),

            'menuName'    => __d('content', 'Tags'),
        );
    }
}
