<?php
namespace Helpers;

/**
 * Assets static helper.
 *
 * @author volter9
 * @author QsmaPL
 * @version 3.0
 */


class Assets
{
    /**
     * @var array Asset templates
     */
    protected static $templates = array(
        'js'  => '<script src="%s" type="text/javascript"></script>',
        'css' => '<link href="%s" rel="stylesheet" type="text/css">'
    );

    /**
     * Common templates for assets.
     *
     * @param string|array $files
     * @param string       $template
     */
    protected static function resource($files, $template)
    {
        $template = self::$templates[$template];

        if (is_array($files)) {
            foreach ($files as $file) {
                if (!empty($file)) {
                    echo sprintf($template, $file) . "\n";
                }
            }

            return;
        }

        if (!empty($files)) {
            echo sprintf($template, $files) . "\n";
        }
    }

    /**
     * Load js scripts.
     *
     * @param  string|array   $files      paths to file/s
     */
    public static function js($files)
    {
        static::resource($files, 'js');
    }

    /**
     * Load css scripts.
     *
     * @param  string|array  $files      paths to file/s
     */
    public static function css($files)
    {
        static::resource($files, 'css');
    }
}
