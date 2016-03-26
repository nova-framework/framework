<?php
namespace Helpers;

/**
 * Assets static helper
 *
 * @author volter9
 * @author QsmaPL
 * @version 3.0
 */

use Helpers\Url;
use Helpers\JsMin;
use Helpers\Response;
use Helpers\Inflector;

class Assets
{
    /**
     * @var array Asset templates
     */
    protected static $templates = array
    (
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
        } else {
            if (!empty($files)) {
                echo sprintf($template, $files) . "\n";
            }
        }
    }

    /**
     * load js scripts
     * @param  String|Array  $files      paths to file/s
     * @param  boolean       $cache      if set to true a cache will be created and serverd
     * @param  boolean       $refresh    if true the cache will be updated
     * @param  string        $cachedMins minutes to hold the cache
     */
    public static function js($files, $cache = false, $refresh = false, $cachedMins = '1440')
    {
        $path = APPDIR.Url::relativeTemplatePath().'js/compressed.min.js';
        $type = 'js';

        if ($cache == false) {
            static::resource($files, $type);
        } else {
            if ($refresh == false && file_exists($path) && (filemtime($path) > (time() - 60 * $cachedMins))) {

                $path = str_replace(APPDIR, null, $path);
                $path = Inflector::tableize($path);

                static::resource(DIR.$path, $type);
            } else {
                $source = static::collect($files, $type);
                $source = JsMin::minify($source);
                file_put_contents($path, $source);

                $path = str_replace(APPDIR, null, $path);
                $path = Inflector::tableize($path);

                static::resource(DIR.$path, $type);
            }
        }
    }

    /**
     * load css scripts
     * @param  String|Array  $files      paths to file/s
     * @param  boolean       $cache      if set to true a cache will be created and serverd
     * @param  boolean       $refresh    if true the cache will be updated
     * @param  string        $cachedMins minutes to hold the cache
     */
    public static function css($files, $cache = false, $refresh = false, $cachedMins = '1440')
    {
        $path = APPDIR.Url::relativeTemplatePath().'css/compressed.min.css';
        $type = 'css';

        if ($cache == false) {
            static::resource($files, $type);
        } else {
            if ($refresh == false && file_exists($path) && (filemtime($path) > (time() - 60 * $cachedMins))) {
                $path = str_replace(APPDIR, null, $path);

                $path = Inflector::tableize($path);
                static::resource(DIR.$path, $type);
            } else {
                $source = static::collect($files, $type);
                $source = static::compress($source);
                file_put_contents($path, $source);

                $path = str_replace(APPDIR, null, $path);
                $path = Inflector::tableize($path);

                static::resource(DIR.$path, $type);
            }
        }
    }

    private static function collect($files, $type)
    {
        $content = null;
        if (is_array($files)) {
            foreach ($files as $file) {
                if (!empty($file)) {
                    if (strpos(basename($file), '.min.') === false && $type == 'css') {
                        //compress files that aren't minified
                        $content.= static::compress(file_get_contents($file));

                    } else {
                        $file = str_replace(APPDIR, null, $file);
                        $content.= file_get_contents($file);
                    }
                }
            }
        } else {
            if (!empty($files)) {
                if (strpos(basename($files), '.min.') === false && $type == 'css') {
                    //compress files that aren't minified
                    $content.= static::compress(file_get_contents($files));
                } else {
                    $content.= file_get_contents($files);
                }
            }
        }

        return $content;
    }

    private static function compress($buffer)
    {
        /* remove comments */
        $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
        /* remove tabs, spaces, newlines, etc. */
        $buffer = str_replace(array("\r\n","\r","\n","\t",'  ','    ','     '), '', $buffer);
        /* remove other spaces before/after ; */
        $buffer = preg_replace(array('(( )+{)','({( )+)'), '{', $buffer);
        $buffer = preg_replace(array('(( )+})','(}( )+)','(;( )*})'), '}', $buffer);
        $buffer = preg_replace(array('(;( )+)','(( )+;)'), ';', $buffer);
        return $buffer;
    }
}
