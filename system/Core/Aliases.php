<?php
/**
 * Alias - make helpers available in views
 *
 * @author David Carr - dave@novaframework.com
 * @version 3.0
 */
namespace Core;

/**
 * Aliases - make alias for classes for views to use without delcaring a use element.
 */
class Aliases
{
    public static function init()
    {
        $classes = self::load();

        foreach ($classes as $classAlias => $className) {
            class_alias($className, $classAlias);
        }
    }

    private static function load()
    {
        return [
            'Errors'        => '\Core\Error',
            'Language'      => '\Core\Language',
            'Mail'          => '\Helpers\PhpMailer\Mail',
            'Assets'        => '\Helpers\Assets',
            'Arr'           => '\Helpers\Arr',
            'Cookie'        => '\Helpers\Cookie',
            'Csrf'          => '\Helpers\Csrf',
            'Data'          => '\Helpers\Data',
            'Date'          => '\Helpers\Date',
            'Document'      => '\Helpers\Document',
            'Form'          => '\Helpers\Form',
            'Ftp'           => '\Helpers\Ftp',
            'GeoCode'       => '\Helpers\GeoCode',
            'Hooks'         => '\Helpers\Hooks',
            'Inflector'     => '\Helpers\Inflector',
            'Number'        => '\Helpers\Number',
            'Paginator'     => '\Helpers\Paginator',
            'Password'      => '\Helpers\Password',
            'RainCaptcha'   => '\Helpers\RainCaptcha',
            'Request'       => '\Helpers\Request',
            'ReservedWords' => '\Helpers\ReservedWords',
            'Response'      => '\Helpers\Response',
            'Session'       => '\Helpers\Session',
            'SimpleCurl'    => '\Helpers\SimpleCurl',
            'TableBuilder'  => '\Helpers\TableBuilder',
            'Tags'          => '\Helpers\Tags',
            'Url'           => '\Helpers\Url'
        ];
    }
}
