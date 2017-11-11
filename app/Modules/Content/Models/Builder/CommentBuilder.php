<?php

namespace App\Modules\Content\Models\Builder;

use Nova\Database\ORM\Builder;


class CommentBuilder extends Builder
{

    /**
     * @return \App\Modules\Content\Models\Builder\CommentBuilder
     */
    public function approved()
    {
        return $this->where('approved', 1);
    }
}
