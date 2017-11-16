<?php

namespace App\Modules\Content\Models;

use App\Modules\Content\Models\PostMeta;


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
        return $this->belongsTo('App\Modules\Content\Models\Comment', 'comment_id');
    }
}
