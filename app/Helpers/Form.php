<?php
/**
 * Form Helper
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
     * @param   array   $pair array(key=>value)
     *
     * @return string   single string of all attributes with value
     */
    private function getAttrValue($pair){
        $p = "";
        foreach($pair as $attribute => $value){
            $p .= !is_int($attribute)? $attribute .'="'.$value.'" ' : $value;
        }
        return $p;
    }


    /**
     * Open form
     *
     * @param   string  $method GET or POST
     * @param   array   $options attributes=>values
     *
     * @return  string <form method=....
     */
    public static function open($method="get",$options = array())
    {
        return '<form method="'.$method.'" ' . self::getAttrValue($options) . '>';
    }



    /**
     * Close form
     *
     * @return string </form>
     */
    public static function close()
    {
        return "</form>";
    }

    /**
     * Create text area
     *
     * This method creates a textarea element
     *
     * @param   array   $options attributes=>values
     * @param   string  $value default text
     *
     * @return  string  text area html element
     */
    public static function textBox($options = array(),$value='')
    {
        return '<textarea ' . self::getAttrValue($options) . '>' . $value . '</textarea>';
    }

    /**
     * Create Input
     *
     * This method returns a input element.
     *
     * @param   string  $type type of input, default set as text
     * @param   array   $options attributes=>values
     *
     * @return  string input html element
     */
    public static function input($type="text", $options = array())
    {
        return '<input type="' . $type . ' "' . self::getAttrValue($options) . '/>';
    }

    /**
     * Create Select
     *
     * This method returns a select element.
     *
     * @param   array   $options    attributes=>values or values
     * @param   array   $data   [value=>text,text,value=>['selected',text],['selected',text]] no reverse text with 'selected'
     *
     * @return  string  select html element
     */
    public static function select($options = array(), $data = array())
    {
        $o = '<select ' . self::getAttrValue($options) . '>';
        foreach ($data as $k => $v) {
            $o .= !is_int($k) ?
                "<option value='{$k}'".((is_array($v) && in_array('selected',$v)) ? 'selected' : '').">".
                ((is_array($v) && in_array('selected',$v)) ? $v[1] : $v)."</option>" :
                "<option value='". ((is_array($v) && in_array('selected',$v)) ? $v[1] : $v)."'" .
                    ((is_array($v) && in_array('selected',$v)) ? 'selected' : '').">".
                    ((is_array($v) && in_array('selected',$v)) ? $v[1] : $v)."</option>";
        }
        $o .= "</select>";
        return $o;
    }

    /**
     * checkboxMulti
     *
     * This method returns multiple checkbox elements in order given in an array
     * For checking of checkbox pass checked
     * Each checkbox should look like array(0=>array('id'=>'1', 'name'=>'cb[]', 'value'=>'x', 'label'=>'label_text' ))
     *
     * @param   array(array(id, name, value, class, checked, disabled))
     *
     * @return  string
     */
    public static function checkbox($params = array())
    {
        $o = '';
        if (!empty($params)) {
            $x = 0;
            foreach ($params as $k => $v) {
                $v['id'] = (isset($v['id']))        ? $v['id']                                          : "cb_id_{$x}_".rand(1000, 9999);
                $o .= "<input type='checkbox'";
                $o .= (isset($v['id']))             ? " id='{$v['id']}'"                                : '';
                $o .= (isset($v['name']))           ? " name='{$v['name']}'"                            : '';
                $o .= (isset($v['value']))          ? " value='{$v['value']}'"                          : '';
                $o .= (isset($v['class']))          ? " class='{$v['class']}'"                          : '';
                $o .= (isset($v['checked']))        ? " checked='checked'"                              : '';
                $o .= (isset($v['disabled']))       ? " disabled='{$v['disabled']}'"                    : '';
                $o .= (isset($params['style']))     ? " style='{$params['style']}'"                 : '';
                $o .= " />\n";
                $o .= (isset($v['label']))          ? "<label for='{$v['id']}'>{$v['label']}</label> "  : '';
                $x++;
            }
        }
        return $o;
    }

    /**
     * radioMulti
     *
     * This method returns radio elements in order given in an array
     * For selection pass checked
     * Each radio should look like array(0=>array('id'=>'1', 'name'=>'rd[]', 'value'=>'x', 'label'=>'label_text' ))
     *
     * @param   array(array(id, name, value, class, checked, disabled, label))
     *
     * @return  string
     */
    public static function radio($params = array())
    {
        $o = '';
        if (!empty($params)) {
            $x = 0;
            foreach ($params as $k => $v) {
                $v['id'] = (isset($v['id']))        ? $v['id']                                          : "rd_id_{$x}_".rand(1000, 9999);
                $o .= "<input type='radio'";
                $o .= (isset($v['id']))             ? " id='{$v['id']}'"                                : '';
                $o .= (isset($v['name']))           ? " name='{$v['name']}'"                            : '';
                $o .= (isset($v['value']))          ? " value='{$v['value']}'"                          : '';
                $o .= (isset($v['class']))          ? " class='{$v['class']}'"                          : '';
                $o .= (isset($v['checked']))        ? " checked='checked'"                              : '';
                $o .= (isset($v['disabled']))       ? " disabled='{$v['disabled']}'"                    : '';
                $o .= (isset($params['style']))     ? " style='{$params['style']}'"                 : '';
                $o .= " />\n";
                $o .= (isset($v['label']))          ? "<label for='{$v['id']}'>{$v['label']}</label> "  : '';
                $x++;
            }
        }
        return $o;
    }

    /**
     * This method returns a button element given the params for settings
     *
     * @param   array(id, name, class, onclick, value, disabled)
     *
     * @return  string
     */
    public static function button($params = array())
    {
        $o = "<button type='submit'";
        $o .= (isset($params['id']))        ? " id='{$params['id']}'"                           : '';
        $o .= (isset($params['name']))      ? " name='{$params['name']}'"                       : '';
        $o .= (isset($params['class']))     ? " class='{$params['class']}'"                     : '';
        $o .= (isset($params['onclick']))   ? " onclick='{$params['onclick']}'"                 : '';
        $o .= (isset($params['disabled']))  ? " disabled='{$params['disabled']}'"               : '';
        $o .= (isset($params['style']))     ? " style='{$params['style']}'"                 : '';
        $o .= ">";
        $o .= (isset($params['iclass']))    ? "<i class='fa {$params['iclass']}'></i> "         : '';
        $o .= (isset($params['value']))     ? "{$params['value']}"                              : '';
        $o .= "</button>\n";
        return $o;
    }


}
