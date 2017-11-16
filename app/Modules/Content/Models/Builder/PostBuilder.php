<?php

namespace App\Modules\Content\Models\Builder;

use Nova\Database\ORM\Builder;


class PostBuilder extends Builder
{

    /**
     * @param string $status
     * @return PostBuilder
     */
    public function status($status)
    {
        return $this->where('status', $status);
    }

    /**
     * @return PostBuilder
     */
    public function published()
    {
        return $this->status('publish');
    }

    /**
     * @param string $type
     * @return PostBuilder
     */
    public function type($type)
    {
        return $this->where('type', $type);
    }

    /**
     * @param array $types
     * @return PostBuilder
     */
    public function typeIn(array $types)
    {
        return $this->whereIn('type', $types);
    }

    /**
     * @param string $slug
     * @return PostBuilder
     */
    public function slug($slug)
    {
        return $this->where('name', $slug);
    }
}
