<?php

namespace Shared\Support;

use Nova\Support\Facades\Config;

use ReCaptcha\ReCaptcha as GoogleReCaptcha;


class ReCaptcha
{
    /**
     * Verify the given ReCaptcha response.
     *
     * @param  string   $response
     * @param  string   $remoteIp
     * @return boolean
     */
    public static function check($response, $remoteIp)
    {
        if (false === Config::get('reCaptcha.active', false)) {
            return true;
        }

        $secret = Config::get('reCaptcha.secret');

        $result = with(new GoogleReCaptcha($secret))->verify($response, $remoteIp);

        return $result->isSuccess();
    }
}
