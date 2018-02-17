<?php

namespace Modules\Content\Models;

use Modules\Content\Models\PostMeta;


class CommentMeta extends PostMeta
{
    /**
     * @var string
     */
    protected $table = 'comments_meta';

    /**
     * @var string
     */
    protected $primaryKey = 'id';


    /**
     * @return \Nova\Database\ORM\Relations\BelongsTo
     */
    public function comment()
    {
        return $this->belongsTo('Modules\Content\Models\Comment', 'comment_id');
    }
}
