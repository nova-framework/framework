<?php
/**
 * Alias - make helpers available in views
 *
 * @author David Carr - dave@novaframework.com
 * @version 3.0
 */
namespace Core;

/**
 * Alias - make alias for classes for views to use without delcaring a use element.
 */
class Alias
{
    public function __construct()
    {
        $classes = $this->load();
        foreach ($classes as $key => $value) {
            class_alias($key, $value);
        }
    }

    private function load()
    {
        return [
            '\Core\\Error'  => 'Errors',
            '\Core\\Language'  => 'Language',
            '\Helpers\\PhpMailer\\Mail'  => 'Mail',
            '\Helpers\\Assets'  => 'Assets',
            '\Helpers\\Arr'  => 'Arr',
            '\Helpers\\Cookie'   => 'Cookie',
            '\Helpers\\Csrf'   => 'Csrf',
            '\Helpers\\Data'   => 'Data',
            '\Helpers\\Date'   => 'Date',
            '\Helpers\\Document'   => 'Document',
            '\Helpers\\Form'   => 'Form',
            '\Helpers\\Ftp'   => 'Ftp',
            '\Helpers\\GeoCode'   => 'GeoCode',
            '\Helpers\\Hooks' => 'Hooks',
            '\Helpers\\Inflector' => 'Inflector',
            '\Helpers\\Number' => 'Number',
            '\Helpers\\Paginator' => 'Paginator',
            '\Helpers\\Password' => 'Password',
            '\Helpers\\RainCaptcha' => 'RainCaptcha',
            '\Helpers\\Request' => 'Request',
            '\Helpers\\ReservedWords' => 'ReservedWords',
            '\Helpers\\Response' => 'Response',
            '\Helpers\\Session' => 'Session',
            '\Helpers\\SimpleCurl' => 'SimpleCurl',
            '\Helpers\\TableBuilder' => 'TableBuilder',
            '\Helpers\\Tags' => 'Tags',
            '\Helpers\\Url' => 'Url'
        ];
    }
}
