<?php
/**
 * url Class
 *
 * @author David Carr - dave@daveismyname.com
 * @version 2.2
 * @date June 27, 2014
 * @date updated Sept 19, 2015
 */

namespace Helpers;

use Helpers\Session;

/**
 * Collection of methods for working with urls.
 */
class Url
{
    /**
     * Redirect to chosen url.
     *
     * @param  string  $url      the url to redirect to
     * @param  boolean $fullpath if true use only url in redirect instead of using DIR
     */
    public static function redirect($url = null, $fullpath = false)
    {
        if ($fullpath == false) {
            $url = DIR . $url;
        }

        header('Location: '.$url);
        exit;
    }

    /**
     * Created the absolute address to the template folder.
     *
     * @param  boolean $custom
     * @return string url to template folder
     */
    public static function templatePath($custom = false)
    {
        if ($custom == true) {
            return DIR.'app/templates/'.$custom.'/';
        } else {
            return DIR.'app/templates/'.TEMPLATE.'/';
        }
    }

    /**
     * Created the relative address to the template folder.
     *
     * @param  boolean $custom
     * @return string url to template folder
     */
    public static function relativeTemplatePath($custom = false)
    {
        if ($custom) {
            return "app/templates/".$custom."/";
        } else {
            return "app/templates/".TEMPLATE."/";
        }
    }

    /**
     * Converts plain text urls into HTML links, second argument will be
     * used as the url label <a href=''>$custom</a>.
     *
     *
     * @param  string $text   data containing the text to read
     * @param  string $custom if provided, this is used for the link label
     *
     * @return string         returns the data with links created around urls
     */
    public static function autoLink($text, $custom = null)
    {
        $regex   = '@(http)?(s)?(://)?(([-\w]+\.)+([^\s]+)+[^,.\s])@';

        if ($custom === null) {
            $replace = '<a href="http$2://$4">$1$2$3$4</a>';
        } else {
            $replace = '<a href="http$2://$4">'.$custom.'</a>';
        }

        return preg_replace($regex, $replace, $text);
    }

    /**
     * This function converts and url segment to an safe one, for example:
     * `test name @132` will be converted to `test-name--123`
     * Basicly it works by replacing every character that isn't an letter or an number to an dash sign
     * It will also return all letters in lowercase.
     *
     * @param $slug - The url slug to convert
     *
     * @return mixed|string
     */
    public static function generateSafeSlug($slug)
    {
        setlocale(LC_ALL, "en_US.utf8");

        $slug = preg_replace('/[`^~\'"]/', null, iconv('UTF-8', 'ASCII//TRANSLIT', $slug));

        $slug = htmlentities($slug, ENT_QUOTES, 'UTF-8');

        $pattern = '~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i';
        $slug = preg_replace($pattern, '$1', $slug);

        $slug = html_entity_decode($slug, ENT_QUOTES, 'UTF-8');

        $pattern = '~[^0-9a-z]+~i';
        $slug = preg_replace($pattern, '-', $slug);

        return strtolower(trim($slug, '-'));
    }

    /**
     * Go to the previous url.
     */
    public static function previous()
    {
        header('Location: '. $_SERVER['HTTP_REFERER']);
        exit;
    }

    /**
     * Get all url parts based on a / seperator.
     *
     * @return array of segments
     */
    public static function segments()
    {
        return explode('/', $_SERVER['REQUEST_URI']);
    }

    /**
     * Get item in array.
     *
     * @param  array $segments array
     * @param  int $id array index
     *
     * @return string - returns array index
     */
    public static function getSegment($segments, $id)
    {
        if (array_key_exists($id, $segments)) {
            return $segments[$id];
        }
    }

    /**
     * Get last item in array.
     *
     * @param  array $segments
     * @return string - last array segment
     */
    public static function lastSegment($segments)
    {
        return end($segments);
    }

    /**
     * Get first item in array
     *
     * @param  array segments
     * @return int - returns first first array index
     */
    public static function firstSegment($segments)
    {
        return $segments[0];
    }
}
