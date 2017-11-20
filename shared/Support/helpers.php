<?php

//----------------------------------------------------------------------
// Custom Helpers
//----------------------------------------------------------------------



if (! function_exists('str_not_empty'))
{
    /**
     * String helper
     * @param string $value
     * @return bool
     */
    function str_not_empty($value)
    {
        return ! empty($value);
    }
}
