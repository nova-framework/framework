<?php

use App\Models\Option;


//--------------------------------------------------------------------------
// Load The Options
//--------------------------------------------------------------------------

if (CONFIG_STORE === 'database') {

    // Retrieve the Option items, caching them for 24 hours.
    $options = Cache::remember('system_options', 1440, function ()
    {
        return Option::all();
    });

    // Setup the information stored on the Option instances into Configuration.
    foreach ($options as $option) {
        $key = $option->getConfigKey();

        Config::set($key, $option->value);
    }
}

// If the CONFIG_STORE is not in 'files' mode, go Exception.
else if(CONFIG_STORE !== 'files') {
    throw new InvalidArgumentException('Invalid Config Store type.');
}

//--------------------------------------------------------------------------
// Boot Stage Customization
//--------------------------------------------------------------------------
