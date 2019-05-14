<?php

use Shared\Support\Facades\Action;
use Shared\Support\Facades\Filter;

//----------------------------------------------------------------------
// Custom Helpers
//----------------------------------------------------------------------

if (! function_exists('human_size'))
{
    /**
     * Returns the human readable size
     *
     * @param  numeric $bytes size number
     * @param  numeric $decimals number of number
     * @return string  returns the human readable size
     */
    function human_size($bytes, $decimals = 2)
    {
        $size = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');

        $factor = floor((strlen($bytes) - 1) / 3);

        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
    }
}

if (! function_exists('sanitize'))
{
    function sanitize($data, $filter)
    {
        switch ($filter) {
            case 'string':
                return filter_var($data, FILTER_SANITIZE_STRING);

            case 'email':
                return filter_var($data, FILTER_SANITIZE_EMAIL);

            case 'integer':
                return filter_var($data, FILTER_SANITIZE_NUMBER_INT);

            case 'float':
                return filter_var($data, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION | FILTER_FLAG_ALLOW_THOUSAND);

            case 'url':
                return filter_var($data, FILTER_SANITIZE_URL);
        }

        throw new InvalidArgumentException('Filter sanitize unknown.');
    }
}
