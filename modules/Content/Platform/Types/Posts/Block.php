<?php

namespace Modules\Content\Platform\Types\Posts;

use Modules\Content\Platform\Types\Post as BasePost;


class Block extends BasePost
{
    /**
     * @var string
     */
    protected $name = 'block';

    /**
     * @var string
     */
    protected $model = 'Modules\Content\Models\Block';

    /**
     * @var string
     */
    protected $view =  'Modules/Content::Content/Block';

    /**
     * @var bool
     */
    protected $hidden = false;

    /**
     * @var bool
     */
    protected $public = false;

    /**
     * @var bool
     */
    protected $hierarchical = false;

    /**
     * @var bool
     */
    protected $hasArchive = false;

    /**
     * @var array
     */
    protected $rewrite = array(
        'item'  => 'block',
        'items' => 'blocks'
    );


    /**
     * @return string
     */
    public function description()
    {
        return __d('content', 'A content element which is displayed in one of the Widget Positions.');
    }

    /**
     * @return array
     */
    public static function labels()
    {
        return array(
            'name'        => __d('content', 'Block'),
            'title'       => __d('content', 'Blocks'),

            'searchItems' => __d('content', 'Search Blocks'),
            'allItems'    => __d('content', 'All Blocks'),
            'notFound'    => __d('content', 'No blocks found'),

            'parentItem'      => null,
            'parentItemColon' => null,

            'addNew'      => __d('content', 'Add New'),
            'addNewItem'  => __d('content', 'Add New Block'),
            'editItem'    => __d('content', 'Edit Block'),
            'updateItem'  => __d('content', 'Update Block'),
            'deleteItem'  => __d('content', 'Delete Block'),
            'newItem'     => __d('content', 'New Block'),
            'viewItem'    => __d('content', 'View Block'),

            'menuName'    => __d('content', 'Blocks'),
        );
    }
}
