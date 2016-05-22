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
    'Response'      => '\Core\Response',
    'Redirect'      => '\Core\Redirect',
    'Mail'          => '\Helpers\Mailer',
    'Assets'        => '\Helpers\Assets',
    'Arr'           => '\Helpers\Arr',
    'Cookie'        => '\Helpers\Cookie',
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
    'Paginator'     => '\Helpers\Paginator',
    'Password'      => '\Helpers\Password',
    'RainCaptcha'   => '\Helpers\RainCaptcha',
    'ReservedWords' => '\Helpers\ReservedWords',
    'SimpleCurl'    => '\Helpers\SimpleCurl',
    'TableBuilder'  => '\Helpers\TableBuilder',
    'Tags'          => '\Helpers\Tags',
    'Url'           => '\Helpers\Url',
    // The Support Facades
    'Auth'          => '\Support\Facades\Auth',
    'Crypt'         => '\Support\Facades\Crypt',
    'DB'            => '\Support\Facades\Database',
    'Event'         => '\Support\Facades\Event',
    'Input'         => '\Support\Facades\Input',
    'Language'      => '\Support\Facades\Language',
    'Request'       => '\Support\Facades\Request',
    'Session'       => '\Support\Facades\Session',
    'Validator'     => '\Support\Facades\Validator',
    // The Legacy Mailer
    'Helpers\PhpMailer\Mail' => '\Helpers\Mailer',
));
