<?php
/**
 * Config - the Module's specific Configuration.
 *
 * @author David Carr - dave@daveismyname.com
 * @author Edwin Hoksberg - info@edwinhoksberg.nl
 * @version 3.0
 */


Config::set('cron', array(
    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    | This page can be used to generate key - http://novaframework.com/token-generator
    |
    */
    'token' => 'SomeRandomStringThere_1234567890',
));
