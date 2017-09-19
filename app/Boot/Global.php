<?php

//--------------------------------------------------------------------------
// Load The Options
//--------------------------------------------------------------------------

use Nova\Database\QueryException;

use App\Models\Option;


if (CONFIG_STORE === 'database') {
    // Retrieve the Option items, caching them for 24 hours.
    $options = Cache::remember('system_options', 1440, function ()
    {
        try {
            return Option::all();
        }
        catch (QueryException $e) {
            //
        }
        catch (PDOException $e) {
            //
        }

        return collect();
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

//--------------------------------------------------------------------------
// Boot Stage Customization
//--------------------------------------------------------------------------
