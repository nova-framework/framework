<?php
/**
 * Object Dumper
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 3.0
 * @date January 23th, 2016
 */

namespace App\Modules\Demo\Helpers;

use Nova\ORM\Model as BaseModel;


class ObjectDumper
{
    public static function dumpObject($object)
    {
        if($object === null) {
            return 'null'; // NULL.
        }
        else if($object === false) {
            return 'false'; // Boolean false.
        }
        else if(is_string($object)) {
            return $object;
        }
        else if($object instanceof BaseModel) {
            return (string) $object;
        }

        //return var_export($object);
    }

    public static function dumpObjectArray($data)
    {
        if($data === null) {
            return 'null'; // NULL.
        }
        else if($data === false) {
            return 'false'; // Empty string.
        }

        $result = '';

        $cnt = 0;

        foreach($data as $object) {
            if($cnt > 0) {
                $result .= "\n";
            }
            else {
                $cnt++;
            }

            $result .= (string) $object;
        }

        return $result;
    }

}
