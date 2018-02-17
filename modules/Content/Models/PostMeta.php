<?php

namespace Modules\Content\Models;

use Nova\Database\ORM\Model;

use Modules\Content\Models\Collection\MetaCollection;
use Modules\Content\Models\Post;
use Modules\Content\Models\Taxonomy;

use Exception;


class PostMeta extends Model
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

    /**
     * @param string $primary
     * @param string $where
     * @return \Nova\Database\ORM\Relations\Relation
     * @todo test
     */
    public function taxonomy($primary = null, $where = null)
    {
        if (! is_null($primary) && ! empty($primary)) {
            $this->primaryKey = $primary;
        }

        $relation = $this->hasOne('Modules\Content\Models\Taxonomy', 'id');

        if (! is_null($where) && ! empty($where)) {
            $relation->where($where, $this->value);
        }

        return $relation;
    }

    /**
     * @param  mixed  $value
     * @return mixed
     */
    public function getValueAttribute($value)
    {
        if (! is_string($value)) {
            return $value;
        }

        try {
            $result = unserialize($value);

            if (($result === false) && ($value !== false)) {
                return $value;
            }

            return $result;
        }
        catch (Exception $e) {
            return $value;
        }
    }

    /**
     * @param  mixed  $value
     * @return void
     */
    public function setValueAttribute($value)
    {
        if (is_array($value) || is_object($value)) {
            $value = serialize($value);
        }

        // When the value is a string containing serialized data, we should serialize it again.
        else if (is_string($value) && preg_match("#^((N;)|((a|O|s):[0-9]+:.*[;}])|((b|i|d):[0-9.E-]+;))$#um", $value)) {
            $value = serialize($value);
        }

        $this->attributes['value'] = $value;
    }

    /**
     * Create a new ORM Collection instance.
     *
     * @param  array  $models
     * @return \Modules\Content\Models\Collection\MetaCollection
     */
    public function newCollection(array $models = array())
    {
        return new MetaCollection($models);
    }
}
