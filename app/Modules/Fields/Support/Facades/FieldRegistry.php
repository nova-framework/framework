<?php

namespace Nova\Support\Facades;

use App\Modules\Fields\Support\FieldRegistry;


/**
 * @see \App\Modules\Fields\Support\FieldRegistry
 */
class FieldRegistry extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return FieldRegistry::class; }

}
