<?php

namespace Modules\Content\Models;

use Nova\Database\ORM\Builder;


class CommentBuilder extends Builder
{

    /**
     * @return \Modules\Content\Models\CommentBuilder
     */
    public function approved()
    {
        return $this->where('approved', 1);
    }
}
