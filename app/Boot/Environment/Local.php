<?php

use Nova\Config\Config;


Config::set('app.url', 'http://www.novastable.dev');

/**
 * Setup the Profiler configuration
 */
Config::set('profiler', array(
    'useForensics' => false,
    'withDatabase' => true,
));

