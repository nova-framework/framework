<?php
namespace Core;

use Core\Error;

/*
 * Language - simple language handler
 *
 * @author Bartek Kuśmierczuk - contact@qsma.pl - http://qsma.pl
 * @version 2.2
 * @date November 18, 2014
 * @date updated May 18 2015
 * @author Enner Pérez - ennerperez@gmail.com
 * @date updated Jun 29 2015
 */
class Language
{
    /**
     * Variable holds array with language
     * @var array
     */
    private $array;

    /**
     * Load language function
     * @param  string $name
     * @param  string $code
     */
    public function load($name, $code = LANGUAGE_CODE)
    {
        // lang file
        $file = "app/Language/$code/$name.php";

        // check if is readable
        if (is_readable($file)) {
            // require file
            $this->array = include($file);
        } else {
            // display error
            echo Error::display("Could not load language file '$code/$name.php'");
            die;
        }
    }

    /**
     * Get element from language array by key
     * @param  string $value
     * @return string
     */
    public function get($value, $index = NULL)
    {
        if (!empty($this->array[$value])) {
            if (!isset($index)){
                return $this->array[$value];
            }
            else {
                $return =$this->array[$value];
                return $return[$index];
            }
        } else {
            return $value;
        }
    }

    /**
     * Get lang for views
     * @param  string $value this is "word" value from language file
     * @param  string $name  name of file with language
     * @param  string $code  optional, language code
     * @return string
     */
    public static function show($value, $name, $code = LANGUAGE_CODE)
    {
        // lang file
        $file = "app/Language/$code/$name.php";

        // check if is readable
        if (is_readable($file)) {
            // require file
            $array = include($file);
        } else {
            // display error
            echo Error::display("Could not load language file '$code/$name.php'");
            die;
        }

        // If
        if (!empty($array[$value])) {
            return $array[$value];
        } else {
            return $value;
        }
    }
}
