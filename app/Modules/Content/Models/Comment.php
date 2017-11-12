<?php

namespace App\Modules\Content\Models;

use Nova\Database\ORM\Model;

use App\Modules\Content\Models\Builder\CommentBuilder;
use App\Modules\Content\Traits\HasMetaTrait;


class Comment extends Model
{
    use HasMetaTrait;

    /**
     * @var string
     */
    protected $table = 'comments';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var array
     */
    protected $with = array('meta');


    /**
     * @return \Nova\Database\ORM\Relations\HasMany
     */
    public function meta()
    {
        return $this->hasMany('App\Modules\Content\Models\CommentMeta', 'comment_id');
    }

    /**
     * @return \Nova\Database\ORM\Relations\BelongsTo
     */
    public function post()
    {
        return $this->belongsTo('App\Modules\Content\Models\Comment', 'post_id');
    }

    /**
     * @return \Nova\Database\ORM\Relations\BelongsTo
     */
    public function original()
    {
        return $this->belongsTo('App\Modules\Content\Models\Comment', 'parent_id');
    }

    /**
     * @return \Nova\Database\ORM\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->original();
    }

    /**
     * @return \Nova\Database\ORM\Relations\HasMany
     */
    public function replies()
    {
        return $this->hasMany('App\Modules\Content\Models\Comment', 'parent_id');
    }

    /**
     * @return bool
     */
    public function isApproved()
    {
        return $this->attributes['approved'] == 1;
    }

    /**
     * @return bool
     */
    public function isReply()
    {
        return $this->attributes['parent_id'] > 0;
    }

    /**
     * @return bool
     */
    public function hasReplies()
    {
        return $this->replies->count() > 0;
    }

    /**
     * @param \Illuminate\Database\Query\Builder $query
     * @return CommentBuilder
     */
    public function newQueryBuilder($query)
    {
        return new CommentBuilder($query);
    }

    /**
     * Find a comment by post ID.
     *
     * @param int $postId
     * @return Comment
     */
    public static function findByPostId($postId)
    {
        return with(new static())->where('post_id', $postId)->get();
    }
}
