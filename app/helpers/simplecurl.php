<?php namespace helpers;

class simplecurl {
    
    /**
     * Performs a get request on the chosen link and the chosen parameters
     * in the array
     * @param string $url
     * @param array $params
     * @return string with the contents of the site
     */
    public static function get($url, $params=array()) {
        $url = $url.'?'.http_build_query($params, '', '&');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);    
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);        
        $response = curl_exec($ch);    
        curl_close($ch);    
        return $response;
    }
    
    /**
     * Performs a post request on the chosen link and the chosen parameters
     * in the array
     * @param string $url
     * @param array $fields
     * @return string with the contents of the site
     */
    function post($url, $fields=array())
    {
        $post_field_string = http_build_query($fields, '', '&');    
        $ch = curl_init();    
        curl_setopt($ch, CURLOPT_URL, $url);    
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);    
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);    
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_field_string);    
        curl_setopt($ch, CURLOPT_POST, true);    
        $response = curl_exec($ch);    
        curl_close ($ch);    
        return $response;
    }
    
    /**
     * Performs a post request on the chosen link and the chosen parameters
     * in the array
     * @param string $url
     * @param array $fields
     * @return string with the contents of the site
     */
    function put($url, $fields=array())
    {
        $post_field_string = http_build_query($fields, '', '&');    
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);    
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);    
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");    
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_field_string);    
        $response = curl_exec($ch);    
        curl_close ($ch);    
        return $response;
    }

}
