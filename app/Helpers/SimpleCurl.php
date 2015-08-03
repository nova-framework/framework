<?php
namespace Helpers;

/*
* Curl class with some default functions and settings
*
* @author SecretD - https://github.com/SecretD
* @version 2.2
* @date Sept 22, 2014
* @date updated May 18 2015
*/
class SimpleCurl
{
  public static function get($url, $params = array(), $referer = null)
  {
      $url = $url . '?' . http_build_query($params, '', '&');
      $ch = curl_init();

      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_USERAGENT, SITETITLE);

      if($referer){
        curl_setopt($ch, CURLOPT_REFERER, $referer);
      }

      $response = curl_exec($ch);
      curl_close($ch);

      return $response;
  }

  /**
  * Performs a HTTP POST request with the chosen link more parameters in the array
  * @param string $url
  * @param array $params
  * @param string $referer
  * @return string returns the content of the given url after post
  */
  public static function post($url, $params = array(), $referer = null)
  {
      $ch = curl_init();

      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_USERAGENT, SITETITLE);

      if($referer){
        curl_setopt($ch, CURLOPT_REFERER, $referer);
      }

      $response = curl_exec($ch);
      curl_close($ch);

      return $response;
  }

  /**
  * Performs a HTTP PUT request with the chosen link more parameters in the array
  * @param string $url
  * @param array $params
  * @param string $referer
  * @return string with the contents of the site
  */
  public static function put($url, $params = array(), $referer = null)
  {
      $post_field_string = http_build_query($params);
      $ch = curl_init();

      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
      curl_setopt($ch, CURLOPT_POSTFIELDS, $post_field_string);

      curl_setopt($ch, CURLOPT_USERAGENT, SITETITLE);

      if($referer){
        curl_setopt($ch, CURLOPT_REFERER, $referer);
      }

      $response = curl_exec($ch);
      curl_close($ch);

      return $response;
  }


  /**
  * Performs a HTTP DELETE request with the chosen link more parameters in the array
  * @param string $url
  * @param array $params
  * @param string $referer
  * @return string with the contents of the site
  */
  public static function delete($url, $params = array(), $referer = null){

      $ch = curl_init($url);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
      curl_setopt($ch, CURLOPT_FAILONERROR, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          'Content-Type: application/json',
          'Content-Length: ' . strlen($data_string))
      );

      if($referer){
        curl_setopt($ch, CURLOPT_REFERER, $referer);
      }

      $response = curl_exec($ch);
      curl_close($ch);

      return $response;
  }
}
