<?php
namespace Helpers;

/*
 * Request Class
 *
 * It contains the request information and provide methods to fetch request body
 */
class Request{

    /**
     * safer and better access to $_POST
     *
     * @param  string   $key
     * @static static method
     * @return mixed
     */
    public static function post($key){
        return array_key_exists($key, $_POST)? $_POST[$key]: null;
    }

    /**
     * safer and better access to $_FILES
     *
     * @param  string   $key
     * @static static method
     * @return mixed
     */
    public static function files($key){
        return array_key_exists($key, $_FILES)? $_FILES[$key]: null;
    }

    /**
     * safer and better access to $_GET
     *
     * @param  string   $key
     * @static static method
     * @return mixed
     */
    public static function query($key){
        return array_key_exists($key, $_GET)? $_GET[$key]: null;
    }

    /**
     * detect if request is Ajax
     *
     * @static static method
     * @return boolean
     */
    public static function isAjax(){
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH'])){
            return strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
        }
        return false;
    }

    /**
     * detect if request is POST request
     *
     * @static static method
     * @return boolean
     */
    public static function isPost(){
        return $_SERVER["REQUEST_METHOD"] === "POST";
    }

    /**
     * detect if request is GET request
     *
     * @static static method
     * @return boolean
     */
    public static function isGet(){
        return $_SERVER["REQUEST_METHOD"] === "GET";
    }

}
