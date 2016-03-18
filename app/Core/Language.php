<?php
/**
 * Language - simple language handler.
 *
 * @author Bartek Kuśmierczuk - contact@qsma.pl - http://qsma.pl
 *
 * @version 2.2
 * @date November 18, 2014
 * @date updated Sept 19, 2015
 */
namespace Core;

/**
 * Language class to load the requested language file.
 */
class Language
{
    /**
     * Variable holds array with language.
     *
     * @var array
     */
    private $array;

    /**
     * Load language function.
     *
     * @param string $name
     * @param string $code
     */
    public function load($name, $code = LANGUAGE_CODE)
    {
        /* lang file */
        $file = SMVC."app/language/$code/$name.php";

        /* check if is readable */
        if (is_readable($file)) {
            /* require file */
            $this->array[$code] = include $file;
        } else {
            /* display error */
            echo Error::display("Could not load language file '$code/$name.php'");
            die;
        }
    }

    /**
     * Get element from language array by key or by key and language.
     *
     * @param string $value
     * @param string $code
     *
     * @return string
     */
    public function get($value, $code = LANGUAGE_CODE)
    {
        if (!empty($this->array[$code][$value])) {
            return $this->array[$code][$value];
        } elseif(!empty($this->array[LANGUAGE_CODE][$value])) {
            return $this->array[LANGUAGE_CODE][$value];
        } else {
            return $value;
        }
    }

    /**
     * Get lang for views.
     *
     * @param string $value this is "word" value from language file
     * @param string $name  name of file with language
     * @param string $code  optional, language code
     *
     * @return string
     */
    public static function show($value, $name, $code = LANGUAGE_CODE)
    {
        /* lang file */
        $file = SMVC."app/language/$code/$name.php";

        /* check if is readable */
        if (is_readable($file)) {
            /* require file */
            $array = include $file;
        } else {
            /* display error */
            echo Error::display("Could not load language file '$code/$name.php'");
            die;
        }

        if (!empty($array[$value])) {
            return $array[$value];
        } else {
            return $value;
        }
    }
}
