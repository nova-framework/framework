<?php
/**
 * Form Helper
 * Totally Rewritten!
 *
 * @author David Carr - dave@daveismyname.com
 * @version 1.0
 * @date June 27, 2014
 * @date updated Sept 19, 2015
 */

namespace Helpers;

/**
 * Create form elements quickly.
 */
class Form
{
    /**
     * Sets value for attributes from key value pair
     * Uses value as key if key-value pair does not exist
     *
     * @param   array $pair array(key=>value)
     *
     * @return  string  attribute="value"
     */
    private static function getAttrValue($pair)
    {
        $p = "";
        foreach ($pair as $attribute => $value) {
            $p .= !is_int($attribute) ? $attribute . '="' . $value . '" ' : $value;
        }
        return $p;
    }


    /**
     * Open form
     * Use openGET or openPOST
     *
     * @param   string  $method GET or POST, default GET
     * @param   array   $options array(attributes=>values)
     *
     * @return  string  starts with open form tag
     */
    private static function open($method, $options = array())
    {
        return '<form method="' . $method . '" ' . self::getAttrValue($options) . '>';
    }

    /**
     * Open form with method set as get
     *
     * @param   array   $options    [attribute=>value]
     *
     * @return  string  start get from tag
     */
    public static function openGET($options = array())
    {
        return self::open("get",$options);
    }


    /**
     * Open form with method set as post
     *
     * @param   array   $options    [attribute=>value]
     *
     * @return  string  start post from tag
     */
    public static function openPOST($options = array())
    {
        return self::open("post",$options);
    }


    /**
     * Close form
     *
     * @return  string  ends the form tag
     */
    public static function close()
    {
        return "</form>";
    }


    /**
     * Create text area
     *
     * @param   array $options array(attributes=>values)
     * @param   string $value default text
     *
     * @return  string  text area html element
     */
    public static function textBox($options = array(), $value = '')
    {
        return '<textarea ' . self::getAttrValue($options) . '>' . $value . '</textarea>';
    }


    /**
     * Create Input
     * Use textInput, passwordInput, submitInput, buttonInput, numberInput, rangeInput
     *  -WARNING- BELOW inputs not supported by all browsers
     *  dateInput, datetimeInput, emailInput, monthInput, searchInput, telInput, timeInput, urlInput, weekInput
     *
     * @param   string  $type type of input
     * @param   array   $options [attribute=>value,(and/or value)]
     *
     * @return  string  input html element
     */
    private static function input($type, $options = array())
    {
        return '<input type="' . $type . '" ' . self::getAttrValue($options) . '/>';
    }

    /**
     * Create Text Input
     *
     * @param   array   $options [attribute=>value,(and/or value)]
     *
     * @return  string  text input html element
     */
    public static function textInput($options = array())
    {
        return self::input("text",$options);
    }

    /**
     * Create Password Input
     *
     * @param   array   $options [attribute=>value,(and/or value)]
     *
     * @return  string  password input html element
     */
    public static function passwordInput($options = array())
    {
        return self::input("password",$options);
    }

    /**
     * Create Submit Input
     *
     * @param   array   $options [attribute=>value,(and/or value)]
     *
     * @return  string  submit input html element
     */
    public static function submitInput($options = array())
    {
        return self::input("submit",$options);
    }

    /**
     * Create Button Input
     *
     * @param   array   $options [attribute=>value,(and/or value)]
     *
     * @return  string  button input html element
     */
    public static function buttonInput($options = array())
    {
        return self::input("button",$options);
    }

    /**
     * Create Number Input
     *
     * @param   array   $options [attribute=>value,(and/or value)]
     *
     * @return  string  number input html element
     */
    public static function numberInput($options = array())
    {
        return self::input("number",$options);
    }

    /**
     * Create Date Input
     * -WARNING- NOT supported in IE, FF
     *
     * @param   array   $options [attribute=>value,(and/or value)]
     *
     * @return  string  date input html element
     */
    public static function dateInput($options = array())
    {
        return self::input("date",$options);
    }

    /**
     * Create Color Input
     * -WARNING- NOT supported in IE, Safari
     *
     * @param   array   $options [attribute=>value,(and/or value)]
     *
     * @return  string  color input html element
     */
    public static function colorInput($options = array())
    {
        return self::input("color",$options);
    }

    /**
     * Create Range Input
     *
     * @param   array   $options [attribute=>value,(and/or value)]
     *
     * @return  string  range input html element
     */
    public static function rangeInput($options = array())
    {
        return self::input("range",$options);
    }

    /**
     * Create Month Input
     * -WARNING- NOT supported in IE, FF
     *
     * @param   array   $options [attribute=>value,(and/or value)]
     *
     * @return  string  month input html element
     */
    public static function monthInput($options = array())
    {
        return self::input("month",$options);
    }

    /**
     * Create Week Input
     * -WARNING- NOT supported in IE, FF
     *
     * @param   array   $options [attribute=>value,(and/or value)]
     *
     * @return  string  week input html element
     */
    public static function weekInput($options = array())
    {
        return self::input("week",$options);
    }

    /**
     * Create Time Input
     * -WARNING- NOT supported in IE, FF
     *
     * @param   array   $options [attribute=>value,(and/or value)]
     *
     * @return  string  time input html element
     */
    public static function timeInput($options = array())
    {
        return self::input("time",$options);
    }

