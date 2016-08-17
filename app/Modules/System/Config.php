<?php
/**
 * Config - the Module's specific Configuration.
 *
 * @author David Carr - dave@daveismyname.com
 * @author Edwin Hoksberg - info@edwinhoksberg.nl
 * @version 3.0
 */

use Core\Config;

/**
 * Configuration constants and options.
 */
Config::set('cron', array(
    /**
     * The CRON token.
     * This tool can be used to generate key - http://jeffreybarke.net/tools/codeigniter-encryption-key-generator
     */
    'token' => 'SomeRandomStringThere_1234567890',
));
