<?php

namespace Modules\Content\Traits;

use Modules\Content\Fields\CustomFields;


trait HasFieldsTrait
{
    /**
     * @return \Modules\Content\Fields\CustomFields
     */
    public function getFieldsAttribute()
    {
        return new CustomFields($this);
    }
}
