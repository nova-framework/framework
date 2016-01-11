<?php
/**
 * Expect
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date January 11th, 2016
 */

namespace Nova\ORM;


class Expects
{
    public static function toAssocArray($mixed)
    {
        // We expect to have 'key:value,value,value,key:value'
        $result = array();

        $rows = self::toArray($mixed);

        foreach ($rows as $existingkey => $row) {
            list($key, $value) = preg_split("/:/", $row);

            if (isset($value)) {
                $result[$key] = $value;
            } elseif (! is_numeric($existingkey)) {
                $result[$existingkey] = $key;
            } else {
                $result[$key] = $key;
            }
        }

        return $result;
    }

    public static function toArray($mixed)
    {
        // We expect to have 'row1,row2,row3'
        if (! is_array($mixed)) {
            return preg_split("/\s*,\s*/", $mixed);
        }

        return $mixed;
    }

    public static function toString($mixed)
    {
        if (is_array($mixed)) {
            return join(', ', $mixed);
        }

        if (is_object($mixed)) {
            throw new \Exception('Unexpected object, was expecting a String');
        }

        return $mixed;
    }

}
