<?php

namespace App\Modules\Content\Models;

use Nova\Database\ORM\Model;

use App\Modules\Content\Models\Builder\TaxonomyBuilder;
use App\Modules\Content\Models\TermMeta;
use App\Modules\Content\Traits\HasMetaTrait;


class Taxonomy extends Model
{
    use HasMetaTrait;

    //
    protected $table = 'term_taxonomy';

    protected $primaryKey = 'id';

    /**
     * @var array
     */
    protected $fillable = array('term_id', 'taxonomy', 'description', 'parent_id');

    /**
     * @var array
     */
    protected $with = array('term', 'meta');

    /**
     * @var bool
     */
    public $timestamps = false;


    /**
     * @return \Nova\Database\ORM\Relations\HasMany
     */
    public function meta()
    {
        return $this->hasMany('App\Modules\Content\Models\TermMeta', 'term_id');
    }

    /**
     * @return \Nova\Database\ORM\Relations\BelongsTo
     */
    public function term()
    {
        return $this->belongsTo('App\Modules\Content\Models\Term', 'term_id');
    }

    /**
     * @return \Nova\Database\ORM\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo('App\Modules\Content\Models\Taxonomy', 'parent_id');
    }

    /**
     * @return \Nova\Database\ORM\Relations\BelongsTo
     */
    public function children()
    {
        return $this->hasMany('App\Modules\Content\Models\Taxonomy', 'parent_id');
    }

    /**
     * @return \Nova\Database\ORM\Relations\BelongsToMany
     */
    public function posts()
    {
        return $this->belongsToMany(
            'App\Modules\Content\Models\Post', 'term_relationships', 'term_taxonomy_id', 'object_id'
        );
    }

    /**
     * @param \Illuminate\Database\Query\Builder $query
     * @return TaxonomyBuilder
     */
    public function newQueryBuilder($query)
    {
        $builder = new TaxonomyBuilder($query);

        return isset($this->taxonomy) && ! empty($this->taxonomy)
            ? $builder->where('taxonomy', $this->taxonomy)
            : $builder;
    }

    /**
     * Update the count field.
     */
    public function updateCount()
    {
        $this->count = $this->posts()->count();

        $this->save();
    }

    /**
     * Magic method to return the meta data like the post original fields.
     *
     * @param string $key
     * @return string
     */
    public function __get($key)
    {
        if (! isset($this->$key) && isset($this->term->$key)) {
            return $this->term->$key;
        }

        return parent::__get($key);
    }
}
