<?php

namespace App\Modules\Fields\Support\Facades;

use Nova\Support\Facades\Facade;

use App\Modules\Fields\Types\Registry;


/**
 * @see \App\Modules\Fields\Support\FieldRegistry
 */
class FieldTypeRegistry extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return Registry::class; }

}
