<?php

//----------------------------------------------------------------------
// Custom Helpers
//----------------------------------------------------------------------



if (! function_exists('is_not_empty'))
{
    /**
     * String helper
     * @param string $value
     * @return bool
     */
    function is_not_empty($value)
    {
        return ! empty($value);
    }
}
