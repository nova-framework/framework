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


/**
 * Sample Themed Controller with its typical usage.
 */
class ObjectDumper
{
    public static function highlightText($text)
    {
        $text = trim($text);
        $text = highlight_string("<?php " . $text, true);  // highlight_string requires opening PHP tag or otherwise it will not colorize the text
        $text = trim($text);
        $text = preg_replace("|^\\<code\\>\\<span style\\=\"color\\: #[a-fA-F0-9]{0,6}\"\\>|", "", $text, 1);  // remove prefix
        $text = preg_replace("|\\</code\\>\$|", "", $text, 1);  // remove suffix 1
        $text = trim($text);  // remove line breaks
        $text = preg_replace("|\\</span\\>\$|", "", $text, 1);  // remove suffix 2
        $text = trim($text);  // remove line breaks
        $text = preg_replace("|^(\\<span style\\=\"color\\: #[a-fA-F0-9]{0,6}\"\\>)(&lt;\\?php&nbsp;)(.*?)(\\</span\\>)|", "\$1\$3\$4", $text);  // remove custom added "<?php "

        // Finall processing.
        $text = '<div style="font-weight: bold; margin-bottom: 10px;">'.$text.'</div>';

        return $text;
    }

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
