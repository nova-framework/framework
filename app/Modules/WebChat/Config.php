<?php
/**
 * Config - the Module's specific Configuration.
 *
 * @author Virgil-Adrian Teaca - virgil@giulianaeassociati.com
 * @version 4.0
 */

Config::set('videoChat', array(
    /*
    |--------------------------------------------------------------------------
    | The Signaling Server used by SimpleWebRTC
    |--------------------------------------------------------------------------
    |
    | NOTE: You must use your own Signaling Server for production.
    */
    'url' => 'https://sandbox.simplewebrtc.com:443/',
));
