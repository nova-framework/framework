<?php
namespace Helpers;

class Times
{
    private $hou = 0;
    private $min = 0;
    private $sec = 0;
    private $totaltime = '00:00:00';

    public function __construct($times)
    {
        if (is_array($times)) {

            $length = sizeof($times);

            for ($x=0; $x <= $length; $x++) {
                $split = explode(":", $times[$x]);
                $this->hou += $split[0];
                $this->min += $split[1];
                $this->sec += $split[2];
            }

            $seconds = $this->sec % 60;
            $minutes = $this->sec / 60;
            $minutes = (integer)$minutes;
            $minutes += $this->min;
            $hours = $minutes / 60;
            $minutes = $minutes % 60;
            $hours = (integer)$hours;
            $hours += $this->hou % 24;

            $minutes = sprintf('%02d', $minutes);

            $this->totaltime = $hours.":".$minutes.":".$seconds;
        }
    }

    public function getTotalTime()
    {
        return $this->totaltime;
    }
}
