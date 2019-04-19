<?php

namespace Modules\Content\Models;

use Nova\Database\ORM\Model;
use Nova\Support\Str;

use Shared\MetaField\HasMetaFieldsTrait;


class Term extends Model
{
    use HasMetaFieldsTrait;

    //
    protected $table = 'terms';

    protected $primaryKey = 'id';

    /**
     * @var array
     */
    protected $fillable = array('name', 'slug', 'group');

    /**
     * @var array
     */
    protected $with = array('meta');

    /**
     * @var bool
     */
    public $timestamps = false;


    /**
     * @return \Nova\Database\ORM\Relations\HasMany
     */
    public function meta()
    {
        return $this->hasMany('Modules\Content\Models\TermMeta', 'term_id');
    }

    /**
     * @return \Nova\Database\ORM\Relations\HasOne
     */
    public function taxonomy()
    {
        return $this->hasOne('Modules\Content\Models\Taxonomy', 'term_id');
    }

    public static function uniqueSlug($name, $taxonomy, $id = null)
    {
        $query = static::whereHas('taxonomy', function ($query) use ($taxonomy)
        {
            $query->where('taxonomy', $taxonomy);
        });

        if (! is_null($id)) {
            $query->where('id', '!=', (int) $id);
        }

        $slugs = $query->lists('slug');

        if (! in_array($slug = Str::slug($name), $slugs)) {
            // The slug is unique, then no further processing is required.
            return $slug;
        }

        $count = 0;

        if (count($segments = explode('-', $slug)) > 1) {
            $last = end($segments);

            if (is_integer($last)) {
                $count = (int) array_pop($segments);

                $slug = implode('-', $segments);
            }
        }

        do {
            $value = ($count === 0) ? $slug : $slug .'-' .$count;

            $count++;
        }
        while (in_array($value, $slugs));

        return $value;
    }
}
