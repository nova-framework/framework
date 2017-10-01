<?php

use Nova\Http\Request;

use App\Models\Option;


/**
 * Load The Options
 */
if (CONFIG_STORE === 'database') {
    // Retrieve the Option items, caching them for 24 hours.
    $options = Cache::remember('system_options', 1440, function ()
    {
        return Option::getResults();
    });

    foreach ($options as $option) {
        list ($key, $value) = $option->getConfigItem();

        Config::set($key, $value);
    }
}

// If the CONFIG_STORE is not in 'files' mode, go Exception.
else if(CONFIG_STORE !== 'files') {
    throw new InvalidArgumentException('Invalid Config Store type.');
}

/**
 * Listener Closure to the Event 'router.matched'.
 */
Event::listen('router.matched', function ($route, Request $request)
{
    // Share the Views the current URI.
    View::share('currentUri', $request->path());
});
