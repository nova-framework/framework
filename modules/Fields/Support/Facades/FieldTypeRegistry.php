<?php

namespace Modules\Fields\Support\Facades;

use Nova\Support\Facades\Facade;

use Modules\Fields\Types\Registry;


/**
 * @see \Modules\Fields\Support\FieldRegistry
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
