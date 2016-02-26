<?php
/**
* Curl class.
*
* @author SecretD - https://github.com/SecretD
*
* @version 2.2
* @date Sept 22, 2014
* @date updated Sept 19, 2015
*/
namespace Helpers;

/**
 * Sets some default functions and settings.
 */
class SimpleCurl
{
    /**
     * Performs a get request on the chosen link and the chosen parameters
     * in the array.
     *
     * @param string $url
     * @param array  $params
     *
     * @return string returns the content of the given url
     */
    public static function get($url, $params = [])
    {
        if (is_array($params) && count($params) > 0) {
            $url = $url.'?'.http_build_query($params, '', '&');
        }
        $ch = curl_init();

        $options = [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => false,
        ];
        curl_setopt_array($ch, $options);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    /**
     * Performs a post request on the chosen link and the chosen parameters
     * in the array.
     *
     * @param string $url
     * @param array  $fields
     *
     * @return string returns the content of the given url after post
     */
    public static function post($url, $fields = [])
    {
        if (is_array($fields) && count($fields) > 0) {
            $postFieldsString = http_build_query($fields, '', '&');
        } else {
            $postFieldsString = '';
        }

        $ch = curl_init();

        $options = [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POSTFIELDS     => $postFieldsString,
            CURLOPT_POST           => true,
            CURLOPT_USERAGENT      => 'SMVC Agent',
        ];
        curl_setopt_array($ch, $options);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    /**
     * Performs a put request on the chosen link and the chosen parameters
     * in the array.
     *
     * @param string $url
     * @param array  $fields
     *
     * @return string with the contents of the site
     */
    public static function put($url, $fields = [])
    {
        if (is_array($fields) && count($fields) > 0) {
            $postFieldsString = http_build_query($fields, '', '&');
        } else {
            $postFieldsString = '';
        }
        $ch = curl_init($url);

        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_CUSTOMREQUEST  => 'PUT',
            CURLOPT_POSTFIELDS     => $postFieldsString,
            ];
        curl_setopt_array($ch, $options);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }
}
