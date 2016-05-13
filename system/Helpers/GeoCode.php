<?php
/**
 * GeoCode Helper.
 *
 * @author Mark Parrish - mark@pipcommunications.com - http://www.pipcommunications.com
 * @version 3.0
 */
namespace Helpers;

/**
 * A collection of methods for working with Google's GeoCoder.
 */
class GeoCode
{
   /**
    * Helper class to house Google map api calls.
    *
    * This function connects to Google maps and retrieves the Latitude/Longitude of the address provided.
    * Usage: GeoCode::getLngLat(array($address, $city, $state, $zipcode));
    *
    * @param  array $options should contain up to 4 keys for steet, city, state and zipcode
    *
    * @return array array(lon, lat)
    */
    public static function getLngLat(array $options)
    {
        $url_base = 'http://maps.googleapis.com/maps/api/geocode/json?address=';
        $url_end = "&sensor=false";

        $address = array (
            'street' => $options[0],
            'city' => $options[1],
            'state' => $options[2],
            'zipcode' => $options[3]
            );
        $url = $url_base .
               urlencode($address['street']).','.
               urlencode($address['city']). ','.
               $address['state'].','.
               $address['zipcode'].
               $url_end;

        $result = json_decode(file_get_contents($url), true);

        if ($result['status'] == 'OK') {
            return array(
            'lon' => $result['results'][0]['geometry']['location']['lng'],
            'lat' => $result['results'][0]['geometry']['location']['lat'],
            );
        } else {
            return 'Failed to GeoCode the address.';
        }
    }
}
