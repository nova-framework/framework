<?php
/**
 * Class Aliases configuration
 *
 * @author David Carr - dave@daveismyname.com
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 */

use Core\Config;

Config::set('classAliases', array(
    'Config'        => '\Core\Config',
    'Errors'        => '\Core\Error',
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
    'Password'      => '\Helpers\Password',
    'RainCaptcha'   => '\Helpers\RainCaptcha',
    'ReservedWords' => '\Helpers\ReservedWords',
    'SimpleCurl'    => '\Helpers\SimpleCurl',
    'TableBuilder'  => '\Helpers\TableBuilder',
    'Tags'          => '\Helpers\Tags',
    'Url'           => '\Helpers\Url',
    // The Support Classes
    'Arr'           => '\Support\Arr',
    'Str'           => '\Support\Str',
    // The Support Facades
    'Auth'          => '\Support\Facades\Auth',
    'Cookie'        => '\Support\Facades\Cookie',
    'Crypt'         => '\Support\Facades\Crypt',
    'DB'            => '\Support\Facades\Database',
    'Event'         => '\Support\Facades\Event',
    'Input'         => '\Support\Facades\Input',
    'Language'      => '\Support\Facades\Language',
    'Paginator'     => '\Support\Facades\Paginator',
    'Redirect'      => '\Support\Facades\Redirect',
    'Request'       => '\Support\Facades\Request',
    'Response'      => '\Support\Facades\Response',
    'Session'       => '\Support\Facades\Session',
    'Validator'     => '\Support\Facades\Validator',
));
