<?php
namespace Helpers;

/*
 * Date Helper - collection of methods for working with dates
 *
 * @author David Carr - dave@simplemvcframework.com
 * @version 1.0
 * @date May 18 2015
 */
class Date
{
    /**
     * get the difference between 2 dates
     * @param  date $from start date
     * @param  date $to   end date
     * @param  string $type the type of difference to return
     * @return string or array, if type is set then a string is returned otherwise an array is returned
     */
    public static function difference($from, $to, $type = null)
    {
        $d1 = new \DateTime($from);
        $d2 = new \DateTime($to);
        $diff = $d2->diff($d1);
        if ($type == null) {
            //return array
            return $diff;
        } else {
            return $diff->$type;
        }
    }
}
