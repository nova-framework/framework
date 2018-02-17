<?php

namespace Modules\Content\Traits;

use Modules\Content\Fields\CustomFields;


/**
 * Trait HasAcfFields
 *
 * @package Corcel\Traits
 * @author Junior Grossi <juniorgro@gmail.com>
 */
trait HasAcfFieldsTrait
{
    /**
     * @return AdvancedCustomFields
     */
    public function getAcfAttribute()
    {
        return new CustomFields($this);
    }
}
