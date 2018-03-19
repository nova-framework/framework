<?php

namespace Modules\Content\Models;

use Modules\Content\Models\MenuItem;
use Modules\Content\Models\Taxonomy;


class Menu extends Taxonomy
{
    /**
     * @var string
     */
    protected $taxonomy = 'nav_menu';

    /**
     * @var array
     */
    protected $with = array('term', 'items');


    /**
     * @return \Nova\Database\ORM\Relations\BelongsToMany
     */
    public function items()
    {
        return $this->belongsToMany(
            'Modules\Content\Models\MenuItem', 'term_relationships', 'term_taxonomy_id', 'object_id'

        )->orderBy('menu_order');
    }
}
