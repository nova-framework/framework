<?php
namespace Helpers;

/*
 * Hooks controller
 *
 * @author David Carr - dave@simplemvcframework.com
 * @version 2.2
 * @date updated May 18 2015
 */

class Hooks
{

    private static $plugins = array();
    private static $hooks = array();
    private static $instances = array();

    /**
     * initial hooks
     * @param  integer $id
     * @return $instance
     */
    public static function get($id = 0)
    {
        // return if instance exists
        if (isset(self::$instances[$id])) {
            return self::$instances[$id];
        }

        //define hooks
        self::setHooks(array(
            'meta',
            'css',
            'afterBody',
            'footer',
            'js',
            'routes'
        ));

        //load modules
        self::loadPlugins('app/Modules/');
        $instance = new self();
        self::$instances[$id] = $instance;
        return $instance;

    }

    //adds hook to hook list
    public static function setHook($where)
    {
        self::$hooks[$where] = '';
    }

    //add multiple hooks
    public static function setHooks($where)
    {
        foreach ($where as $where) {
            self::setHook($where);
        }
    }

    public static function loadPlugins($fromFolder)
    {
        if ($handle = opendir($fromFolder)) {
            while ($file = readdir($handle)) {
                if (is_file($fromFolder.$file)) {
                    require_once $fromFolder . $file;
                    self::$plugins [$file] ['file'] = $file;
                } elseif ((is_dir($fromFolder.$file)) && ($file != '.') && ($file != '..')) {
                    self::loadPlugins($fromFolder.$file.'/');
                }
            }
            closedir($handle);
        }
    }

    //attach custom function to hook
    public static function addHook($where, $function)
    {
        if (!isset(self::$hooks[$where])) {
            die("There is no such place ($where) for hooks.");
        } else {
            $theseHooks = explode('|', self::$hooks[$where]);
            $theseHooks[] = $function;
            self::$hooks[$where] = implode('|', $theseHooks);

        }
    }

    public function run($where, $args = '')
    {
        if (isset(self::$hooks[$where])) {
            $theseHooks = explode('|', self::$hooks[$where]);
            $result = $args;

            foreach ($theseHooks as $hook) {
                if (preg_match("/@/i", $hook)) {
                    //grab all parts based on a / separator
                    $parts = explode('/', $hook);

                    //collect the last index of the array
                    $last = end($parts);

                    //grab the controller name and method call
                    $segments = explode('@', $last);

                    $classname = new $segments[0]();
                    $result = call_user_func(array($classname, $segments[1]), $result);

                } else {
                    if (function_exists($hook)) {
                        $result = call_user_func($hook, $result);
                    }
                }
            }

            return $result;
        } else {
            die("There is no such place ($where) for hooks.");
        }
    }

    public function collectHook($where, $args = null)
    {
        ob_start();
            echo $this->run($where, $args);
        return ob_get_clean();
    }
}
