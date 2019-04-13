<?php

namespace Modules\Content\Models;

use Modules\Content\Models\Taxonomy;


class Tag extends Taxonomy
{
    /**
     * @var string
     */
    protected $taxonomy = 'post_tag';
}
