<?php

namespace App\Modules\Content\Models;

use App\Modules\Content\Models\Taxonomy;


class Tag extends Taxonomy
{
    /**
     * @var string
     */
    protected $taxonomy = 'tag';
}
