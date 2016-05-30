<?php
/**
 * Application Configuration
 *
 * @author David Carr - dave@daveismyname.com
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

use Core\Config;

/**
 * Class Aliases configuration
 */
Config::set('classAliases', array(
    // The Core Tools
    'Config'        => '\Core\Config',
    'Errors'        => '\Core\Error',

    // The Helpers
    'Mail'          => '\Helpers\Mailer',
    'Assets'        => '\Helpers\Assets',
    'Csrf'          => '\Helpers\Csrf',
    'Date'          => '\Helpers\Date',
    'Document'      => '\Helpers\Document',
    'Encrypter'     => '\Helpers\Encrypter',
    'FastCache'     => '\Helpers\FastCache',
    'Form'          => '\Helpers\Form',
    'Ftp'           => '\Helpers\Ftp',
    'GeoCode'       => '\Helpers\GeoCode',
    'Hooks'         => '\Helpers\Hooks',
    'Inflector'     => '\Helpers\Inflector',
    'Number'        => '\Helpers\Number',
    'RainCaptcha'   => '\Helpers\RainCaptcha',
    'ReservedWords' => '\Helpers\ReservedWords',
    'SimpleCurl'    => '\Helpers\SimpleCurl',
    'TableBuilder'  => '\Helpers\TableBuilder',
    'Tags'          => '\Helpers\Tags',
    'Url'           => '\Helpers\Url',

    // Forensics
    'Console'       => '\Forensics\Console',

    // The Support Classes
    'Arr'           => '\Support\Arr',
    'Str'           => '\Support\Str',

    // The Support Facades
    'Auth'          => '\Support\Facades\Auth',
    'Cookie'        => '\Support\Facades\Cookie',
    'Crypt'         => '\Support\Facades\Crypt',
    'DB'            => '\Support\Facades\Database',
    'Event'         => '\Support\Facades\Event',
    'Hash'          => '\Support\Facades\Hash',
    'Input'         => '\Support\Facades\Input',
    'Language'      => '\Support\Facades\Language',
    'Mailer'        => '\Support\Facades\Mailer',
    'Paginator'     => '\Support\Facades\Paginator',
    'Password'      => '\Support\Facades\Password',
    'Redirect'      => '\Support\Facades\Redirect',
    'Request'       => '\Support\Facades\Request',
    'Response'      => '\Support\Facades\Response',
    'Session'       => '\Support\Facades\Session',
    'Validator'     => '\Support\Facades\Validator',
));