    /**
     * Create Date Time Local Input
     * -WARNING- NOT supported in IE, FF
     *
     * @param   array   $options [attribute=>value,(and/or value)]
     *
     * @return  string  datetime-local input html element
     */
    public static function datetimeInput($options = array())
    {
        return self::input("datetime-local",$options);
    }

    /**
     * Create Email Input
     * -WARNING- NOT supported in Safari
     *
     * @param   array   $options [attribute=>value,(and/or value)]
     *
     * @return  string  email input html element
     */
    public static function emailInput($options = array())
    {
        return self::input("email",$options);
    }

    /**
     * Create Search Input
     * -WARNING- NOT supported in IE, FF, Opera
     *
     * @param   array   $options [attribute=>value,(and/or value)]
     *
     * @return  string  search input html element
     */
    public static function searchInput($options = array())
    {
        return self::input("search",$options);
    }

    /**
     * Create Telephone Input
     * -WARNING- NOT supported in IE, FF, Chrome, Opera
     *
     * @param   array   $options [attribute=>value,(and/or value)]
     *
     * @return  string  telephone input html element
     */
    public static function telInput($options = array())
    {
        return self::input("tel",$options);
    }

    /**
     * Create URL Input
     * -WARNING- NOT supported in Safari
     *
     * @param   array   $options [attribute=>value,(and/or value)]
     *
     * @return  string  url input html element
     */
    public static function urlInput($options = array())
    {
        return self::input("url",$options);
    }

    /**
     * Create Select
     * This currently does not support <optgroup> tag
     *
     * @param   array   $options    [attribute=>value,(and/or value)]
     * @param   array   $data       [value=>text,text,value=>['selected',text],['selected',text]] no reverse text with 'selected'
     *
     * @return  string  select html element
     */
    public static function select($options = array(), $data = array())
    {
        $o = '<select ' . self::getAttrValue($options) . '>';
        foreach ($data as $k => $v) {
            $o .= !is_int($k) ?
                "<option value='{$k}'" . ((is_array($v) && in_array('selected', $v)) ? 'selected' : '') . ">" .
                ((is_array($v) && in_array('selected', $v)) ? $v[1] : $v) . "</option>" :
                "<option value='" . ((is_array($v) && in_array('selected', $v)) ? $v[1] : $v) . "'" .
                ((is_array($v) && in_array('selected', $v)) ? 'selected' : '') . ">" .
                ((is_array($v) && in_array('selected', $v)) ? $v[1] : $v) . "</option>";
        }
        $o .= "</select>";
        return $o;
    }


    /**
     * Create many checkbox
     *
     * @param   array   $params [labelText=>[[labelAttribute=>value],[radioAttribute=>value]]]
     *
     * @return  string  multiple checkboxes html
     */
    public static function checkbox($params = array())
    {
        $o = '';

        foreach ($params as $k => $v) {
            $o .= "<label " . self::getAttrValue($v[0]) . ">" . self::input("checkbox", $v[1]) . (!is_int($k) ? $k : '') . "</label>";
        }

        return $o;
    }

    /**
     * Create many radio
     *
     * @param   array   $params [labelText=>[[labelAttribute=>value],[radioAttribute=>value]]]
     *
     * @return  string  multiple radio buttons html
     */
    public static function radio($params = array())
    {
        $o = '';

        foreach ($params as $k => $v) {
            $o .= "<label " . self::getAttrValue($v[0]) . ">" . self::input("radio", $v[1]) . (!is_int($k) ? $k : '') . "</label>";
        }

        return $o;
    }


    /**
     * Creates a button
     * use submitButton, resetButton, buttonButton
     *
     * @param   string  $type       type of button
     * @param   array   $params     ["value"=>value,"class"=>[tag,class]]
     * @param   array   $options    [attributes=>value]
     *
     * @return  string  button
     */
    private static function button($type, $params, $options)
    {
        $o = "<button type='{$type}'" . self::getAttrValue($options) . ">";
        foreach ($params as $k => $v) {
            $o .= (($k == "class") ? ("<" . $v . " class='{$v}'></" . $v . ">") : $v);
        }
        $o .= "</button>";
        return $o;
    }

    /**
     * Create a submit button
     *
     * @param   array   $params     ["value"=>value,"class"=>[tag,class]]
     * @param   array   $options    [attributes=>value]
     *
     * @return string   submit button html
     */
    public static function submitButton($params = array(), $options = array())
    {
        return self::button("submit", $params, $options);
    }

    /**
     * Create a reset button
     *
     * @param   array   $params     ["value"=>value,"class"=>[tag,class]]
     * @param   array   $options    [attributes=>value]
     *
     * @return string   reset button html
     */
    public static function resetButton($params = array(), $options = array())
    {
        return self::button("reset", $params, $options);
    }

    /**
     * Create a button button
     *
     * @param   array   $params     ["value"=>value,"class"=>[tag,class]]
     * @param   array   $options    [attributes=>value]
     *
     * @return string   button button html
     */
    public static function buttonButton($params = array(), $options = array())
    {
        return self::button("button", $params, $options);
    }
}
