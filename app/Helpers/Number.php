<?php
/**
 * Number Class.
 *
 * @author David Carr - dave@daveismyname.com
 *
 * @version 2.2
 * @date May 18 2015
 * @date updated Sept 19, 2015
 */
namespace Helpers;

/**
 * Contains methods for converting number formats and getting a percentage.
 */
class Number
{
    /**
     * Formats a number to start with 0 useful for mobile numbers.
     *
     * @param numeric $number the number
     * @param numeric $prefix the number should start with
     *
     * @return string the formatted number
     */
    public static function format($number, $prefix = '4')
    {
        //remove any spaces in the number
        $number = str_replace(' ', '', $number);
        $number = trim($number);

        //make sure the number is actually a number
        if (is_numeric($number)) {
            //if number doesn't start with a 0 or a $prefix add a 0 to the start.
            if ($number[0] != 0 && $number[0] != $prefix) {
                $number = '0'.$number;
            }

            //if number starts with a 0 replace with $prefix
            if ($number[0] == 0) {
                $number[0] = str_replace('0', $prefix, $number[0]);
                $number = $prefix.$number;
            }

            //return the number
            return $number;

        //number is not a number
        } else {
            //return nothing
            return false;
        }
    }

    /**
     * Returns the percentage.
     *
     * @param numeric $val1 start number
     * @param numeric $val2 end number
     *
     * @return string returns the percentage
     */
    public static function percentage($val1, $val2)
    {
        if ($val1 > 0 && $val2 > 0) {
            $division = $val1 / $val2;
            $res = $division * 100;

            return round($res).'%';
        } else {
            return '0%';
        }
    }
}
