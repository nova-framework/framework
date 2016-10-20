<?php

use Nova\Config\Config;

/**
 * Setup the Profiler configuration
 */
Config::set('profiler', array(
    'useForensics' => false,
    'withDatabase' => true,
));

