<?php

namespace Modules\Content\Models;

use Nova\Database\ORM\Model;

use Shared\MetaField\Models\MetaField as BaseModel;

use Modules\Content\Models\Post;
use Modules\Content\Models\Taxonomy;

use Exception;


class PostMeta extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'posts_meta';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = array('key', 'value', 'post_id');


    /**
     * @return \Nova\Database\ORM\Relations\BelongsTo
     */
    public function post()
    {
        return $this->belongsTo('Modules\Content\Models\Post', 'post_id');
    }
}
