<?php

namespace App\Modules\Fields\Models\Builder;

use Nova\Database\ORM\Builder as BaseBuilder;


class MetaBuilder extends BaseBuilder
{

    public function whereMeta($key, $operator, $value = null)
    {
        if (func_num_args() == 2) {
            list ($value, $operator) = array($operator, '=');
        }

        return $this->whereHas('meta', function ($query) use ($key, $operator, $value)
        {
            return $query->where('key', $key)->where('value', $operator, $value);
        });
    }

    public function orWhereMeta($key, $operator, $value = null)
    {
        if (func_num_args() == 2) {
            list ($value, $operator) = array($operator, '=');
        }

        return $this->orWhereHas('meta', function ($query) use ($key, $operator, $value)
        {
            return $query->where('key', $key)->where('value', $operator, $value);
        });
    }
}
