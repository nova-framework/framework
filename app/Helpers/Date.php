<?php
/**
 * Date Helper
 *
 * @author David Carr - dave@daveismyname.com
 * @version 1.0
 * @date May 18 2015
 * @date updated Sept 19, 2015
 */

namespace Helpers;

/**
 * collection of methods for working with dates.
 */
class Date
{
    /**
     * get the difference between 2 dates
     *
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

    /**
     * Business Days
     *
     * Get number of working days between 2 dates
     *
     * Taken from http://mugurel.sumanariu.ro/php-2/php-how-to-calculate-number-of-work-days-between-2-dates/
     *
     * @param  date     $startDate date in the format of Y-m-d
     * @param  date     $endDate date in the format of Y-m-d
     * @param  booleen  $weekendDays returns the number of weekends
     * @return integer  returns the total number of days
     */
    public static function businessDays($startDate, $endDate, $weekendDays = false)
    {
        $begin = strtotime($startDate);
        $end = strtotime($endDate);

        if ($begin > $end) {
            //startDate is in the future
            return 0;
        } else {
            $numDays = 0;
            $weekends = 0;

            while ($begin <= $end) {
                $numDays++; // no of days in the given interval
                $whatDay = date('N', $begin);

                if ($whatDay > 5) { // 6 and 7 are weekend days
                    $weekends++;
                }
                $begin+=86400; // +1 day
            };

            if ($weekendDays == true) {
                return $weekends;
            }

            $working_days = $numDays - $weekends;
            return $working_days;
        }
    }

    /**
    * get an array of dates between 2 dates (not including weekends)
    *
    * @param  date    $startDate start date
    * @param  date    $endDate end date
    * @param  integer $nonWork day of week(int) where weekend begins - 5 = fri -> sun, 6 = sat -> sun, 7 = sunday
    * @return array   list of dates between $startDate and $endDate
    */
    public static function businessDates($startDate, $endDate, $nonWork = 6)
    {
        $begin    = new \DateTime($startDate);
        $end      = new \DateTime($endDate);
        $holiday  = array();
        $interval = new \DateInterval('P1D');
        $dateRange= new \DatePeriod($begin, $interval, $end);
        foreach ($dateRange as $date) {
            if ($date->format("N") < $nonWork and !in_array($date->format("Y-m-d"), $holiday)) {
                $dates[] = $date->format("Y-m-d");
            }
        }
        return $dates;
    }
}
