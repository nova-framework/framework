<?php

namespace App\Modules\Content\Models;

use Nova\Database\ORM\Model;
use Nova\Support\Str;

use App\Modules\Content\Traits\HasMetaTrait;


class Term extends Model
{
    use HasMetaTrait;

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
        return $this->hasMany('App\Modules\Content\Models\TermMeta', 'term_id');
    }

    /**
     * @return \Nova\Database\ORM\Relations\HasOne
     */
    public function taxonomy()
    {
        return $this->hasOne('App\Modules\Content\Models\Taxonomy', 'term_id');
    }

    public static function uniqueSlug($name, $taxonomy)
    {
        $count = 0;

        $segments = explode('-', Str::slug($name));

        if ((count($segments) > 1) && is_integer(end($segments))) {
            $count = (int) array_pop($segments);
        }

        $name = implode('-', $segments);

        // Compute an unique slug.
        $slugs = static::whereHas('taxonomy', function ($query) use ($taxonomy)
        {
            $query->where('taxonomy', $taxonomy);

        })->lists('slug');

        do {
            $slug = $name;

            if ($count > 0) $slug .= '-' .$count;

            $count++;
        }
        while (in_array($slug, $slugs));

        return $slug;
    }
}
