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
        } else if($object === false) {
            return 'false'; // Boolean false.
        } else if(is_string($object)) {
            return $object;
        } else if($object instanceof BaseModel) {
            return $object->getDebugInfo();
        }

        //return var_export($object);
    }

    public static function dumpObjectArray($data)
    {
        $cnt = 0;

        // There we store the parsed output.
        $result = '';

        if(is_array($data)) {
            foreach($data as $object) {
                if($cnt > 0) {
                    $result .= "\n";
                } else {
                    $cnt++;
                }

                $result .= self::dumpObject($object);
            }
        } else {
            $result .= self::dumpObject($object);
        }

        return $result;
    }

}
